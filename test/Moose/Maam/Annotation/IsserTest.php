<?php

namespace Moose\Maam\Annotation;

class IsserTest extends \PHPUnit_Framework_TestCase
{
    public function testIsserAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Isser'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Isser');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }

    public function testHasCorrectShortName()
    {
        $isser = new Isser([]);
        $this->assertSame('Isser', $isser->getShortName());
    }
}
