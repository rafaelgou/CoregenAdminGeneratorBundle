<?php
namespace Coregen\AdminGeneratorBundle\Data;

/**
 * List Data
 *
 * @package    Coregen
 * @subpackage Data
 */
class ListData extends AbstractData
{
    /**
     * Get Metadata for create Data Structured
     *
     * @return array
     */
    protected function getMetadata()
    {
        return array(
            'title' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'string',
                ),
            'query_builder' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'string',
                ),
            'display' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'array',
                ),
            'layout' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'string',
                ),
            'stackedTemplate' => array(
                'required' => false,
                'null'     => true,
                'length'   => null,
                'type'     => 'string',
                ),
            'sort' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'array',
                ),
            'sort_fields' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'array',
                ),
            'max_per_page' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'int',
                ),
            'object_actions' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'array',
                ),
            'batch_actions' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'array',
                ),
            );
    }

}
