<?php


namespace App\DocumentRepository;

use App\Document\AdrecordProduct;
use App\QueueModel\ResourceProductQueues;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class AdrecordProductRepository
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 * @method array getDataTableAggr ($collection, array $params)
 * @method string|null matchExistProductAllow (string $collection, array $match)
 */
class AdrecordProductRepository extends ServiceDocumentRepository implements CarefulSavingSku, DirectlyRemove
{
    use CommonTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdrecordProduct::class);
    }

    /**
     * @param ResourceProductQueues $productQueues
     * @return mixed|string|null
     */
    public function matchExistProduct(ResourceProductQueues $productQueues)
    {
        return  $this->matchExistProductAllow('AdrecordProduct', [
            'identityUniqData' => $productQueues->getAttributeByName('identityUniqData'),
        ]);
    }

    /**
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getCountDoc()
    {
        return $this->getCount($this->createQueryBuilder());
    }
}