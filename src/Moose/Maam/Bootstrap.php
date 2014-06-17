<?php

namespace Moose\Maam;

use Composer\Autoload\ClassLoader;

class Bootstrap
{
    const MODE_DEVELOPMENT = 1;
    const MODE_PRODUCTION = 2;

    /**
     * Bootstraps Maam by generating new class files for all PHP files in the supplied sourcePath
     * and adds the new classmap to Composer's loader.
     *
     * @param ClassLoader $loader Composer's class loader.
     * @param string $sourcePath Path to application's PHP source files.
     * @param int $mode "dev" to generate class files, "production" to assume they are already there.
     * @return void
     */
    public function bootstrap(ClassLoader $loader, $sourcePath, $mode = self::MODE_PRODUCTION)
    {
        if ($mode === self::MODE_DEVELOPMENT) {
            /*
             * Runs the Maam compiler. Needs to be run as a separate PHP process so that its loading
             * of the original PHP classes as part of generation do not interfere with injection of
             * the generated classes' classMap into Composer.
             */
            exec(
                PHP_BINARY . ' ' . realpath(__DIR__ . '/../../../bin/maam.php') . ' "' . $sourcePath . '"',
                $output,
                $exitCode
            );

            // Successful generation should result in $exitCode 0.
            if ($exitCode !== 0) {
                throw new \RuntimeException("Maam compilation failed! Error output: " . implode("\n", $output));
            }
        }

        $loader->addClassMap(require __DIR__ . '/../../../generated/classmap.php');
    }
}
