<?php
namespace Coregen\AdminGeneratorBundle\Data;

/**
 * Fields Data
 *
 * @package    Coregen
 * @subpackage Data
 */
class FieldsData extends AbstractData
{
    /**
     * Get Metadata for create Data Structured
     *
     * @return array
     */
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
