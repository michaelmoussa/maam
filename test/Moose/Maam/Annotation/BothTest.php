<?php

namespace Moose\Maam\Annotation;

use Xpmock\TestCase;

class BothTest extends TestCase
{
    public function testBothAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Both'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Both');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }

    public function testHasCorrectShortName()
    {
        $both = $this->mock('Moose\Maam\Annotation\Both')->new();
        $this->assertSame('Both', $both->getShortName());
    }
}
