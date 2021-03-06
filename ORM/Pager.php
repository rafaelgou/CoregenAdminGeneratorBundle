<?php
namespace Coregen\AdminGeneratorBundle\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Coregen\AdminGeneratorBundle\Generator\Generator;
use Symfony\Bundle\DoctrineBundle\Registry;

/**
 * ORM Generator Pager
 *
 * @package    CoregenAdminGenerator
 * @subpackage ORM
 * @author     Rafael Goulart <rafaelgou@gmail.com>
 */
class Pager
{
    /**
     * @var Symfony\Bundle\DoctrineBundle\Registry
     */
    protected $doctrineRegistry = null;

    /**
     * @var Coregen\AdminGeneratorBundle\Generator\Generator
     */
    protected $generator = null;

    /**
     * @var Doctrine\ORM\QueryBuilder
     */
    protected $queryBuilder = null;

    /**
     * @var int
     */
    protected $currentPage = null;

    /**
     * @var int
     */
    protected $nextPage = null;

    /**
     * @var int
     */
    protected $previousPage = null;

    /**
     * @var int
     */
    protected $lastPage = null;

    /**
     * @var int
     */
    protected $maxPagesToList = 5;

    /**
     * @var boolean
     */
    protected $haveToPaginate = false;

    /**
     * @var int
     */
    protected $limit = 20;

    /**
     * @var array
     */
    protected $sort = array();

    /**
     * @var array
     */
    protected $query = array();

    /**
     * @var int
     */
    protected $count = null;

    /**
     * Constructor
     *
     * @param Symfony\Bundle\DoctrineBundle\Registry           $doctrineRegistry The Doctrine Registry
     * @param Coregen\AdminGeneratorBundle\Generator\Generator $generator        The Coregen Generator
     *
     * @return void
     */
    public function __construct(Registry $doctrineRegistry, Generator $generator=null)
    {
        $this->doctrineRegistry = $doctrineRegistry;

        if (null !== $generator) {
            $this->generator  = $generator;
        }

    }

    /**
     * Get the generator
     *
     * @return Coregen\AdminGeneratorBundle\Generator\Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Define the generator
     *
     * @param Coregen\AdminGeneratorBundle\Generator\Generator $generator The Coregen Generator
     *
     * @return void
     */
    public function setGenerator(Generator $generator)
    {
        $this->generator  = $generator;
        return $this;
    }

    /**
     * Return de Repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        if (null === $this->generator) {
            throw new \Exception('Can\'t instatiate a Doctrine Repository without Generator Class');
        } else {
            return $this->doctrineRegistry->getRepository($this->generator->model);
        }
    }

    /**
     * Return de EntityManager
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getManager()
    {
        return $this->doctrineRegistry->getEntityManager();
    }

    /**
     * Executes the query
     *
     * @return mixed
     */
    public function execute()
    {

        $count = $this->count();

        $this->lastPage = (floor($count / $this->limit) == $count / $this->limit)
                            ? $count / $this->limit
                            : floor($count / $this->limit) + 1;
        $this->nextPage = ($this->currentPage == $this->lastPage)
                            ? false
                            : $this->currentPage + 1;
        $this->previousPage = ($this->currentPage == 1)
                            ? false
                            : $this->currentPage - 1;

        return $this->getQueryBuilder()
                ->setFirstResult(($this->currentPage -1 )* $this->limit)
                ->setMaxResults($this->limit)
                ->getQuery()
                ->getResult();
    }

    /**
     * Executes the query ( alias to execute() )
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->execute();
    }

    /**
     * Define the Query Builder
     *
     * @param Doctrine\ODM\MongoDB\Query\Builder $queryBuilder A query builder instance
     */

    /**
     * Set Query Buildr
     *
     * @param Doctrine\ODM\MongoDB\Query\Builder $queryBuilder        A query builder instance
     * @param boolean                            $useGeneratorSort    Use default generator.list.sort, false to use QueryBuilder's instead
     * @param boolean                            $useGeneratorFilters Use default generator.filter + ::setQuery(), default to false
     *
     * @return Pager
     */
    public function setQueryBuilder($queryBuilder, $useGeneratorSort=true, $useGeneratorFilters=false)
    {
        $this->queryBuilder = $queryBuilder;
        if ($useGeneratorSort) {
            foreach ($this->sort as $field => $order) {
                $this->queryBuilder->addOrderBy('e.' . $field, $order);
            }
        }
        if ($useGeneratorFilters) {
            $this->processFilters();
        }
        return $this;
    }

