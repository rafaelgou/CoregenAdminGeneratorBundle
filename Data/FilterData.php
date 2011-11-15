<?php
namespace Coregen\AdminGeneratorBundle\Data;

/**
 * List Data
 *
 * @package coregen
 * @subpackage data
 */
class FilterData extends AbstractData
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
            'fields' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'array',
                ),
            );
    }

}
