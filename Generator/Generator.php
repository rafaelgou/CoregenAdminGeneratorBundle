<?php

namespace Coregen\AdminGeneratorBundle\Generator;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Coregen\AdminGeneratorBundle\Data\AbstractData;

class Generator extends AbstractData
{

//    /**
//     * Config Loader
//     * @var Symfony\Component\Config\Loader\LoaderInterface
//     */
//    protected $loader = null;

    protected $config = null;

    /**
     * Classes for all object data
     * @var array
     */
    protected $classes = array(
        'fields' => '\Coregen\AdminGeneratorBundle\\Data\FieldsData',
        'list'   => '\Coregen\AdminGeneratorBundle\\Data\ListData',
        'form'   => '\Coregen\AdminGeneratorBundle\\Data\FormData',
        'edit'   => '\Coregen\AdminGeneratorBundle\\Data\EditData',
        'new'    => '\Coregen\AdminGeneratorBundle\\Data\NewData',
        'filter' => '\Coregen\AdminGeneratorBundle\\Data\FilterData',
    );

    public function __constructor(LoaderInterface $loader)
    {
        '@CoregenAdminGeneratorBundle/Resources/config/generator.yml';
        $loader = new YamlFileLoader;
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
