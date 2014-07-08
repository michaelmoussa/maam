<?php

namespace Moose\Maam\Annotation;

class GetterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Getter'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Getter');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }

    public function testHasCorrectShortName()
    {
        $getter = new Getter([]);
        $this->assertSame('Getter', $getter->getShortName());
    }
}
