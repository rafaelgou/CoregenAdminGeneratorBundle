<?php
namespace Coregen\AdminGeneratorBundle\Data;

/**
 * Form Data
 *
 * @package    Coregen
 * @subpackage Data
 */
class FormData extends AbstractData
{
    /**
     * Get Metadata for create Data Structured
     *
     * @return array
     */
    protected function getMetadata()
    {
        return array(
            'type' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'string',
                ),
            );
    }

}
