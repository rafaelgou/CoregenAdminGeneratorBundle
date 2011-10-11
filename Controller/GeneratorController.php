<?php

namespace Coregen\AdminGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Coregen\AdminGeneratorBundle\Generator\Generator;

abstract class GeneratorController extends Controller
{
    protected $views= array(
        'listGrid'    => ':Coregen:listGrid.html.twig',
        'listStacked' => ':Coregen::listStacked.html.twig',
        'edit'        => ':Coregen::edit.html.twig',
        'new'         => ':Coregen::new.html.twig',
        'form'        => ':Coregen::form.html.twig',
    );

    protected $generator = null;

    abstract protected function configure();

    abstract public function indexAction();

    abstract public function showAction($id);

    abstract public function newAction();

    abstract public function createAction();

    abstract public function editAction($id);

    abstract public function updateAction($id);

    abstract public function deleteAction($id);

    abstract protected function createDeleteForm($id);

    abstract protected function getManager();

    abstract protected function getRepository();


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
        return parent::render($this->generator->theme . $this->getView($view), $parameters);
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
        $pager = new DynamicPager($this->getDocumentManager(), $this->definitionName);
        $pager->setCurrent($page ? $page : $this->getPage());
        $pager->setLimit($max_per_page ? $max_per_page : $this->getDefinitionModel()->getList()->getMaxPerPage());
        $pager->setQuery($query);

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

        return $pager;
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
     * @param integer $page
     */
    protected function setPage($page)
    {
        $this->getRequest()->getSession()->set($this->generator->class . '.page', $page);
    }

    protected function loadGenerator(Generator $generator)
    {
        echo '<pre>';print_r($generator);echo '</pre>';
        $this->generator = $generator;
    }
}
