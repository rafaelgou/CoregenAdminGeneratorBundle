<?php
namespace Coregen\AdminGeneratorBundle\Data;

/**
 * List Data
 *
 * @package    Coregen
 * @subpackage Data
 */
class FilterData extends AbstractData
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
            'fields' => array(
                'required' => true,
                'null'     => true,
                'length'   => null,
                'type'     => 'array',
                ),
            );
    }

}
