<?php

namespace Coregen\AdminGeneratorBundle\Generator;

use Coregen\AdminGeneratorBundle\Data\AbstractData;
use Coregen\AdminGeneratorBundle\Data\FieldsData;
use Coregen\AdminGeneratorBundle\Data\ListData;
use Coregen\AdminGeneratorBundle\Data\FormData;
use Coregen\AdminGeneratorBundle\Data\EditData;
use Coregen\AdminGeneratorBundle\Data\NewData;
use Coregen\AdminGeneratorBundle\Data\FilterData;

class Generator extends AbstractData
{

    protected $config = null;

    /**
     * Classes for all object data
     * @var array
     */
    protected $classes = array(
        'fields' => '\Coregen\AdminGeneratorBundle\Data\FieldsData',
        'list'   => '\Coregen\AdminGeneratorBundle\Data\ListData',
        'form'   => '\Coregen\AdminGeneratorBundle\Data\FormData',
        'edit'   => '\Coregen\AdminGeneratorBundle\Data\EditData',
        'new'    => '\Coregen\AdminGeneratorBundle\Data\NewData',
        'filter' => '\Coregen\AdminGeneratorBundle\Data\FilterData',
    );

    public function __constructor()
    {
    }

    protected function getMetadata()
    {
        return array(
            'theme' => array(
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
            'filter' => array(
                'required' => true,
                'null'     => false,
                'length'   => null,
                'type'     => 'object',
                ),

            );
    }
}
