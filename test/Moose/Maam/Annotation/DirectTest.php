<?php

namespace Moose\Maam\Annotation;

use Xpmock\TestCase;

class DirectTest extends TestCase
{
    public function testDirectAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Direct'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Direct');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }

    public function testHasCorrectShortName()
    {
        $direct = $this->mock('Moose\Maam\Annotation\Direct')->new();
        $this->assertSame('Direct', $direct->getShortName());
    }
}
