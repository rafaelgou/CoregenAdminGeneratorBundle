<?php
namespace Coregen\AdminGeneratorBundle\Data;

/**
 * List Data
 *
 * @package    Coregen
 * @subpackage Data
 */
class EditData extends AbstractData
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
