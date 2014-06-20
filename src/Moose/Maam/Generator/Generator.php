<?php
/**
 * Maam
 */
namespace Moose\Maam\Generator;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Moose\Maam\Annotation\MaamAnnotationInterface;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Generator for Maam'ed files
 */
class Generator
{
    /**
     * Path where files should be written.
     *
     * @var string
     */
    protected $generationPath;

    /**
     * Path to application source files.
     *
     * @var string
     */
    protected $sourcePath;

    public function __construct($sourcePath, $generationPath)
    {
        AnnotationRegistry::registerLoader(
            function ($className) {
                return class_exists($className);
            }
        );

        $this->sourcePath = $sourcePath;
        $this->generationPath = $generationPath;
    }

    /**
     * Recursively checks the source path for files to generate, then generates them to the generation path.
     *
     * @return array The Composer classmap that was written.
     */
    public function generate()
    {
        $classMap = [];
        $finder = new Finder();
        $finder->in($this->sourcePath)->files()->name('*.php');

        /** @var SplFileInfo $object */
        foreach ($finder as $file) {
            $result = $this->generateClass($file->getPathname());
            if (!empty($result)) {
                $classMap[$result['class']] = $result['path'];
            }
        }

        file_put_contents(
            $this->generationPath . '/classmap.php',
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

        /** @var \ReflectionProperty $property */
        foreach ($reflectionClass->getProperties() as $property) {
            $annotations = $annotationReader->getPropertyAnnotations($property);

            if (count($annotations) === 0) {
                return null;
            }

            $newMethods = array_merge($newMethods, $this->generateMethods($annotations, $property->getName()));
        }

        return [
            'class' => $reflectionClass->getName(),
            'path' => $this->writeNewCode($filePath, $newMethods)
        ];
    }

    protected function generateMethods($annotations, $propertyName)
    {
        $methods = [];

        /** @var \Doctrine\Common\Annotations\Annotation $annotation */
        foreach ($annotations as $annotation) {
            if ($annotation instanceof MaamAnnotationInterface) {
                $generationMethodName = 'generate' . $annotation->getShortName();
                $methods[] = call_user_func([$this, $generationMethodName], $propertyName);
            }
        }

        return $methods;
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
     * Writes both the getter and the setter.
     *
     * @param string $propertyName The name of the property
     * @return string
     */
    protected function generateBoth($propertyName)
    {
        return $this->generateGetter($propertyName) . "\n\n" . $this->generateSetter($propertyName);
    }

    /**
     * Combines all of the new methods and writes a new classfile with the getters and setters present.
     *
     * @param string $filePath Target file
     * @param array $newMethods Code for the new methods
     * @return string
     */
    protected function writeNewCode($filePath, array $newMethods)
    {
        $currentCode = file_get_contents($filePath);
        $newCode = "\n" . implode("\n\n", $newMethods);
        $targetFile = str_replace($this->sourcePath, $this->generationPath, $filePath);
        $targetFileDir = dirname($targetFile);

        if (!file_exists($targetFileDir)) {
            mkdir($targetFileDir, 0755, true);
        }

        file_put_contents(
            $targetFile,
            substr_replace($currentCode, $newCode, strrpos($currentCode, '}'), 1) . "}\n"
        );

        return $targetFile;
    }
}
