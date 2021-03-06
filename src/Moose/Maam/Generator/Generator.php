<?php
/**
 * Maam
 */
namespace Moose\Maam\Generator;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Moose\Maam\Annotation\FluentAware;
use Moose\Maam\Annotation\MaamAnnotation;
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

    /**
     * Constructor
     *
     * @param string $sourcePath
     * @param string $generationPath
     */
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

        /** @var SplFileInfo $file */
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

        if (count($newMethods) === 0) {
            return null;
        }

        return [
            'class' => $reflectionClass->getName(),
            'path' => $this->writeNewCode($filePath, $newMethods)
        ];
    }

    /**
     * Uses generate<shortname> methods to generate the new method code.
     *
     * @param \Doctrine\Common\Annotations\Annotation[] $annotations
     * @param string $propertyName
     * @return array
     */
    protected function generateMethods($annotations, $propertyName)
    {
        $methods = [];

        foreach ($annotations as $annotation) {
            if ($annotation instanceof MaamAnnotation) {
                $generationMethodName = 'generate' . $annotation->getShortName();
                $methods[] = call_user_func([$this, $generationMethodName], $propertyName, $annotation);
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
        require_once $filePath;
        $classes = get_declared_classes();
        return new ReflectionClass(end($classes));
    }

    /**
     * Writes the isser.
     *
     * @param string $propertyName The name of the property
     * @return string
     */
    protected function generateDirect($propertyName)
    {
        return <<<HEREDOC
    /**
     * Gets the ${propertyName}.
     *
     * @return bool
     */
    public function {$propertyName}()
    {
        return \$this->${propertyName};
    }
HEREDOC;
    }

    /**
     * Writes the isser.
     *
     * @param string $propertyName The name of the property
     * @return string
     */
    protected function generateIsser($propertyName)
    {
        $methodSuffix = ucfirst($propertyName);

        return <<<HEREDOC
    /**
     * Gets the ${propertyName}.
     *
     * @return bool
     */
    public function is${methodSuffix}()
    {
        return \$this->${propertyName};
    }
HEREDOC;
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
     * @param FluentAware $annotation
     * @return string
     */
    protected function generateSetter($propertyName, FluentAware $annotation)
    {
        $setterGenerator = new Setter();
        return $setterGenerator->generate($propertyName, $annotation);
    }

    /**
     * Writes both the getter and the setter.
     *
     * @param string $propertyName The name of the property
     * @param FluentAware $annotation
     * @return string
     */
    protected function generateBoth($propertyName, FluentAware $annotation)
    {
        return $this->generateGetter($propertyName) . "\n\n" . $this->generateSetter($propertyName, $annotation);
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
