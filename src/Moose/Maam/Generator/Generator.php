<?php
/**
 * Maam
 */
namespace Moose\Maam\Generator;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

/**
 * Generator for Maam'ed files
 */
class Generator
{
    /**
     * Constructor
     */
    public function __construct()
    {
        AnnotationRegistry::registerAutoloadNamespace(
            'Moose\Maam\Annotation',
            [__DIR__ . '/../../../']
        );
    }

    /**
     * Recursively checks the source path for files to generate, then generates them.
     *
     * @param string $sourcePath Path to the application source files containing the Maam annotations.
     * @return array The Composer classmap that was written.
     */
    public function generate($sourcePath)
    {
        $generatedDir = __DIR__ . '/../../../../generated';
        $classMap = [];
        $objects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath),
            RecursiveIteratorIterator::SELF_FIRST
        );

        /** @var SplFileInfo $object */
        foreach ($objects as $object) {
            if ($object->isFile() && $object->getExtension() === 'php') {
                $result = $this->generateClass($object->getPathname());
                if (!empty($result)) {
                    $classMap[$result['class']] = $result['path'];
                }
            }
        }

        if (!file_exists($generatedDir)) {
            mkdir($generatedDir, 0777, true);
        }

        file_put_contents(
            $generatedDir . '/classmap.php',
            "<?php\nreturn " . var_export($classMap, true) . ";"
        );

        return $classMap;
    }

    /**
     * Processes Maam annotations in class files and writes new classes with the generated
     * getters and setters.
     *
     * @param string $filePath Path to the file to generate
     * @return array|null Array consisting of the class and path if generated, or null if not because there were no
     *                    Maam annotations.
     */
    protected function generateClass($filePath)
    {
        $newMethods = [];
        $reflectionClass = $this->getReflectionClass($filePath);
        $annotationReader = new AnnotationReader();

        foreach ($reflectionClass->getProperties() as $property) {
            $annotations = $annotationReader->getPropertyAnnotations($property);

            if (count($annotations) === 0) {
                return null;
            }

            /** @var \Doctrine\Common\Annotations\Annotation $annotation */
            foreach ($annotations as $annotation) {
                $annotationShortName = basename(strtr(get_class($annotation), "\\", "/"));
                $generationMethodName = 'generate' . $annotationShortName;
                $propertyName = $property->getName();
                $newMethods[] = call_user_func([$this, $generationMethodName], $propertyName);
            }
        }

        return [
            'class' => $reflectionClass->getName(),
            'path' => $this->writeNewCode($filePath, $reflectionClass->getName(), $newMethods)
        ];
    }

    /**
     * Returns an instance of a ReflectionClass corresponding to the
     * class file located in $filePath.
     *
     * @param string $filePath
     * @return ReflectionClass
     */
    protected function getReflectionClass($filePath)
    {
        include($filePath);
        $classes = get_declared_classes();
        return new ReflectionClass(end($classes));
    }

    /**
     * Writes the getter.
     *
     * @param string $propertyName The name of the property
     * @return string
     */
    protected function generateGetter($propertyName)
    {
        $methodSuffix = ucfirst($propertyName);

        return <<<HEREDOC
    /**
     * Sets the ${propertyName}.
     *
     * @param mixed \$${propertyName}
     * @return void
     */
    public function set${methodSuffix}(\$${propertyName})
    {
        \$this->${propertyName} = \$${propertyName};
    }
HEREDOC;
    }

    /**
     * Writes the setter.
     *
     * @param string $propertyName The name of the property
     * @return string
     */
    protected function generateSetter($propertyName)
    {
        $methodSuffix = ucfirst($propertyName);

        return <<<HEREDOC
    /**
     * Gets the ${propertyName}.
     *
     * @return mixed
     */
    public function get${methodSuffix}()
    {
        return \$this->${propertyName};
    }
HEREDOC;
    }

    /**
     * Combines all of the new methods and writes a new classfile with the getters and setters present.
     *
     * @param string $filePath Target file
     * @param string $className FQCN of the class being Maam'ed
     * @param array $newMethods Code for the new methods
     * @return string
     */
    protected function writeNewCode($filePath, $className, array $newMethods)
    {
        $generateTargetPath = __DIR__ . '/../../../../generated';
        $classRelativePath = strtr($className, '\\', '/') . '.php';
        $classFileTargetPath = $generateTargetPath . '/' . dirname($classRelativePath);

        $currentCode = file_get_contents($filePath);
        $newCode = "\n" . implode("\n\n", $newMethods);
        $targetFile = $generateTargetPath . '/' . $classRelativePath;

        if (!file_exists($classFileTargetPath)) {
            mkdir($classFileTargetPath, 0777, true);
        }

        file_put_contents(
            $targetFile,
            substr_replace($currentCode, $newCode, strrpos($currentCode, '}'), 1) . "}\n"
        );

        return $targetFile;
    }
}
