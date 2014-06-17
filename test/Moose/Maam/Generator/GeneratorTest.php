<?php

namespace Moose\Maam\Generator;

use Xpmock\TestCase;

class GeneratorTest extends TestCase
{
    public function testGenerateGeneratesClassFilesForPhpFilesInTheSourcePath()
    {
        $this->assertTrue(!file_exists($this->getGenerationDir() . '/MaamTest/Person.php'));
        $this->assertTrue(!file_exists($this->getGenerationDir() . '/MaamTest/NoAnnotationsHere.php'));
        $this->assertTrue(!file_exists($this->getGenerationDir() . '/classmap.php'));

        $assetDir = __DIR__ . '/../../../assets';
        $generator = new Generator();
        $generator->generate($assetDir);

        $this->assertSame(
            file_get_contents($assetDir . '/Person.expected-output.txt'),
            file_get_contents($this->getGenerationDir() . '/MaamTest/Person.php')
        );

        $classmap = include $this->getGenerationDir() . '/classmap.php';

        $this->assertSame(1, count($classmap));

        $className = array_keys($classmap)[0];

        $this->assertSame('MaamTest\Person', $className);
        $this->assertSame(
            realpath($this->getGenerationDir() . '/MaamTest/Person.php'),
            realpath($classmap[$className])
        );

        $this->assertTrue(!file_exists($this->getGenerationDir() . '/MaamTest/NoAnnotationsHere.php'));
    }

    protected function setUp()
    {
        if (file_exists($this->getGenerationDir() . '/MaamTest/Person.php')) {
            unlink($this->getGenerationDir() . '/MaamTest/Person.php');
        }
        if (file_exists($this->getGenerationDir() . '/classmap.php')) {
            unlink($this->getGenerationDir() . '/classmap.php');
        }
    }

    protected function getGenerationDir()
    {
        return __DIR__ . '/../../../../generated';
    }
}
