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

abstract class Generator extends AbstractData
{

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

    abstract protected function configure();

    public function __construct()
    {
        $this->configure();
    }

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
//echo '<pre>';print_r($list); exit;

        if (!isset($list['batch_actions'])) {
            $list['batch_actions'] = array(
                'delete' => true
            );
        } else {
            if (!isset($list['batch_actions']['delete'])) {
                $list['batch_actions']['delete'] = true;
            }
        }

        $this->validateAndStore('list', $list);
        return $this;
    }

    /**
     * Define Fields
     *
     * @param mixed $fields The Fields
     */
    public function setFields($fields)
    {
        $newFields = array();
        foreach($fields as $name => $field) {
            $newFields[$name] = $field;
            if (!isset($field['label'])) {
                $newFields[$name]['label'] = ucfirst($field);
            }
            if (!isset($field['size'])) {
                $newFields[$name]['size'] = 'medium';
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
     */
    public function setFilter($filter)
    {
        $this->validateAndStore('filter', $filter);
        return $this;
    }

    /**
     * Return just the fields to be displayed in some part of the CRUD
     *
     * @return array
     */
    public function getDisplayFields($crudId)
    {
        if (!isset($this->get($crudId)->display) && !is_array($this->get($crudId)->display)) {
            return $this->fields;
        }

        $fields = array();
        foreach($this->get($crudId)->display as $displayField ) {
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
     * @param string $field The field to render
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
            foreach($value as $v) {
                $values[] = (string) $v;
            }
            $value = implode(', ', $values);
        }
        return $value;
    }

    /**
     * Return just the fields to be hidden in some part of the CRUD
     *
     * @return array
     */
    public function getHiddenFields($crudId)
    {

        if (!isset($this->get($crudId)->display) && !is_array($this->get($crudId)->display)) {
            return $this->fields;
        }

        $fieldNames = array();

        foreach(array_keys($this->fields) as $fieldName ) {
            if (!in_array($fieldName, $this->get($crudId)->display)) {
                $fieldNames[] = $fieldName;
            }
        }


        return $fieldNames;
    }


}
