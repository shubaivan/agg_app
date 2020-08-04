<?php


namespace App\DocumentRepository;

use App\Document\AdtractionProduct;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class AdtractionProductRepository
 * @package App\DocumentRepository
 * @method int getCount(Builder $builder)
 */
class AdtractionProductRepository extends ServiceDocumentRepository
{
    use CommonTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdtractionProduct::class);
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