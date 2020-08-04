<?php


namespace App\DocumentRepository;

use App\Document\AdrecordProduct;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class AdrecordProductRepository
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 */
class AdrecordProductRepository extends ServiceDocumentRepository
{
    use CommonTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdrecordProduct::class);
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