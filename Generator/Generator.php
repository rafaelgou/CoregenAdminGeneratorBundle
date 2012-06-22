<?php

namespace Coregen\AdminGeneratorBundle\Generator;

use Coregen\AdminGeneratorBundle\Data\AbstractData;
use Coregen\AdminGeneratorBundle\Data\FieldsData;
use Coregen\AdminGeneratorBundle\Data\ListData;
use Coregen\AdminGeneratorBundle\Data\FormData;
use Coregen\AdminGeneratorBundle\Data\EditData;
use Coregen\AdminGeneratorBundle\Data\NewData;
use Coregen\AdminGeneratorBundle\Data\ShowData;
use Coregen\AdminGeneratorBundle\Data\FilterData;
use Symfony\Component\DependencyInjection\Container;

/**
 * Generator
 *
 * @package CoregenAdminGenerator
 * @author  Rafael Goulart <rafaelgou@gmail.com>
 */
abstract class Generator extends AbstractData
{

    /**
     * Config
     * @var array
     */
    protected $config = null;

    /**
     * Classes for all object data
     * @var array
     */
    protected $classes = array(
        'list'   => '\Coregen\AdminGeneratorBundle\Data\ListData',
        'form'   => '\Coregen\AdminGeneratorBundle\Data\FormData',
        'edit'   => '\Coregen\AdminGeneratorBundle\Data\EditData',
        'new'    => '\Coregen\AdminGeneratorBundle\Data\NewData',
        'show'   => '\Coregen\AdminGeneratorBundle\Data\ShowData',
        'filter' => '\Coregen\AdminGeneratorBundle\Data\FilterData',
    );

    /**
     * Twig
     * @var \Twig_Environment
     */
    protected $twig = null;

