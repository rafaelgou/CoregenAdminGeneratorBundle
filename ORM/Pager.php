<?php
namespace Coregen\AdminGeneratorBundle\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Coregen\AdminGeneratorBundle\Generator\Generator;
use Symfony\Bundle\DoctrineBundle\Registry;


class Pager
{
    /**
     * @var Doctrine\ORM\entityManager
     */
    protected $entityManager;

    /*
     * @var Doctrine\ORM\QueryBuilder
     */
    protected $queryBuilder;

    /*
     * @var Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @var Coregen\AdminGeneratorBundle\Generator\Generator
     */
    protected $generator;

    /**
     * @var int
     */
    protected $currentPage = false;

    /**
     * @var int
     */
    protected $nextPage = false;

    /**
     * @var int
     */
    protected $previousPage = false;

    /**
     * @var int
     */
    protected $lastPage = false;

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
     * @param Symfony\Bundle\DoctrineBundle\Registry           $doctrineRegistry The Doctrine Registry
     * @param Coregen\AdminGeneratorBundle\Generator\Generator $generator        The Coregen Generator
     */
    public function __construct(Registry $doctrineRegistry, $generator)
    {

        $this->entityManager  = $entityManager;
        $this->generator = $generator;
    }

    /**
     * Executes the query
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

        return $this->getQueryBuilder()->getQuery()->execute();
    }

    /**
     * Executes the query ( alias to execute() )
     * @return mixed
     */
    public function getResults()
    {
        return $this->execute();
    }


    /**
     * Returns the Query Builder
     * @return Doctrine\ODM\MongoDB\Query\Builder
     */
    public function getQueryBuilder()
    {
        $skip  = $this->currentPage <= 1 ? 0 : ($this->currentPage-1) * $this->limit;


        $this->queryBuilder = $this->entityManager
                                ->createQueryBuilder($this->generator)
                                    ->setQueryArray($this->query)
                                    ->limit($this->limit)
                                    ->skip($skip);

        foreach ($this->sort as $field => $order)
        {
            $this->queryBuilder->sort($field, $order);
        }
        return $this->queryBuilder;
    }

    /**
     * Sets the query limit
     * @param integer $limit
     * @return DynamicPager
     */
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    /**
     * Sets the query page
     * @param int $page
     * @return DynamicPager
     */
    public function setCurrent($page)
    {
        $this->currentPage = (int) $page;
        return $this;
    }

    /**
     * Sets the query
     * @param array $query
     * @return DynamicPager
     */
    public function setQuery($query=array())
    {
        $this->query = is_array($query) ? $query : array();
        return $this;
    }

    /**
     * Sets the query sort
     * @param array $sort
     * @return DynamicPager
     */
    public function setSort($sort=array())
    {
        $this->sort = is_array($sort) ? $sort : array();
        return $this;
    }

    /**
     * Returns the documents count
     * @return integer
     */
    public function getCount()
    {
        if (null === $this->count) $this->count = $this->getQueryBuilder()->getQuery()->count();
        return $this->count;
    }

    /**
     * Returns the documents count
     * @return integer
     */
    public function count()
    {
        return $this->getCount();
    }

    /**
     * Returns actual page
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Returns next page
     * @return integer
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }

    /**
     * Returns previous page
     * @return integer
     */
    public function getPreviousPage()
    {
        return $this->previousPage;
    }

    /**
     * Returns last page
     * @return integer
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * Check if it is on the first page
     * @return boolean
     */
    public function isFirstPage()
    {
        return ($this->currentPage == 1) ? true : false;
    }

    /**
     * Check if it is on the last page
     * @return boolean
     */
    public function isLastPage()
    {
        return ($this->currentPage == $this->lastPage) ? true : false;
    }

    /**
     * Check if has pages enough to paginate (more than limit)
     * @return boolean
     */
    public function haveToPaginate()
    {
        return ($this->count() > $this->limit) ? true : false;
    }

}