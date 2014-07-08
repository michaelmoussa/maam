<?php

namespace Moose\Maam\Annotation;

class DirectTest extends \PHPUnit_Framework_TestCase
{
    public function testDirectAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Direct'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Direct');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }

    public function testHasCorrectShortName()
    {
        $direct = new Direct([]);
        $this->assertSame('Direct', $direct->getShortName());
    }
}