    /**
     * Configure
     *
     * @return void
     */
    abstract protected function configure();

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->configure();
    }

    /**
     * Get Metadata for create Data Structured
     *
     * @return array
     */
    protected function getMetadata()
    {
        return array(
            'class' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'string',
                ),
            'model' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'string',
                ),
            'layoutTheme' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'string',
                ),
            'coreTheme' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'string',
                ),
            'route' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'string',
                ),
            'fields' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'array',
                ),
            'list' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'object',
                ),
            'form' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'object',
                ),
            'edit' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'object',
                ),
            'new' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'object',
                ),
            'show' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'object',
                ),
            'filter' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'object',
                ),

            );
    }

    /**
     * Define Class Name
     *
     * @param mixed $class The Class Name
     *
     * @return Generator
     */
    public function setClass($class)
    {
        $this->validateAndStore('class', $class);
        return $this;
    }

    /**
     * Define Model Name
     *
     * @param mixed $model The Model Name
     *
     * @return Generator
     */
    public function setModel($model)
    {
        $this->validateAndStore('model', $model);
        return $this;
    }

    /**
     * Define Layout Theme Name
     *
     * @param mixed $theme The Class Name
     *
     * @return Generator
     */
    public function setLayoutTheme($theme)
    {
        $this->validateAndStore('layoutTheme', $theme);
        return $this;
    }

    /**
     * Define Core Theme Name
     *
     * @param mixed $theme The Class Name
     *
     * @return Generator
     */
    public function setCoreTheme($theme)
    {
        $this->validateAndStore('coreTheme', $theme);
        return $this;
    }

    /**
     * Define Route
     *
     * @param mixed $route The Route
     *
     * @return Generator
     */
    public function setRoute($route)
    {
        $this->validateAndStore('route', $route);
        return $this;
    }

    /**
     * Define Actions
     *
     * @param mixed $actions The Actions
     *
     * @return Generator
     */
    public function setActions($actions)
    {
        $this->validateAndStore('actions', $actions);
        return $this;
    }

    /**
     * Define List
     *
     * @param mixed $list The List
     *
     * @return Generator
     */
    public function setList($list)
    {
        if (!isset($list['object_actions'])) {
            $list['object_actions'] = array(
                'edit'   => true,
                'view'   => true,
                'delete' => true
            );
        } else {
            if (!isset($list['object_actions']['edit'])) {
                $list['object_actions']['edit'] = true;
            }
            if (!isset($list['object_actions']['view'])) {
                $list['object_actions']['view'] = true;
            }
            if (!isset($list['object_actions']['delete'])) {
                $list['object_actions']['delete'] = true;
            }
        }

        if (!isset($list['batch_actions'])) {
            $list['batch_actions'] = array(
                'delete' => array(
                    'label'  => 'Delete',
                    'success_message' => '%s item(s) has been excluded successfully.'
                ),
            );
        }

        $this->validateAndStore('list', $list);
        return $this;
    }

    /**
     * Define Fields
     *
     * @param mixed $fields The Fields
     *
     * @return Generator
     */
    public function setFields($fields)
    {
        $newFields = array();
        foreach ($fields as $name => $field) {
            $newFields[$name] = $field;
            if (!isset($field['label'])) {
                $newFields[$name]['label'] = ucfirst($field);
            }
            if (!isset($field['size'])) {
                $newFields[$name]['size'] = '';
            }
            if (!isset($field['class'])) {
                $newFields[$name]['class'] = '';
            }
            if (!isset($field['help'])) {
                $newFields[$name]['help'] = false;
            }
        }

        $this->validateAndStore('fields', $newFields);
        return $this;
    }

    /**
     * Define Form
     *
     * @param mixed $form The Form
     *
     * @return Generator
     */
    public function setForm($form)
    {
        $this->validateAndStore('form', $form);
        return $this;
    }

    /**
     * Define Edit
     *
     * @param mixed $edit The Edit
     *
     * @return Generator
     */
    public function setEdit($edit)
    {
        $this->validateAndStore('edit', $edit);
        return $this;
    }

    /**
     * Define New
     *
     * @param mixed $new The New
     *
     * @return Generator
     */
    public function setNew($new)
    {
        if (!isset($new['actions'])) {
            $new['actions'] = array(
                'save'         => true,
                'save_and_add' => true,
                'back_to_list' => true
            );
        } else {
            if (!isset($new['actions']['save'])) {
                $new['actions']['save'] = true;
            }
            if (!isset($new['actions']['save_and_add'])) {
                $new['actions']['save_and_add'] = true;
            }
            if (!isset($new['actions']['back_to_list'])) {
                $new['actions']['back_to_list'] = true;
            }
        }

        $this->validateAndStore('new', $new);
        return $this;
    }

    /**
     * Define Show
     *
     * @param mixed $show The Show
     *
     * @return Generator
     */
    public function setShow($show)
    {
        $this->validateAndStore('show', $show);
        return $this;
    }

    /**
     * Define Filter
     *
     * @param mixed $filter The Filter
     *
     * @return Generator
     */
    public function setFilter($filter)
    {
        $this->validateAndStore('filter', $filter);
        return $this;
    }

    /**
     * Return just the fields to be displayed in some part of the CRUD
     *
     * @param integer $crudId The CRUD id (list, edit, new)
     *
     * @return array
     */
    public function getDisplayFields($crudId)
    {
        if (!isset($this->get($crudId)->display) && !is_array($this->get($crudId)->display)) {
            return $this->fields;
        }

        $fields = array();
        foreach ($this->get($crudId)->display as $displayField) {
            $fields[$displayField] = $this->fields[$displayField];
        }

        if (count($fields)) {
            return $fields;
        } else {
            return $this->fields;
        }

    }

    /**
     * Return just the fields to be displayed in List
     *
     * @return array
     */
    public function getListDisplayFields()
    {
        return $this->getDisplayFields('list');
    }

    /**
     * Return the Batch Actions from the List
     *
     * @return mixed
     */
    public function getListBatchActions()
    {
        $list = $this->get('list');
        if (count($list->batch_actions)) {
            return $list->batch_actions;
        } else {
            return false;
        }
    }

    /**
     * Return just the fields to be displayed in Show
     *
     * @return array
     */
    public function getShowDisplayFields()
    {
        return $this->getDisplayFields('show');
    }

    /**
     * Return just the fields to be displayed in Edit
     *
     * @return array
     */
    public function getEditDisplayFields()
    {
        return $this->getDisplayFields('edit');
    }

    /**
     * Return just the fields to be displayed in New
     *
     * @return array
     */
    public function getNewDisplayFields()
    {
        return $this->getDisplayFields('new');
    }

    /**
     * Returns the Data Value
     *
     * @param string $fieldName The field to render
     * @param object $data      The data to extract info
     *
     * @return string
     */
    public function renderField($fieldName, $data)
    {
        if (method_exists($data, 'is' . Container::camelize($fieldName))) {
            $value = $data->{'is' . Container::camelize($fieldName)}();
        } else {
            $value = $data->{'get' . Container::camelize($fieldName)}();
        }
        if (is_array($value)) {
            $values = array();
            foreach ($value as $v) {
                $values[] = (string) $v;
            }
            $value = implode(', ', $values);
        }
        return $value;
    }

    /**
     * Return just the fields to be hidden in some part of the CRUD
     *
     * @param integer $crudId The CRUD id (list, edit, new)
     *
     * @return array
     */
    public function getHiddenFields($crudId)
    {

        if (!isset($this->get($crudId)->display) && !is_array($this->get($crudId)->display)) {
            return $this->fields;
        }

        $fieldNames = array();

        foreach (array_keys($this->fields) as $fieldName) {
            if (!in_array($fieldName, $this->get($crudId)->display)) {
                $fieldNames[] = $fieldName;
            }
        }


        return $fieldNames;
    }

    /**
     * Return the domain for translate messages
     */
    public function getTransDomain()
    {
        return str_replace(':', '',$this->get('model'));
    }

    /**
     * Return the Batch Actions from the List
     *
     * @param $record The record
     *
     * @return mixed
     */
    public function getListStackedTemplate($record)
    {
        $list = $this->get('list');

        if (null === $this->twig) {
            $loader = new \Twig_Loader_String();
            $this->twig = new \Twig_Environment($loader, array('debug' => false));
        }

        $template = $this->twig->loadTemplate($list->stackedTemplate);
        return $template->render(array('record' => $record));
    }


}
