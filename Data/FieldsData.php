<?php
namespace Coregen\AdminGeneratorBundle\Data;

/**
 * Fields Data
 *
 * @package coregen
 * @subpackage data
 */
class FieldsData extends AbstractData
{
    protected function getMetadata()
    {
        return array(
            'class' => array(
                'required' => false,
                'null'     => true,
                'length'   => null,
                'type'     => 'array',
                ),

            );

    }

}
