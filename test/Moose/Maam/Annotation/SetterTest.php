<?php

namespace Moose\Maam\Annotation;

use Xpmock\TestCase;

class SetterTest extends TestCase
{
    public function testSetterAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Setter'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Setter');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }
}
