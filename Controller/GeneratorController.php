<?php

namespace Coregen\AdminGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Coregen\AdminGeneratorBundle\Generator\Generator;
use Coregen\AdminGeneratorBundle\Form\FilterType;

/**
 * Main Generator Controller
 *
 * @package CoregenAdminGenerator
 * @author  Rafael Goulart <rafaelgou@gmail.com>
 */
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

    /**
     * Configure generator and other stuffs
     *
     * @return void
     */
    abstract protected function configure();

    /**
     * Index action
     *
     * @return View
     */
    abstract public function indexAction();

    /**
     * Show action
     *
     * @param mixed $id Entity/Document Id
     *
     * @return View
     */
    abstract public function showAction($id);

    /**
     * New action
     *
     * @return View
     */
    abstract public function newAction();

    /**
     * Create Action
     *
     * @return View
     */
    abstract public function createAction();

    /**
     * Edit Action
     *
     * @param mixed $id Entity/Document Id
     *
     * @return View
     */
    abstract public function editAction($id);

    /**
     * Update Action
     *
     * @param mixed $id Entity/Document Id
     *
     * @return View
     */
    abstract public function updateAction($id);

    /**
     * Delete Action
     *
     * @param mixed $id Entity/Document Id
     *
     * @return View
     */
    abstract public function deleteAction($id);

    /**
     * Load Generator
     *
     * @param Coregen\AdminGeneratorBundle\Generator\Generator $generator A generator
     *
     * @return void
     */
    abstract public function loadGenerator($generator);

    /**
     * Return the Doctrine ORM/EntityManager or ODM/DocumentManager
     *
     * @return Manager
     */
    protected function getManager()
    {
        return $this->pager->getManager();
    }

    /**
     * Return the Doctrine ORM/ODM Repository
     *
     * @return Manager
     */
    protected function getRepository()
    {
        return $this->pager->getRepository();
    }

    /**
     * Renders a view
     *
     * @param string $view       The view to find
     * @param array  $parameters Adicional parameters to the view
     *
     * @return Response A Response instance
     */
    public function renderView($view, array $parameters = array())
    {
        $parameters = array_merge($parameters,
            array(
                'generator'  => $this->generator,
                )
        );

        return parent::render($this->getViewToRender($view), $parameters);
    }

    /**
     * Get the View To Render
     *
     * Permits overriding default views by
     * using full path (not begging with ":")
     *
     * @param string $view A internal view name
     *
     * @return string
     */
    protected function getViewToRender($view)
    {
        $view = $this->getView($view);

        if (substr($view, 0, 1) == ':') {
            return $this->generator->coreTheme . $view;
        } else {
            return $view;
        }
    }

    /**
     * Get the View by configuration
     *
     * @param string $view A internal view name
     *
     * @return string
     */
    protected function getView($view)
    {
        if (isset($this->views[$view])) {
            return $this->views[$view];
        } else {
            return $this->views['list'];
        }
    }

    /**
     * Return a paged query, and sets variables for paging
     *
     * @param array $query      An array with the query
     * @param mixed $page       Current page or false
     * @param mixed $maxPerPage Limit (max per page) or false
     * @param mixed $sort       Current sort or false
     *
     * @return Dynamic\MongoDBBundle\Core\DynamicPager
     */
    protected function getPager($query=array(), $page=false, $maxPerPage=false, $sort=false)
    {
        $this->pager->setCurrent($page ? $page : $this->getPage());
        $this->pager->setLimit($maxPerPage ? $maxPerPage : $this->generator->list->max_per_page);
        $this->pager->setQuery($query ? $query : $this->getQuery());

        $this->configureSort();
        if ($sort && is_array($sort)) {
            $this->pager->setSort($sort);
        } elseif (count($this->getSort() !== 0))  {
            $this->pager->setSort($this->getSort());
        } else {
            $this->pager->setSort($this->generator->list->sort);
        }
        return $this->pager;
    }

    /**
     * Set current page on session
     *
     * @param integer $page Page number
     *
     * @return void
     */
    protected function setPage($page)
    {
        $this->getRequest()->getSession()->set($this->generator->route . '.page', $page);
    }

    /**
     * Return current page
     *
     * @return Integer
     */
    protected function getPage()
    {
      return $this->getRequest()->getSession()->get($this->generator->route . '.page', 1);
    }

    /**
     * Set current sort on session
     *
     * @param string $fieldName The field name to sort
     *
     * @return void
     */
    protected function setSort($fieldName)
    {
        $sort = $this->getSort();
        if (count($sort) === 0) {
            $sortOrder = 'ASC';
        } else {
            if (current($sort) == 'ASC' and key($sort) == $fieldName) {
                $sortOrder = 'DESC';
            } else {
                $sortOrder = 'ASC';
            }
        }
        $this->getRequest()->getSession()->set($this->generator->route . '.sort', array($fieldName => $sortOrder));
    }

    /**
     * Return current sort
     *
     * @return Integer
     */
    protected function getSort()
    {
      return $this->getRequest()->getSession()->get($this->generator->route . '.sort', array());
    }

    /**
     * Configure current sort by request
     *
     * @return void
     */
    protected function configureSort()
    {
        // Configuring the Generator Controller
        $this->configure();
        $sort = $this->getRequest()->get('sort', false);
        if ($sort) {
            $this->setSort($sort);
        }
    }

    /**
     * Get query (for a while, just proxy for getFilter)
     *
     * @return array
     */
    protected function getQuery()
    {
        return $this->getFilter();
    }

    /**
     * Get query (for a while, just proxy for getFilter)
     *
     * @param string $id Form id
     *
     * @return array
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }

    /**
     * Configure current filter by request
     *
     * @return void
     */
    protected function configureFilter()
    {
        // Configuring the Generator Controller
        $this->configure();

        $filtertype = $this->getRequest()->get('filtertype', false);

        if ($filtertype) {
            $this->setPage(1);
            if (isset($filtertype['reset'])) {
                $filter = array();
            } else {
                $filter = array();
                foreach ($this->getRequest()->get('filtertype', false) as $key => $value) {
                    if ($value != '') {
                        $keyClean = str_replace('_from', '', $key);
                        $keyClean = str_replace('_to', '', $keyClean);

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

    /**
     * Get current filter
     *
     * @return array
     */
    protected function getFilter()
    {
        // Configuring the Generator Controller
        $this->configure();

        return $this->getRequest()->getSession()->get($this->generator->route . '.filter', array());
    }

    /**
     * Get Filter Form data
     *
     * @return array
     */
    protected function getFilterFormData()
    {
        if ($this->generator->filter->fields && is_array($this->generator->filter->fields)) {

            $data = $this->getFilter();
            foreach ($this->generator->filter->fields as $fieldName => $field) {
                switch($field['type']) {
                    case 'entity':
                        try {
                            if (isset($data[$fieldName]) && $data[$fieldName] != '') {
                                $entity = $this->getDoctrine()->getEntityManager()
                                                    ->getRepository($field['options']['class'])
                                                    ->find($data[$fieldName]);
                                if ($entity) {
                                    $data[$fieldName] = $entity;
                                } else {
                                    unset($data[$fieldName]);
                                }
                            }

                        } catch (Exception  $exc) {
                            unset($data[$fieldName]);
                        }
                        break;
                    case 'daterange':
                    case 'text':
                    default:
                        break;
                }
            }
        }
        return $data;
    }

    /**
     * Create and return a generator filter form
     *
     * @return Coregen\AdminGeneratorBundle\Form\FilterType
     */
    protected function createFilterForm()
    {

        // Configuring the Generator Controller
        $this->configure();

        if ($this->generator->filter->fields && is_array($this->generator->filter->fields)) {

            $form = $this->container->get('form.factory')->createBuilder(new FilterType(), $this->getFilterFormData(), array());
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
                    case 'entity':
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
            //$form->setData()
            $form = $form->getForm();
        } else {
            $form = false;
        }

        return $form;
        ;
    }

}
