<?php
namespace Coregen\AdminGeneratorBundle\Data;

/**
 * List Data
 *
 * @package coregen
 * @subpackage data
 */
class EditData extends AbstractData
{
    protected function getMetadata()
    {
        return array(
            'title' => array(
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
            'actions' => array(
                'required' => false,
                'null'     => true,
                'length'   => null,
                'type'     => 'array',
                ),
            );
    }

}
