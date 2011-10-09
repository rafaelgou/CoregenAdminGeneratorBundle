# Coregen Admin Generator

Symfony2 Admin Generator based on symfony 1.4 version

## deps

    [CoregenAdminGeneratorBundle]
        git=git://github.com/rafaelgou/CoregenAdminGeneratorBundle.git
        target=bundles/Coregen/AdminGeneratorBundle

(add to deps file and run `./bin/vendors install`)

## AppKernel

      //...

      new Coregen\AdminGeneratorBundle\CoregenAdminGeneratorBundle(),

      //...


## autoload

      //...

      'Coregen'            => __DIR__.'/../vendor/bundles',

      //...

