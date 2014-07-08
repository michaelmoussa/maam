<?php

namespace Moose\Maam {

    class MaamTest extends \PHPUnit_Framework_TestCase
    {
        public static $generationPath;

        /**
         * @var \Composer\Autoload\ClassLoader
         */
        protected $loader;

        /**
         * @var Maam
         */
        protected $maam;

        public function testInitializerSkipsGenerationInProductionMode()
        {
            $this->maam->init($this->loader);

            $classMap = $this->loader->getClassMap();

            $this->assertArrayHasKey('someclass', $classMap);
            $this->assertSame('somefile', $classMap['someclass']);
        }

        public function testInitializerGeneratesClassFilesInDevelopmentMode()
        {
            unlink(self::$generationPath . '/classmap.php');

            $this->maam->setMode(Maam::MODE_DEVELOPMENT);
            $this->maam->init($this->loader);
        }

        /**
         * @expectedException \RuntimeException
         * @expectedExceptionMessage Maam compilation failed! Error output: failure output
         */
        public function testDiesWithErrorWhenMaamCallFails()
        {
            $this->maam->setMode(Maam::MODE_DEVELOPMENT);
            $this->maam->setApplicationSourcePath(__DIR__);
            $this->maam->init($this->loader);
        }

        /**
         * @expectedException \RuntimeException
         * @expectedExceptionMessage Invalid application source path (invalid-application-source-path) does not exist
         */
        public function testExceptionIsThrownIfAttemptingToSetNonexistentApplicationSourcePath()
        {
            $this->maam->setApplicationSourcePath('invalid-application-source-path');
        }

        /**
         * @expectedException \RuntimeException
         * @expectedExceptionMessage Invalid autoload path (invalid-autoload-path) does not exist
         */
        public function testExceptionIsThrownIfAttemptingToSetNonexistentAutoloadPath()
        {
            $this->maam->setApplicationAutoloadPath('invalid-autoload-path');
        }

        /**
         * @expectedException \RuntimeException
         * @expectedExceptionMessage Directory invalid-generation-path not found! Generation directory must exist.
         */
        public function testExceptionIsThrownIfAttemptingToSetNonexistentGenerationPath()
        {
            $this->maam->setGenerationPath('invalid-generation-path');
        }

        /**
         * @expectedException \RuntimeException
         * @expectedExceptionMessage Directory valid-but-not-writable is not writable!
         */
        public function testExceptionIsThrownIfAttemptingToSetUnwritableGenerationPath()
        {
            $this->maam->setGenerationPath('valid-but-not-writable');
        }

        /**
         * @expectedException \RuntimeException
         * @expectedExceptionMessage Invalid Maam mode (3) - use Maam:: constant to set.
         */
        public function testExceptionIsThrownIfAttemptingToSetInvalidMode()
        {
            $this->maam->setMode(3);
        }

        public function testGetApplicationAutoloadPathDefaultsToTypicalComposerAutoloadFileLocation()
        {
            $maam = new Maam();
            $this->assertSame(
                realpath(__DIR__ . '/../../../src') . '/Moose/Maam/../../../../../autoload.php',
                $maam->getApplicationAutoloadPath()
            );
        }

        public function testGetApplicationSourcePathDefaultsToSrcDirInApplicationRoot()
        {
            $maam = new Maam();
            $this->assertSame(
                realpath(__DIR__ . '/../../../src') . '/Moose/Maam/../../../../../../src',
                $maam->getApplicationSourcePath()
            );
        }

        public function testGetGenerationPathDefaultsToDataMaamDirInApplicationRoot()
        {
            $maam = new Maam();
            $this->assertSame(
                realpath(__DIR__ . '/../../../src') . '/Moose/Maam/../../../../../../cache/maam',
                $maam->getGenerationPath()
            );
        }

        public static function setUpBeforeClass()
        {
            $path = __DIR__ . '/../../cache/maam';

            if (!\file_exists($path)) {
                mkdir($path, 0755, true);
            }

            self::$generationPath = \realpath($path);
        }

        public static function tearDownAfterClass()
        {
            if (file_exists(self::$generationPath . '/classmap.php')) {
                unlink(self::$generationPath . '/classmap.php');
            }
        }

        protected function setUp()
        {
            $this->loader = include __DIR__ . '/../../../vendor/autoload.php';

            $this->maam = new Maam();
            $this->maam->setGenerationPath(self::$generationPath);
            $this->maam->setApplicationSourcePath(__DIR__ . '/../../assets');
            $this->maam->setApplicationAutoloadPath(__DIR__ . '/../../../vendor/autoload.php');

            file_put_contents(
                self::$generationPath . '/classmap.php',
                "<?php\n" .
                "return ['someclass' => 'somefile'];"
            );
        }

        protected function tearDown()
        {
            $this->loader = null;
            $this->maam = null;
        }
    }

    function exec($command, &$output, &$exitCode)
    {
        if (strpos($command, '/test/Moose/Maam"') !== false) {
            $exitCode = 1;
            $output = ['failure output'];
        } else {
            $expectedCommand = sprintf(
                '%s "%s" "%s" "%s" "%s"',
                PHP_BINARY,
                realpath(__DIR__ . '/../../../bin/maam.php'),
                realpath(__DIR__ . '/../../../vendor/autoload.php'),
                realpath(__DIR__ . '/../../assets'),
                MaamTest::$generationPath
            );

            if ($command !== $expectedCommand) {
                throw new \PHPUnit_Framework_Exception(
                    "Wrong exec(...) call!\n" .
                    'Expected: ' . $expectedCommand . "\n" .
                    'Got: ' . $command
                );
            }

            \exec($command, $output, $exitCode);
        }
    }

    /**
     * Some paths are expected to not exist when running the tests. Check against a whitelist and return whether
     * or not we can just return the path itself.
     *
     * @param string $path
     * @param array $values
     * @return bool
     */
    function shouldReturnSelf($path, $values)
    {
        foreach ($values as $value) {
            if (strpos($path, $value) !== false) {
                return true;
            }
        }

        return false;
    }

    function file_exists($path)
    {
        return shouldReturnSelf($path, ['src/Moose/Maam/../../../../../autoload.php']) ? $path : \file_exists($path);
    }

    function is_dir($path)
    {
        $allowed = [
            'valid-but-not-writable',
            'src/Moose/Maam/../../../../../../src',
            'src/Moose/Maam/../../../../../../cache/maam'
        ];

        return shouldReturnSelf($path, $allowed) ? $path : \is_dir($path);
    }

    function is_writable($path)
    {
        return shouldReturnSelf($path, ['src/Moose/Maam/../../../../../../cache/maam']) ? $path : \is_writable($path);
    }

    function realpath($path)
    {
        $allowed = [
            'src/Moose/Maam/../../../../../../src',
            'src/Moose/Maam/../../../../../../cache/maam',
            'src/Moose/Maam/../../../../../autoload.php'
        ];

        return shouldReturnSelf($path, $allowed) ? $path : \realpath($path);
    }
}
