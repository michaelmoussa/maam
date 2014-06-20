<?php

namespace Moose\Maam\Generator;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Xpmock\TestCase;

class GeneratorTest extends TestCase
{
    protected static $generationPath;
    protected static $assetDir;

    public function testGenerateGeneratesClassFilesForPhpFilesInTheSourcePath()
    {
        $this->assertTrue(!file_exists(self::$generationPath . '/MaamTest/Person.php'));
        $this->assertTrue(!file_exists(self::$generationPath . '/NoAnnotationsHere.php'));
        $this->assertTrue(!file_exists(self::$generationPath . '/classmap.php'));

        $generator = new Generator(self::$assetDir, self::$generationPath);
        $generator->generate();

        $this->assertSame(
            file_get_contents(self::$assetDir . '/Person.expected-output.txt'),
            file_get_contents(self::$generationPath . '/MaamTest/Person.php')
        );

        $classmap = include self::$generationPath . '/classmap.php';

        $this->assertSame(1, count($classmap));

        $className = array_keys($classmap)[0];

        $this->assertSame('MaamTest\Person', $className);
        $this->assertSame(
            realpath(self::$generationPath . '/MaamTest/Person.php'),
            realpath($classmap[$className])
        );

        $this->assertTrue(!file_exists(self::$generationPath . '/NoAnnotationsHere.php'));
    }

    public static function setUpBeforeClass()
    {
        self::$generationPath = __DIR__ . '/../../../data/maam';
        self::$assetDir = __DIR__ . '/../../../assets';

        if (!file_exists(self::$generationPath)) {
            mkdir(self::$generationPath, 0755, true);
        }
    }

    public static function tearDownAfterClass()
    {
        rmdir(self::$generationPath);
    }

    protected function tearDown()
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(self::$generationPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var $fileinfo \SplFileInfo */
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        if (file_exists(self::$generationPath . '/classmap.php')) {
            unlink(self::$generationPath . '/classmap.php');
        }
    }
}
