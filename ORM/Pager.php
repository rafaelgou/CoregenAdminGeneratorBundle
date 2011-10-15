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
     * Defines the generator
     *
     * @param Coregen\AdminGeneratorBundle\Generator\Generator $generator The Coregen Generator
     */
    public function setGenerator(Generator $generator)
    {
        $this->generator  = $generator;
        return $this;
    }

    /**
     * Returns de Repository
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
     * Returns de EntityManager
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

        return $this->getRepository()
                ->findBy(
                        array(), //$criteria,
                        array(), //$orderBy,
                        $this->limit,
                        ($this->currentPage -1 )* $this->limit
                        );

                //getQueryBuilder()->getQuery()->execute();
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
     * Returns the Query Builder
     *
     * @return Doctrine\ODM\MongoDB\Query\Builder
     */
    public function getQueryBuilder()
    {
        $skip  = $this->currentPage <= 1 ? 0 : ($this->currentPage-1) * $this->limit;


        $this->queryBuilder = $this->entityManager
                                ->createQueryBuilder($this->generator->model)
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
     * @param int $page
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
     *
     * @param array $sort
     *
     * @return  Pager
     */
    public function setSort($sort=array())
    {
        $this->sort = is_array($sort) ? $sort : array();
        return $this;
    }

    /**
     * Returns the documents count
     *
     * @return integer
     */
    public function getCount()
    {
        if (null === $this->count) {
            $result = $this->getRepository()
                ->createQueryBuilder('e')
                ->addSelect('count(e.id) as total_count')
                ->getQuery()
                ->execute();
            $this->count = $result[0]['total_count'];
        }
        return $this->count;
    }

    /**
     * Returns the documents count
     *
     * @return integer
     */
    public function count()
    {
        return $this->getCount();
    }

    /**
     * Returns actual page
     *
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Returns next page
     *
     * @return integer
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }

    /**
     * Returns previous page
     *
     * @return integer
     */
    public function getPreviousPage()
    {
        return $this->previousPage;
    }

    /**
     * Returns last page
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
     * Returns the page range to paginate
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

}