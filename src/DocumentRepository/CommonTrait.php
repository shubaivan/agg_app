<?php


namespace App\DocumentRepository;

use Doctrine\ODM\MongoDB\Query\Builder;

trait CommonTrait
{
    /**
     * @param Builder $builder
     * @return array|\Doctrine\ODM\MongoDB\Iterator\Iterator|int|\MongoDB\DeleteResult|\MongoDB\InsertOneResult|\MongoDB\UpdateResult|object|null
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getCount(Builder $builder)
    {
        return $builder->count()->getQuery()->execute();
    }
}