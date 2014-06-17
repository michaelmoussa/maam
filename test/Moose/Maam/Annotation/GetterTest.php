<?php

namespace Moose\Maam\Annotation;

use Xpmock\TestCase;

class GetterTest extends TestCase
{
    public function testGetterAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Getter'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Getter');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }
}
