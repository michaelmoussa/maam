<?php

namespace Moose\Maam;

use Composer\Autoload\ClassLoader;
use RuntimeException;

class Maam
{
    const MODE_DEVELOPMENT = 1;
    const MODE_PRODUCTION = 2;

    /**
     * Path to application's Composer autoload.php
     *
     * @var string
     */
    protected $applicationAutoloadPath;

    /**
     * Path to the application's source code.
     *
     * @var string
     */
    protected $applicationSourcePath;

    /**
     * Path to where Maam processed files should be written
     *
     * @var string
     */
    protected $generationPath;

    /**
     * Maam run mode.
     *
     * @var int
     */
    protected $mode = self::MODE_PRODUCTION;

    /**
     * Path to the root directory of the Maam module.
     *
     * @var string
     */
    protected $moduleRoot;

    public function __construct($mode = self::MODE_PRODUCTION)
    {
        $this->setMode($mode);
        $this->moduleRoot = __DIR__ . '/../../..';
    }

    /**
     * Initializes Maam by generating new class files for all PHP files in the supplied sourcePath
     * and adds the new classmap to Composer's loader.
     *
     * @param ClassLoader $loader Composer's class loader.
     * @throws RuntimeException
     * @return void
     */
    public function init(ClassLoader $loader)
    {
        if ($this->getMode() === self::MODE_DEVELOPMENT) {
            $this->runGeneratorCommand();
        }

        $loader->addClassMap(require $this->getGenerationPath() . '/classmap.php');
    }

    /**
     * Sets the path to the application's Composer autoload.php
     *
     * @param string $applicationAutoloadPath
     * @throws RuntimeException
     * @return void
     */
    public function setApplicationAutoloadPath($applicationAutoloadPath)
    {
        if (!file_exists($applicationAutoloadPath)) {
            throw new RuntimeException('Invalid autoload path (' . $applicationAutoloadPath . ') does not exist');
        }
        $this->applicationAutoloadPath = $applicationAutoloadPath;
    }

    /**
     * Returns the path to the application's Composer autoload.php. If not previously set, it defaults to
     * two directories up from the Maam module root directory.
     *
     * @return string
     */
    public function getApplicationAutoloadPath()
    {
        if (empty($this->applicationAutoloadPath)) {
            $this->setApplicationAutoloadPath(realpath($this->moduleRoot . '/../../autoload.php'));
        }
        return $this->applicationAutoloadPath;
    }

    /**
     * Sets the path to the application's source directory.
     *
     * @param string $applicationSourcePath
     * @throws RuntimeException
     * @return void
     */
    public function setApplicationSourcePath($applicationSourcePath)
    {
        if (!is_dir($applicationSourcePath)) {
            throw new RuntimeException(
                'Invalid application source path (' . $applicationSourcePath . ') does not exist'
            );
        }
        $this->applicationSourcePath = $applicationSourcePath;
    }

    /**
     * Returns the path to the application's source files. If not previously set, it defaults to the "src/" directory
     * three levels up from the Maam module root directory.
     *
     * @return string
     */
    public function getApplicationSourcePath()
    {
        if (empty($this->applicationSourcePath)) {
            $this->setApplicationSourcePath(realpath($this->moduleRoot . '/../../../src'));
        }
        return $this->applicationSourcePath;
    }

    /**
     * Sets the directory to which processed classes should be written.
     *
     * @param string $generationPath
     * @throws RuntimeException
     * @return void
     */
    public function setGenerationPath($generationPath)
    {
        if (!is_dir($generationPath)) {
            throw new RuntimeException('Directory ' . $generationPath . ' not found! Generation directory must exist.');
        } elseif (!is_writable($generationPath)) {
            throw new RuntimeException('Directory ' . $generationPath . ' is not writable!');
        }
        $this->generationPath = $generationPath;
    }

    /**
     * Returns the path to where processed classes should be written. If not previously set, defaults to
     * the "data/maam" directory 3 levels up from the module root.
     *
     * @return string
     */
    public function getGenerationPath()
    {
        if (empty($this->generationPath)) {
            $this->setGenerationPath($this->moduleRoot . '/../../../data/maam');
        }
        return $this->generationPath;
    }

    /**
     * Sets the mode for Maam to run in - production or development.
     *
     * @param int $mode
     * @throws RuntimeException
     * @return void
     */
    public function setMode($mode)
    {
        if ($mode !== self::MODE_DEVELOPMENT && $mode !== self::MODE_PRODUCTION) {
            throw new RuntimeException('Invalid Maam mode (' . $mode . ') - use Maam:: constant to set.');
        }
        $this->mode = $mode;
    }

    /**
     * Returns the mode for Maam to run in.
     *
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Prepares the shell command that is used to generate classes based on Maam annotations and runs it.
     *
     * @throws RuntimeException
     * @return void
     **/
    protected function runGeneratorCommand()
    {
        /*
         * Runs the Maam compiler. Needs to be run as a separate PHP process so that its loading
         * of the original PHP classes as part of generation do not interfere with injection of
         * the generated classes' classMap into Composer.
         */
        $command = sprintf(
            '%s "%s" "%s" "%s" "%s"',
            PHP_BINARY,
            realpath($this->moduleRoot . '/bin/maam.php'),
            realpath($this->getApplicationAutoloadPath()),
            realpath($this->getApplicationSourcePath()),
            realpath($this->getGenerationPath())
        );

        exec($command, $output, $exitCode);

        // Successful generation should result in $exitCode 0.
        if ($exitCode !== 0) {
            throw new RuntimeException('Maam compilation failed! Error output: ' . implode("\n", (array) $output));
        }
    }
}
