<?php

namespace Moose\Maam {

    use Xpmock\TestCase;

    class MaamTest extends TestCase
    {
        public static $generationPath;

        /**
         * @var Maam
         */
        protected $maam;

        public function testInitializerSkipsGenerationInProductionMode()
        {
            $loader = $this->mock('Composer\Autoload\ClassLoader')
                ->addClassMap(['someclass' => 'somefile'])
                ->new();
            $this->maam->init($loader);
        }

        public function testInitializerGeneratesClassFilesInDevelopmentMode()
        {
            $loader = $this->mock('Composer\Autoload\ClassLoader')
                ->addClassMap(['someclass' => 'somefile'])
                ->new();

            $this->maam->setMode(Maam::MODE_DEVELOPMENT);
            $this->maam->init($loader);
        }

        /**
         * @expectedException \RuntimeException
         * @expectedExceptionMessage Maam compilation failed! Error output: failure output
         */
        public function testDiesWithErrorWhenMaamCallFails()
        {
            $loader = $this->mock('Composer\Autoload\ClassLoader')
                ->addClassMap($this->never())
                ->new();
            $this->maam->setMode(Maam::MODE_DEVELOPMENT);
            $this->maam->setApplicationSourcePath(__DIR__);
            $this->maam->init($loader);
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
            $this->assertSame(realpath(__DIR__ . '/../../../../../autoload.php'), $maam->getApplicationAutoloadPath());
        }

        public function testGetApplicationSourcePathDefaultsToSrcDirInApplicationRoot()
        {
            $maam = new Maam();
            $this->assertSame(realpath(__DIR__ . '/../../../../../../src'), $maam->getApplicationSourcePath());
        }

        public function testGetApplicationSourcePathDefaultsToDataMaamDirInApplicationRoot()
        {
            $maam = new Maam();
            $this->assertSame(realpath(__DIR__ . '/../../../../../../data/maam'), $maam->getGenerationPath());
        }

        public static function setUpBeforeClass()
        {
            self::$generationPath = realpath(__DIR__ . '/../../../data/maam');
            if (!\file_exists(self::$generationPath)) {
                mkdir(self::$generationPath, 0755, true);
            }

            file_put_contents(
                self::$generationPath . '/classmap.php',
                "<?php\n" .
                "return ['someclass' => 'somefile'];"
            );
        }

        public static function tearDownAfterClass()
        {
            unlink(self::$generationPath . '/classmap.php');
        }

        protected function setUp()
        {
            $this->maam = new Maam();
            $this->maam->setGenerationPath(self::$generationPath);
            $this->maam->setApplicationSourcePath(__DIR__ . '/../../../src');
            $this->maam->setApplicationAutoloadPath(__DIR__ . '/../../../vendor/autoload.php');
        }

        protected function tearDown()
        {
            $this->maam = null;
        }
    }

    function exec($command, &$output, &$exitCode)
    {
        if (strpos($command, '/src/Moose/Maam"') !== false) {
            $exitCode = 1;
            $output = ['failure output'];
        } else {
            $expectedCommand = sprintf(
                '%s "%s" "%s" "%s" "%s"',
                PHP_BINARY,
                realpath(__DIR__ . '/../../../bin/maam.php'),
                realpath(__DIR__ . '/../../../vendor/autoload.php'),
                realpath(__DIR__ . '/../../../src'),
                MaamTest::$generationPath
            );

            if ($command !== $expectedCommand) {
                throw new \PHPUnit_Framework_Exception(
                    "Wrong exec(...) call!\n" .
                    'Expected: ' . $expectedCommand . "\n" .
                    'Got: ' . $command
                );
            }

            $exitCode = 0;
        }
    }

    function file_exists($path)
    {
        return strpos($path, 'invalid') !== 0;
    }
    function is_dir($path)
    {
        return strpos($path, 'invalid') !== 0;
    }

    function is_writable($path)
    {
        return strpos($path, 'valid-but-not-writable') !== 0;
    }

    function realpath($path)
    {
        $path = str_replace('/test/', '/src/', $path);
        return $path;
    }
}
