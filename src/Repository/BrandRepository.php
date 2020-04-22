<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheBrand;
use App\Entity\Brand;
use App\Services\Helpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Cache\Cache as ResultCacheDriver;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Cache\ResultCacheStatement;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @method Brand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Brand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Brand[]    findAll()
 * @method Brand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Brand[]|int getList(ResultCacheDriver $cache, QueryBuilder $qb, ParamFetcher $paramFetcher, bool $count = false)
 */
class BrandRepository extends ServiceEntityRepository
{
    use PaginationRepository;

    /**
     * @var Helpers
     */
    private $helpers;

    /**
     * @var TagAwareQueryResultCacheBrand
     */
    private $tagAwareQueryResultCacheBrand;

    /**
     * BrandRepository constructor.
     * @param ManagerRegistry $registry
     * @param Helpers $helpers
     * @param TagAwareQueryResultCacheBrand $tagAwareQueryResultCacheBrand
     */
    public function __construct(
        ManagerRegistry $registry,
        Helpers $helpers,
        TagAwareQueryResultCacheBrand $tagAwareQueryResultCacheBrand
    )
    {
        parent::__construct($registry, Brand::class);

        $this->helpers = $helpers;
        $this->tagAwareQueryResultCacheBrand = $tagAwareQueryResultCacheBrand;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Brand[]|int
     */
    public function getEntityList(
        ParamFetcher $paramFetcher,
        $count = false)
    {
        return $this->getList(
            $this->getEntityManager()->getConfiguration()->getResultCacheImpl(),
            $this->createQueryBuilder('s'),
            $paramFetcher,
            $count
        );
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function fullTextSearchByParameterFetcher(ParamFetcher $paramFetcher, $count = false)
    {
        $parameterBag = new ParameterBag($paramFetcher->all());

        return $this->fullTextSearchByParameterBag($parameterBag, $count);
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool $count
     * @return int|mixed[]
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function fullTextSearchByParameterBag(ParameterBag $parameterBag, $count = false)
    {
        $sort_by = isset($_REQUEST['sort_by']);
        $connection = $this->getEntityManager()->getConnection();
        $limit = $parameterBag->get('count');
        $offset = $limit * ($parameterBag->get('page') - 1);
        $sortBy = $parameterBag->get('sort_by');
        $sortOrder = $parameterBag->get('sort_order');

        $sortBy = $this->getHelpers()->white_list($sortBy,
            ["id", "name", "createdAt"], "Invalid field name " . $sortBy);
        $sortOrder = $this->getHelpers()
            ->white_list(
                $sortOrder,
                [Criteria::DESC, Criteria::ASC],
                "Invalid ORDER BY direction " . $sortOrder
            );

        $searchField = $parameterBag->get('search');
        if ($searchField) {
            $search = $this->getHelpers()
                ->handleSearchValue($searchField, $parameterBag->get('strict') !== true);
        } else {
            $search = $searchField;
        }
        $query = '';

        if ($count) {
            $query .= '
                        SELECT COUNT(DISTINCT brand_alias.id)
                    ';
        } else {
            $query .= '
                    SELECT                         
                            brand_alias.id,
                            brand_alias.name AS "name",
                            brand_alias.created_at AS "createdAt"
            ';

            if ($search) {
                $query .= '
                    ,ts_rank_cd(to_tsvector(\'english\',coalesce(name,\'\')||\' \'), query_search) AS rank
            ';
            }
        }

        $query .= '
                FROM brand brand_alias 
        ';
        if ($search) {
            $query .= '
                JOIN to_tsquery(:search) query_search
                ON to_tsvector(\'english\',coalesce(name,\'\')||\' \') @@ query_search
            ';
        }

        if (!$count) {
            $query .= '
                    GROUP BY id';
            if ($search) {
                $query .= ', query_search.query_search';
            }

            $query .=
                ($search ?
                    ($sort_by
                        ? ' ORDER BY rank DESC, ' . '"' . $sortBy . '"' . ' ' . $sortOrder . ''
                        : ' ORDER BY rank DESC')
                    : ' ORDER BY ' . '"' . $sortBy . '"' . ' ' . $sortOrder . '') . '
                                          
                    LIMIT :limit
                    OFFSET :offset;
            ';
        }

        $params = [];
        $types = [];

        if ($search) {
            $params[':search'] = $search;
            $types[':search'] = \PDO::PARAM_STR;
        }

        if (!$count) {
            $params[':offset'] = $offset;
            $params[':limit'] = $limit;
            $types[':offset'] = \PDO::PARAM_INT;
            $types[':limit'] = \PDO::PARAM_INT;
        }

        $this->getTagAwareQueryResultCacheBrand()->setQueryCacheTags(
            $query,
            $params,
            $types,
            ['brand_full_text_search'],
            0, $count ? "brand_search_cont" : "brand_search_collection"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheBrand()
            ->prepareParamsForExecuteCacheQuery();

        /** @var ResultCacheStatement $statement */
        $statement = $connection->executeCacheQuery(
            $query,
            $params,
            $types,
            $queryCacheProfile
        );

        if ($count) {
            $shops = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $shops = isset($shops[0]['count']) ? (int)$shops[0]['count'] : 0;
        } else {
            $shops = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        $statement->closeCursor();

        return $shops;
    }

    public function getAvailableBrandByProductSearchQuery()
    {
//        SELECT
//        brand_alias.id,
//        brand_alias.name
//
//        FROM brand brand_alias
//        INNER JOIN products products_alias ON products_alias.brand_relation_id = brand_alias.id
//
//        JOIN to_tsquery('ball:*') query_search
//        ON to_tsvector('english',products_alias.name||' '||coalesce(description,'')||' '||coalesce(sku,'')||' '||coalesce(price,0)||' '||coalesce(category,'')||' '||coalesce(brand,'')||' '||coalesce(shop,'')) @@ query_search
//
//        LEFT JOIN product_category cp on cp.product_id = products_alias.id
//        LEFT JOIN product_category cpt on cpt.product_id = products_alias.id
//
//        GROUP BY brand_alias.id, brand_alias.name
//
//        LIMIT 3
//        OFFSET 0;
    }

    /**
     * @return Helpers
     */
    private function getHelpers(): Helpers
    {
        return $this->helpers;
    }

    /**
     * @return TagAwareQueryResultCacheBrand
     */
    private function getTagAwareQueryResultCacheBrand(): TagAwareQueryResultCacheBrand
    {
        return $this->tagAwareQueryResultCacheBrand;
    }
}
