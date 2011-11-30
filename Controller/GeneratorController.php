<?php

namespace Coregen\AdminGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Coregen\AdminGeneratorBundle\Generator\Generator;
use Coregen\AdminGeneratorBundle\Form\FilterType;

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

    protected function loadGenerator(Generator $generator)
    {
        $this->generator = $generator;
        $this->pager = $this->get('coregen.orm.pager')
                ->setGenerator($generator);

    }

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
        $this->pager->setQuery($query ? $query : $this->getQuery());
        if ($sort && is_array($sort)) {
            $this->pager->setSort($sort);
        } else {
            $this->pager->setSort($this->generator->list->sort);
        }
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
        $this->getRequest()->getSession()->set($this->generator->route . '.page', $page);
    }


    protected function getQuery()
    {
        return $this->getFilter();
    }

    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    protected function configurefilter()
    {
        // Configuring the Generator Controller
        $this->configure();
        $filtertype = $this->getRequest()->get('filtertype', false);

        if ($filtertype) {

            if (isset($filtertype['reset'])) {
                $filter = array();
            } else {
                $filter = array();
                foreach ($this->getRequest()->get('filtertype', false) as $key => $value) {
                    if ($value != '') {
                        $keyClean = str_replace('_from', '',$key);
                        $keyClean = str_replace('_to', '',$keyClean);

                        if ($this->generator->filter->fields[$keyClean]['type'] == 'daterange') {
                            $dateFormaterDefault = \IntlDateFormatter::create(
                                $this->getRequest()->getSession()->getLocale(),
                                \IntlDateFormatter::MEDIUM,
                                \IntlDateFormatter::NONE
                            );
                            $dateFormaterEn = \IntlDateFormatter::create(
                                'en',
                                \IntlDateFormatter::MEDIUM,
                                \IntlDateFormatter::NONE
                            );
                            //print_r($dateFormater->format($value));
                            //$filter[$key] = $date->format($dateFormater->getPattern());
                            $filter[$key] = $dateFormaterEn->format($dateFormaterDefault->parse($value));
                        } else {
                            $filter[$key] = $value;
                        }

                    }
                }
                //print_r($filter);exit;
            }
            $this->getRequest()->getSession()->set($this->generator->route . '.filter', $filter);
        }
    }

    protected function getFilter()
    {
        // Configuring the Generator Controller
        $this->configure();

        return $this->getRequest()->getSession()->get($this->generator->route . '.filter', array());
    }

    protected function createFilterForm()
    {

        // Configuring the Generator Controller
        $this->configure();

        if ($this->generator->filter->fields && is_array($this->generator->filter->fields)) {

            $form = $this->container->get('form.factory')->createBuilder(new FilterType(), $this->getFilter() ? $this->getFilter(): array(), array());
            foreach ($this->generator->filter->fields as $fieldName => $field) {
                $field['options'] = isset($field['options']) ? $field['options'] : array();

                switch($field['type']) {
                    case 'daterange':
                        $form->add(
                                $fieldName . '_from',
                                'date',
                                array_merge(
                                    $field['options'],
                                    array(
                                        'required' => false,
                                        'label'    => $this->generator->filter->fields[$fieldName]['label'] . " from",
                                        'format' => 'dd/MM/yyyy',//\IntlDateFormatter::MEDIUM,
                                        'widget'   => 'single_text',
                                        'attr'     => array('class'=>'date span2'),
                                        'input'    => 'string'
                                    ))
                        );
                        $form->add(
                                $fieldName . '_to',
                                'date',
                                array_merge(
                                    $field['options'],
                                    array(
                                        'required' => false,
                                        'label'    => $this->generator->filter->fields[$fieldName]['label'] . " from",
                                        'format' => \IntlDateFormatter::MEDIUM,
                                        'widget' => 'single_text',
                                        'attr'   => array('class'=>'date span2'),
                                        'input'    => 'string'
                                    ))
                        );
                        break;
                    case 'text':
                    default:
                        $form->add(
                                $fieldName,
                                $field['type'],
                                array_merge(
                                    $field['options'],
                                    array(
                                        'required' => false,
                                        'label' => $this->generator->filter->fields[$fieldName]['label'],
                                    ))
                        );
                        break;
                }
            }
            $form = $form->getForm();
        } else {
            $form = false;
        }

        return $form;
        ;
    }

}
