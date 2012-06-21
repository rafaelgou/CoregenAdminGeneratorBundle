<?php

namespace {{ namespace }}\Generator;

use Coregen\AdminGeneratorBundle\Generator\Generator;

class {{ entity }} extends Generator
{
    protected function configure()
    {
        $actions = array();

        $fields  = array(
        {%- for field in fields %}

            '{{ field }}' => array('label' => '{{ field }}'),

        {%- endfor %}

        );

        $list = array(
            'title'           => 'Listing {{ entity }}',
            'query_builder'   => null,
            'display'         => array(
            {%- for field in fields %}

                '{{ field }}',

            {%- endfor %}

            ),
            # grid or stacked, default grid
            'layout'          => 'grid',
            'stackedTemplate' => "<h3>\{\{ record  \}\}</h3><p class=\"details_fixed\">More details</strong></p>",
            'sort'            => array(),
            'max_per_page'    => 20,
            'object_actions'  => array(),
            'batch_actions'   => array(
                'delete' => array(
                    'label'  => 'Delete',
                    'success_message' => '%s item(s) has been excluded successfully.'
                ),
            ),

        );

        $form = array(
            'type'   => '{{ form_type_name }}',
        );

        $edit = array(
            'title'   => 'Editing {{ entity }}',
            'display' => array(
            {%- for field in fields %}

                '{{ field }}',

            {%- endfor %}

            ),
            'actions' => array(),
        );

        $new = array(
            'title'   => 'New {{ entity }}',
            'display' => array(
            {%- for field in fields %}

                '{{ field }}',

            {%- endfor %}

            ),
            'actions' => array(),
        );

        $show = array(
            'title'   => 'Show {{ entity }}',
        );

        $filter = array(
            'title'   => 'Filter',
            'fields' => array(
            {%- for field in fields %}

                '{{ field }}' => array(
                    'type'    => 'text',
                    'compare' => '=',
                    'label'   => '{{ field }}',
                    ),

            {%- endfor %}

            )
        );

        $this
            ->setClass('{{ namespace }}\Entity\{{ entity }}')
            ->setModel('{{ model_name }}')
            ->setCoreTheme('CoregenThemeBootstrapBundle')
            ->setLayoutTheme('ThemeBootstrapBundle')
            ->setRoute('{{ route_name }}')
            ->setActions($actions)
            ->setList($list)
            ->setFields($fields)
            ->setForm($form)
            ->setEdit($edit)
            ->setNew($new)
            ->setShow($show)
            ->setFilter($filter)
            ;

    }




}
