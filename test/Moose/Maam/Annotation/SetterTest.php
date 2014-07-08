<?php

namespace Moose\Maam\Annotation;

class SetterTest extends \PHPUnit_Framework_TestCase
{
    public function testSetterAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Setter'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Setter');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }

    public function testHasCorrectShortName()
    {
        $setter = new Setter([]);
        $this->assertSame('Setter', $setter->getShortName());
    }
}
