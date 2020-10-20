<?php


namespace App\DocumentRepository;

use App\Document\AwinProduct;
use App\QueueModel\ResourceProductQueues;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class AwinProductRepository
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 * @method array getDataTableAggr ($collection, array $params)
 * @method string|null matchExistProductAllow (string $collection, array $match)
 */
class AwinProductRepository extends ServiceDocumentRepository implements CarefulSavingSku, DirectlyRemove
{
    use CommonTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AwinProduct::class);
    }

    /**
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getCountDoc()
    {
        return $this->getCount($this->createQueryBuilder());
    }

    /**
     * @param ResourceProductQueues $productQueues
     * @return mixed|string|null
     */
    public function matchExistProduct(ResourceProductQueues $productQueues)
    {
        return  $this->matchExistProductAllow('AwinProduct', [
            'identityUniqData' => $productQueues->getAttributeByName('identityUniqData'),
        ]);
    }

    public function dictDeclineError(string $collectionName)
    {
//        db.getCollection('AwinProduct').distinct("declineReasonClass",
// { declineReasonClass: { $ne: '' } })
        $client = $this->getDocumentManager()->getClient();
        $collection = $client->symfony->$collectionName;

        return $collection->distinct(
            'declineReasonClass',
            [
                "declineReasonClass" => ['$ne' => '']
            ]
        );
    }
}