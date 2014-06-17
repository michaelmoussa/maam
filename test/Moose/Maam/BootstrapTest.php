<?php

namespace Moose\Maam {

    use Xpmock\TestCase;

    class BootstrapTest extends TestCase
    {
        public function testBootstrapperSkipsGenerationInProductionMode()
        {
            $loader = $this->mock('Composer\Autoload\ClassLoader')
                ->addClassMap(['someclass' => 'somefile'])
                ->new();
            $bootstrap = new Bootstrap();
            $bootstrap->bootstrap($loader, './source-path', Bootstrap::MODE_PRODUCTION);
        }

        public function testBootstrapperGeneratesClassFilesInDevelopmentMode()
        {
            $loader = $this->mock('Composer\Autoload\ClassLoader')
                ->addClassMap(['someclass' => 'somefile'])
                ->new();
            $bootstrap = new Bootstrap();
            $bootstrap->bootstrap($loader, './source-path', Bootstrap::MODE_DEVELOPMENT);
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
            $bootstrap = new Bootstrap();
            $bootstrap->bootstrap($loader, './this-one-should-fail', Bootstrap::MODE_DEVELOPMENT);
        }

        protected function setUp()
        {
            file_put_contents(
                __DIR__ . '/../../../generated/classmap.php',
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
