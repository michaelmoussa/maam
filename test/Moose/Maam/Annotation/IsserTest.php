<?php

namespace Moose\Maam\Annotation;

use Xpmock\TestCase;

class IsserTest extends TestCase
{
    public function testIsserAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Isser'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Isser');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }

    public function testHasCorrectShortName()
    {
        $isser = $this->mock('Moose\Maam\Annotation\Isser')->new();
        $this->assertSame('Isser', $isser->getShortName());
    }
}
