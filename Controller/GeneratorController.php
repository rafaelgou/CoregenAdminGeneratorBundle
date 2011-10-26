<?php

namespace Coregen\AdminGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Coregen\AdminGeneratorBundle\Generator\Generator;

abstract class GeneratorController extends Controller
{
    /**
     * @var array
     */
    protected $views= array(
        'listGrid'    => ':Coregen:listGrid.html.twig',
        'listStacked' => ':Coregen:listStacked.html.twig',
        'edit'        => ':Coregen:edit.html.twig',
        'new'         => ':Coregen:new.html.twig',
        'form'        => ':Coregen:form.html.twig',
        'show'        => ':Coregen:show.html.twig',
    );

    /**
     * @var Coregen\AdminGeneratorBundle\Generator\Generator
     */
    protected $generator = null;

    /**
     * @var Pager
     */
    protected $pager = null;

    abstract protected function configure();

    abstract public function indexAction();

    abstract public function showAction($id);

    abstract public function newAction();

    abstract public function createAction();

    abstract public function editAction($id);

    abstract public function updateAction($id);

    abstract public function deleteAction($id);

    abstract protected function createDeleteForm($id);

    /**
     * Returns the Doctrine ORM/EntityManager or ODM/DocumentManager
     * @return Manager
     */
    protected function getManager()
    {
        return $this->pager->getManager();
    }

    /**
     * Returns the Doctrine ORM/ODM Repository
     * @return Manager
     */
    protected function getRepository()
    {
        return $this->pager->getRepository();
    }

    /**
     * Renders a view
     *
     * @param string $view        The view to find
     * @param array  $parameters  Adicional parameters to the view
     *
     * @return Response A Response instance
     */
    public function renderView($view, array $parameters = array())
    {
        $parameters = array_merge($parameters,
                array(
                    'generator'      => $this->generator,
                    )
                );
        return parent::render($this->generator->coreTheme . $this->getView($view), $parameters);
    }

    /**
     *
     * @param string $view
     * @return string
     */
    protected function getView($view)
    {
        if (isset($this->views[$view]))
        {
            return $this->views[$view];
        } else {
            return $this->views['list'];
        }
    }

    /**
     * Returns a paged query, and sets variables for paging
     * @return Dynamic\MongoDBBundle\Core\DynamicPager
     */
    protected function getPager($query=array(), $page=false, $max_per_page=false, $sort=false)
    {
        $this->pager->setCurrent($page ? $page : $this->getPage());
        $this->pager->setLimit($max_per_page ? $max_per_page : $this->generator->list->max_per_page);
        $this->pager->setQuery($query);
/*
        if ($sort)
        {
            if (!is_array($sort)) $sort[$sort] = 'asc';
        } else {
            $sort = array();
            foreach ($this->getDefinitionModel()->getList()->getSort() as $s)
            {
                $sort['_f.' . $s] = 'asc';
            }
        }
        $pager->setSort($sort);
*/
        return $this->pager;
    }

    /**
     * Returns current page
     * @return Integer
     */
    protected function getPage()
    {
      return $this->getRequest()->getSession()->get($this->generator->class . '.page', 1);
    }

    /**
     * Sets current page on session
     *
     * @param integer $page
     *
     * @return void
     */
    protected function setPage($page)
    {
        $this->getRequest()->getSession()->set($this->generator->class . '.page', $page);
    }

    protected function loadGenerator(Generator $generator)
    {
        $this->generator = $generator;
        $this->pager = $this->get('coregen.orm.pager')
                ->setGenerator($generator);

    }
}
