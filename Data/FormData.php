<?php
namespace Coregen\AdminGeneratorBundle\Data;

/**
 * Form Data
 *
 * @package coregen
 * @subpackage data
 */
class FormData extends AbstractData
{
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
