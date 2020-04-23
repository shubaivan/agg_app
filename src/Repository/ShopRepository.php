<?php

namespace App\Repository;

use App\Cache\TagAwareQueryResultCacheShop;
use App\Entity\Shop;
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
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Shop[]|int getList(ResultCacheDriver $cache, QueryBuilder $qb, ParamFetcher $paramFetcher, bool $count = false)
 */
class ShopRepository extends ServiceEntityRepository
{
    use PaginationRepository;

    /**
     * @var Helpers
     */
    private $helpers;

    /**
     * @var TagAwareQueryResultCacheShop
     */
    private $tagAwareQueryResultCacheShop;

    /**
     * ShopRepository constructor.
     * @param ManagerRegistry $registry
     * @param Helpers $helpers
     * @param TagAwareQueryResultCacheShop $tagAwareQueryResultCacheShop
     */
    public function __construct(
        ManagerRegistry $registry,
        Helpers $helpers,
        TagAwareQueryResultCacheShop $tagAwareQueryResultCacheShop
    )
    {
        parent::__construct($registry, Shop::class);

        $this->helpers = $helpers;
        $this->tagAwareQueryResultCacheShop = $tagAwareQueryResultCacheShop;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool $count
     * @return Shop[]|int
     */
    public function getEntityList(
        ParamFetcher $paramFetcher,
        $count = false)
    {
        $qb = $this->createQueryBuilder('s');
        return $this->getList(
            $this->getEntityManager()->getConfiguration()->getResultCacheImpl(),
            $qb,
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
                ->handleSearchValue($searchField, $parameterBag->get('strict') === true);
        } else {
            $search = $searchField;
        }
        $query = '';

        if ($count) {
            $query .= '
                        SELECT COUNT(DISTINCT shop_alias.id)
                    ';
        } else {
            $query .= '
                    SELECT                         
                            DISTINCT shop_alias.id,
                            shop_alias.name AS "name",
                            shop_alias.created_at AS "createdAt"
            ';

            if ($search) {
                $query .= '
                    ,ts_rank_cd(to_tsvector(\'english\',coalesce(name,\'\')||\' \'), query_search) AS rank
            ';
            }
        }

        $query .= '
                FROM shop shop_alias 
        ';
        if ($search) {
            $query .= '
                JOIN to_tsquery(\'simple\', :search) query_search
                ON to_tsvector(\'english\',coalesce(name,\'\')||\' \') @@ query_search
            ';
        }

        if (!$count) {
            $query .= '
                    GROUP BY shop_alias.id';
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

        $this->getTagAwareQueryResultCacheShop()->setQueryCacheTags(
            $query,
            $params,
            $types,
            ['shop_full_text_search'],
            0, $count ? "shop_search_cont" : "shop_search_collection"
        );
        [$query, $params, $types, $queryCacheProfile] = $this->getTagAwareQueryResultCacheShop()
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

    /**
     * @return Helpers
     */
    private function getHelpers(): Helpers
    {
        return $this->helpers;
    }

    /**
     * @return TagAwareQueryResultCacheShop
     */
    private function getTagAwareQueryResultCacheShop(): TagAwareQueryResultCacheShop
    {
        return $this->tagAwareQueryResultCacheShop;
    }
}
