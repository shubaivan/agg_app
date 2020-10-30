<?php


namespace App\DocumentRepository;


use App\Document\AwinProduct;
use App\Document\TradeDoublerProduct;
use App\QueueModel\ResourceProductQueues;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class TradeDoublerProductRepository
 * @package App\DocumentRepository
 *
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 * @method array getDataTableAggr (string $collection, array $params)
 * @method string|null matchExistProductAllow (string $collection, array $match)
 * @method string|null removeByShop(string $collection,string $shop)
 */
class TradeDoublerProductRepository extends ServiceDocumentRepository implements CarefulSavingSku, DirectlyRemove
{
    use CommonTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeDoublerProduct::class);
    }

    /**
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getCountDoc()
    {
        return $this->getCount($this->createQueryBuilder());
    }

    public function getListQuery()
    {
        $builder = $this->createQueryBuilder();
        return $builder->getQuery();
    }

    public function nativeFindOne()
    {
        $client = $this->getDocumentManager()->getClient();
        $collection = $client->symfony->TradeDoublerProduct;
        $cursor = $collection->findOne();

        return $cursor;
    }

    /**
     * @param ResourceProductQueues $productQueues
     * @return mixed|string|null
     */
    public function matchExistProduct(ResourceProductQueues $productQueues)
    {
        return  $this->matchExistProductAllow('TradeDoublerProduct', [
            'identityUniqData' => $productQueues->getAttributeByName('identityUniqData'),
        ]);
    }
}