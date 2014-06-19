<?php

namespace Moose\Maam {

    use Xpmock\TestCase;

    class MaamTest extends TestCase
    {
        public function testInitializerSkipsGenerationInProductionMode()
        {
            $loader = $this->mock('Composer\Autoload\ClassLoader')
                ->addClassMap(['someclass' => 'somefile'])
                ->new();
            $maam = new Maam();
            $maam->init($loader, './source-path', Maam::MODE_PRODUCTION);
        }

        public function testInitializerGeneratesClassFilesInDevelopmentMode()
        {
            $loader = $this->mock('Composer\Autoload\ClassLoader')
                ->addClassMap(['someclass' => 'somefile'])
                ->new();
            $maam = new Maam();
            $maam->init($loader, './source-path', Maam::MODE_DEVELOPMENT);
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
            $maam = new Maam();
            $maam->init($loader, './this-one-should-fail', Maam::MODE_DEVELOPMENT);
        }

        protected function setUp()
        {
            $generatedDir = __DIR__ . '/../../../generated';

            if (!file_exists($generatedDir)) {
                mkdir($generatedDir, 0777, true);
            }

            file_put_contents(
                $generatedDir . '/classmap.php',
                "<?php\n" .
                "return ['someclass' => 'somefile'];"
            );
        }

        protected function tearDown()
        {
            unlink(__DIR__ . '/../../../generated/classmap.php');
        }
    }

    function exec($command, &$output, &$exitCode)
    {
        if (strpos($command, './this-one-should-fail') !== false) {
            $exitCode = 1;
            $output = ['failure output'];
        } else {
            $expectedCommand = PHP_BINARY . ' ' . realpath(__DIR__ . '/../../../bin/maam.php') . ' "./source-path"';

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
}