    /**
     * Return the Query Builder
     *
     * @return Doctrine\ODM\MongoDB\Query\Builder
     */
    public function getQueryBuilder()
    {
        if (null === $this->queryBuilder) {

            $queryBuilder = $this->generator->list->query_builder;

            if ( null !== $this->generator->list->query_builder
                 && false !== $this->generator->list->query_builder
                 && method_exists($this->getRepository(), $queryBuilder)) {
                    $this->queryBuilder = $this->getRepository()->$queryBuilder();
            } else {
                $this->queryBuilder =  $this->getRepository()->createQueryBuilder('e');
            }

            foreach ($this->sort as $field => $order) {
                $this->queryBuilder->addOrderBy('e.' . $field, $order);
            }

            $this->processFilters();

        }
        return $this->queryBuilder;
    }

    /**
     * Process filters
     *
     * @return void
     */
    protected function processFilters()
    {
        if ($this->generator->filter->fields && is_array($this->generator->filter->fields)) {
            $counter = 0;
            foreach ($this->generator->filter->fields as $fieldName => $field) {
                if (isset($field['type'])) {
                    switch($field['type']) {

                        case 'daterange':

                            $dateFormaterEn = \IntlDateFormatter::create('en', \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
                            if (isset($this->query[$fieldName .'_from']) && $this->query[$fieldName .'_from'] != '') {
                                $counter++;
                                $this->queryBuilder->andWhere("e.{$fieldName} >= ?{$counter}");
                                $dateFrom = date('Y-m-d', $dateFormaterEn->parse($this->query[$fieldName .'_from'])) . ' 00:00:00';
                                $this->queryBuilder->setParameter($counter, $dateFrom);
                            }
                            if (isset($this->query[$fieldName .'_to']) && $this->query[$fieldName .'_to'] != '') {
                                $counter++;
                                $this->queryBuilder->andWhere("e.{$fieldName} <= ?{$counter}");
                                $dateTo = date('Y-m-d', $dateFormaterEn->parse($this->query[$fieldName .'_to'])) . ' 23:59:59';
                                $this->queryBuilder->setParameter($counter, $dateTo);
                            }
                            break;

                        case 'entity':
                            if (isset($this->query[$fieldName]) && $this->query[$fieldName] != '') {
                                try {
                                    $entity = $this->doctrineRegistry->getEntityManager()
                                            ->getRepository($field['options']['class'])
                                            ->find($this->query[$fieldName]);
                                } catch (Exception $exc) {
                                    $entity =  false;
                                }

                                if ($entity) {
                                    $counter++;
                                    $compare = $this->getCompare(isset($field['compare']) ? $field['compare'] : null);
                                    $this->queryBuilder->andWhere(
                                        $this->queryBuilder->expr()->$compare("e.{$fieldName} ", "?{$counter}")
                                    );
                                    $this->queryBuilder->setParameter($counter, $entity);
                                }

                            }
                            break;

                        case 'checkbox':
                            if (isset($this->query[$fieldName]) && $this->query[$fieldName] == true) {
                                $counter++;
                                $this->queryBuilder->andWhere(
                                    $this->queryBuilder->expr()->eq("e.{$fieldName} ", "?{$counter}")
                                );
                                $this->queryBuilder->setParameter($counter, true);
                            }
                            break;

                        case 'text':
                        default:
                            if (isset($this->query[$fieldName])) {
                                $counter++;
                                $compare = $this->getCompare(isset($field['compare']) ? $field['compare'] : null);
                                $this->queryBuilder->andWhere(
                                    $this->queryBuilder->expr()->$compare("e.{$fieldName} ", "?{$counter}")
                                );
                                if ($compare == 'like') {
                                    $this->query[$fieldName] = "%{$this->query[$fieldName]}%";
                                }
                                $this->queryBuilder->setParameter($counter, $this->query[$fieldName]);
                            }
                            break;
                    }
                }
            }
        }
    }


    /**
     * Sets the query limit
     *
     * @param integer $limit The Limit
     *
     * @return DynamicPager
     */
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    /**
     * Sets the query page
     *
     * @param int $page The page number
     *
     * @return DynamicPager
     */
    public function setCurrent($page)
    {
        $this->currentPage = (int) $page;
        return $this;
    }

    /**
     * Sets the max pages to list
     * puts the current page in always in the middle
     *
     * @param integer $maxPagesToList The max pages to list
     *
     * @return DynamicPager
     */
    public function setMaxPagesToList($maxPagesToList)
    {
        $this->maxPagesToList = (int) $maxPagesToList;
        return $this;
    }

    /**
     * Sets the query
     *
     * @param array $query The query
     *
     * @return DynamicPager
     */
    public function setQuery($query=array())
    {
        $this->query = is_array($query) ? $query : array();
        return $this;
    }

    /**
     * Sets the query sort
     *
     * @param array $sort The sort
     *
     * @return  Pager
     */
    public function setSort($sort=array())
    {
        $this->sort = is_array($sort) ? $sort : array();
        return $this;
    }

    /**
     * Return the documents count
     *
     * @return integer
     */
    public function getCount()
    {
        if (null === $this->count) {
            $qb = clone $this->getQueryBuilder();
            $query = $qb->addSelect('count(e.id) as total_count');
            $result = $query->getQuery()->execute();
            $this->count = $result[0]['total_count'];
        }
        return $this->count;
    }

    /**
     * Return the documents count
     *
     * @return integer
     */
    public function count()
    {
        return $this->getCount();
    }

    /**
     * Return actual page
     *
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Return next page
     *
     * @return integer
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }

    /**
     * Return previous page
     *
     * @return integer
     */
    public function getPreviousPage()
    {
        return $this->previousPage;
    }

    /**
     * Return last page
     *
     * @return integer
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * Check if it is on the first page
     *
     * @return boolean
     */
    public function isFirstPage()
    {
        return ($this->currentPage == 1) ? true : false;
    }

    /**
     * Check if it is on the last page
     *
     * @return boolean
     */
    public function isLastPage()
    {
        return ($this->currentPage == $this->lastPage) ? true : false;
    }

    /**
     * Check if has pages enough to paginate (more than limit)
     *
     * @return boolean
     */
    public function haveToPaginate()
    {
        return ($this->count() > $this->limit) ? true : false;
    }

    /**
     * Return the page range to paginate
     *
     * @return array
     */
    public function rangeToPaginate()
    {
        $pages = array();

        if ( $this->lastPage <= $this->maxPagesToList) {
            for ($i = 1; $i <= $this->lastPage; $i++) {
                $pages[] = $i;
            }
        } else if ($this->currentPage <= floor($this->maxPagesToList/2)+1) {
            for ($i = 1; $i <= $this->maxPagesToList; $i++) {
                $pages[] = $i;
            }
        } else if ($this->lastPage <= floor($this->maxPagesToList/2)+$this->currentPage) {
            $begin = $this->lastPage - $this->maxPagesToList + 1;
            for ($i = $begin; $i <= $this->lastPage; $i++) {
                $pages[] = $i;
            }
        } else {
            $begin = $this->currentPage - floor($this->maxPagesToList/2);
            $end   = $this->currentPage + floor($this->maxPagesToList/2);
            for ($i = $begin; $i <= $end; $i++) {
                $pages[] = $i;
            }
        }
        return $pages;

    }

    /**
     * Get corrected compare for Doctrine Expr
     *
     * @param string $compare Comparision to transform
     *
     * @return string
     */
    protected function getCompare($compare)
    {
        switch ($compare) {
            case 'neq':
            case '!=':
                return 'neq';
                break;
            case 'lt':
            case '<':
                return 'lt';
                break;
            case 'lte':
            case '<=':
                return 'lte';
                break;
            case 'gt':
            case '>':
                return 'gt';
                break;
            case 'gte':
            case '>=':
                return 'gte';
                break;
            case 'in':
                return 'in';
                break;
            case 'notIn':
                return 'notIn';
                break;
            case 'like':
                return 'like';
                break;
            case 'between':
                return 'between';
                break;
            case 'eq':
            case '=':
            case null:
            default:
                return 'eq';
                break;
        }
    }

}