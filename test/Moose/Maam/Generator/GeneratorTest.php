<?php

namespace Moose\Maam\Generator;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Xpmock\TestCase;

/**
 * Tests the Generator. All tests that invoke the ->generate() method must run in a separate process, as including the
 * same class file twice will result in a PHP redeclaration error.
 */
class GeneratorTest extends TestCase
{
    protected static $generationPath;
    protected static $assetDir;

    /**
     * @runInSeparateProcess
     */
    public function testGenerateGeneratesClassFilesForPhpFilesInTheSourcePath()
    {
        $this->assertTrue(!file_exists(self::$generationPath . '/MaamTest/Person.php'));
        $this->assertTrue(!file_exists(self::$generationPath . '/classmap.php'));

        $generator = new Generator(self::$assetDir, self::$generationPath);
        $classMap = $generator->generate();

        $this->assertSame(
            file_get_contents(self::$assetDir . '/MaamTest/Person.expected-output.txt'),
            file_get_contents(self::$generationPath . '/MaamTest/Person.php')
        );

        $this->assertSame(
            "<?php\nreturn " . var_export($classMap, true) . ";",
            file_get_contents(self::$generationPath . '/classmap.php')
        );

        $this->assertArrayHasKey('MaamTest\\Person', $classMap);
        $this->assertSame(
            realpath(self::$generationPath . '/MaamTest/Person.php'),
            realpath($classMap['MaamTest\\Person'])
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testDoesNothingForClassesWithNoMaamAnnotations()
    {
        $this->assertTrue(!file_exists(self::$generationPath . '/NoAnnotationsHere.php'));

        $generator = new Generator(self::$assetDir, self::$generationPath);
        $generator->generate();

        $this->assertTrue(!file_exists(self::$generationPath . '/NoAnnotationsHere.php'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testPlaysNicelyWithDoctrineOrmEntityAnnotations()
    {
        $this->assertTrue(!file_exists(self::$generationPath . '/MaamTest/DoctrineOrmEntity.php'));

        $generator = new Generator(self::$assetDir, self::$generationPath);
        $classMap = $generator->generate();

        $this->assertSame(
            file_get_contents(self::$assetDir . '/MaamTest/DoctrineOrmEntity.expected-output.txt'),
            file_get_contents(self::$generationPath . '/MaamTest/DoctrineOrmEntity.php')
        );

        $this->assertSame(
            "<?php\nreturn " . var_export($classMap, true) . ";",
            file_get_contents(self::$generationPath . '/classmap.php')
        );

        $this->assertArrayHasKey('MaamTest\\DoctrineOrmEntity', $classMap);
        $this->assertSame(
            realpath(self::$generationPath . '/MaamTest/DoctrineOrmEntity.php'),
            realpath($classMap['MaamTest\\DoctrineOrmEntity'])
        );
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
        if (file_exists(self::$generationPath)) {
            rmdir(self::$generationPath);
        }
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
