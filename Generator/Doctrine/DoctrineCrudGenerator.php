<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license ingeneratoration, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Coregen\AdminGeneratorBundle\Generator\Doctrine;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Generates a generator class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 */
class DoctrineCrudGenerator extends Generator
{
    private $filesystem;
    private $skeletonDir;
    private $bundle;
    private $entity;
    private $metadata;
    private $className;
    private $classPath;
    private $routePrefix;
    private $routeNamePrefix;
    private $format;
    private $dataToGenerate;

    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getClassPath()
    {
        return $this->classPath;
    }

    /**
     * Generates the entity generator class if it does not exist.
     *
     * @param BundleInterface $bundle The bundle in which to create the class
     * @param string $entity The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix)
    {
        $this->bundle          = $bundle;
        $this->entity          = $entity;
        $this->routePrefix     = $routePrefix;
        $this->routeNamePrefix = str_replace('/', '_', $this->routePrefix);
        $this->metadata        = $metadata;
        $this->setFormat($format);
        $parts                 = explode('\\', $this->entity);
        $this->className       = array_pop($parts);
        $this->entityNamespace = implode('\\', $parts);

        if (count($this->metadata->identifier) > 1) {
            throw new \RuntimeException('The generator class generator does not support entity classes with multiple primary keys.');
        }

        $this->generateGeneratorClass();
        $this->generateControllerClass();
        $this->generateConfiguration();
        $this->generateTranslation();
    }

    protected function setDataToGenerate()
    {
        $parts = explode('\\', $this->entity);
        array_pop($parts);

        $this->dataToGenerate = array(
            'dir'               => $this->skeletonDir,
            'fields'            => $this->getFieldsFromMetadata($this->metadata),
            'namespace'         => $this->bundle->getNamespace(),
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'entity_namespace'  => $this->entityNamespace,
            'model_name'        => $this->bundle->getName() . ':' . $this->entity,
            'form_type_name'    => $this->bundle->getNamespace() .  '\\Form\\' . $this->entity . 'Type',
            'route_name'        => strtolower($this->entity),
//            'actions'           => $this->actions,
            'format'            => $this->format,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
       );

    }

    /**
     * Sets the configuration format.
     *
     * @param string $format The configuration format
     */
    private function setFormat($format)
    {
        switch ($format) {
            case 'yml':
            case 'xml':
            case 'php':
            case 'annotation':
                $this->format = $format;
                break;
            default:
                $this->format = 'yml';
                break;
        }
    }

    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param ClassMetadataInfo $metadata
     * @return array $fields
     */
    private function getFieldsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fields = (array) $metadata->fieldNames;

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            $fields = array_diff($fields, $metadata->identifier);
        }

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] !== ClassMetadataInfo::ONE_TO_MANY) {
                $fields[] = $fieldName;
            }
        }

        return $fields;
    }

    /**
     * Generates the generator class only.
     */
    private function generateGeneratorClass()
    {
        $this->setDataToGenerate();

        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Generator/%s/%s.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (file_exists($target)) {
            throw new \RuntimeException('Unable to generate the generator as it already exists.');
        }

        $this->renderFile($this->skeletonDir, 'generator.php', $target, $this->dataToGenerate);
    }

    /**
     * Generates the controller class only.
     */
    private function generateControllerClass()
    {
        $this->setDataToGenerate();

        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%sController.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile($this->skeletonDir, 'controller.php', $target, $this->dataToGenerate);
    }

    /**
     * Generates the routing configuration.
     *
     */
    private function generateConfiguration()
    {
        $this->setDataToGenerate();

        if (!in_array($this->format, array('yml', 'xml', 'php'))) {
            return;
        }

        $target = sprintf(
            '%s/Resources/config/routing/%s.%s',
            $this->bundle->getPath(),
            strtolower(str_replace('\\', '_', $this->entity)),
            $this->format
        );

        $this->renderFile($this->skeletonDir, 'config/routing.'.$this->format, $target, $this->dataToGenerate);
    }

    /**
     * Generates the translations.
     *
     */
    private function generateTranslation()
    {
        $this->setDataToGenerate();
        $targets = array(
            'en.yml',
            'pt_BR.yml'
        );

        foreach ($targets as $tg) {
            $target = sprintf(
                '%s/Resources/translations/%s.%s',
                $this->bundle->getPath(),
                $this->bundle->getName() . $this->entity,
                $tg
            );

            $this->renderFile($this->skeletonDir, 'translations/messages.'.$tg, $target, $this->dataToGenerate);
        }
    }

}
