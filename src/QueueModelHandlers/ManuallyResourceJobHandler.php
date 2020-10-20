<?php

namespace App\QueueModelHandlers;

use App\Cache\CacheManager;
use App\DocumentRepository\DirectlyRemove;
use App\Entity\ManuallyResourceJob;
use App\Entity\Product;
use App\Entity\Shop;
use App\Exception\ValidatorException;
use App\QueueModel\AdrecordDataRow;
use App\QueueModel\ManuallyResourceJobs;
use App\QueueModel\ResourceProductQueues;
use App\QueueModel\VacuumJob;
use App\Services\Admin\ResourceShopManagement;
use App\Services\HandleDownloadFileData;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ManuallyResourceJobHandler implements MessageHandlerInterface
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ResourceShopManagement
     */
    private $resourceShopManagement;

    /**
     * @var HandleDownloadFileData
     */
    private $handleDownloadFileData;

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * ManuallyResourceJobHandler constructor.
     * @param EntityManagerInterface $em
     * @param ResourceShopManagement $resourceShopManagement
     * @param CacheManager $cacheManager
     * @param HandleDownloadFileData $handleDownloadFileData
     * @param DocumentManager $dm
     */
    public function __construct(
        EntityManagerInterface $em,
        ResourceShopManagement $resourceShopManagement,
        CacheManager $cacheManager,
        HandleDownloadFileData $handleDownloadFileData,
        DocumentManager $dm
    )
    {
        $this->em = $em;
        $this->resourceShopManagement = $resourceShopManagement;
        $this->cacheManager = $cacheManager;
        $this->handleDownloadFileData = $handleDownloadFileData;
        $this->dm = $dm;
    }


    /**
     * @param AdrecordDataRow $adrecordDataRow
     * @throws ValidatorException
     * @throws \Throwable
     */
    public function __invoke(ManuallyResourceJobs $job)
    {
        try {
            $id = $job->getJob();
            $manuallyResourceJob = $this->em->getRepository(ManuallyResourceJob::class)
                ->findOneBy(['id' => $id]);
            if (!$manuallyResourceJob) {
                throw new \Exception('job with id: ' . $id . ' was not found');
            }
            $status = $manuallyResourceJob->getStatus();
            if ($status !== ManuallyResourceJob::STATUS_CREATED) {
                throw new \Exception('wrong status: ' . $status);
            }
            $manuallyResourceJob
                ->setStatus(ManuallyResourceJob::STATUS_IN_PROGRESS);

            $removeProductsByShop = $this->em->getRepository(Product::class)
                ->removeProductsByShop(
                    Shop::getMapShopKeyByOriginalName($manuallyResourceJob->getShopKey())
                );

            $modelClass = $this->handleDownloadFileData->identityModelClass($manuallyResourceJob->getShopKey());
            if ($modelClass) {
                /** @var $modelClass ResourceProductQueues */
                /** @var DirectlyRemove $removeGate */
                $removeGate = $this->dm->getRepository($modelClass::getMongoClass());
                $removeGate->removeByShop(
                    $modelClass::getMongoClass(),
                    Shop::getRealShopNameByKey($manuallyResourceJob->getShopKey())
                );
            }

            $this->getCacheManager()->clearAllPoolsCache();

            $this->resourceShopManagement
                ->guzzleStreamWay(
                    $manuallyResourceJob->getShopKey(),
                    $manuallyResourceJob->getUrl(),
                    $manuallyResourceJob->getDirForFiles(),
                    $manuallyResourceJob->getRedisUniqKey()
                );

            $this->em->flush();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * @return CacheManager
     */
    protected function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }
}
