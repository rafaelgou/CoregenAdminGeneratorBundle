<?php

namespace {{ namespace }}\Controller;

use Coregen\AdminGeneratorBundle\ORM\GeneratorController;
use {{ namespace }}\Generator\{{ entity }} as Generator;
use {{ namespace }}\Entity\{{ entity }};

/**
 * {{ entity }} controller.
 */
class {{ entity }}Controller extends GeneratorController
{
    /**
     * Configure method to load the generator and other stufs
     *
     * @return void
     */
    public function configure()
    {
        $generator = new Generator();
        $this->loadGenerator($generator);
    }
}
