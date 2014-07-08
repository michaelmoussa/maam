<?php

namespace Moose\Maam\Annotation;

class BothTest extends \PHPUnit_Framework_TestCase
{
    public function testBothAnnotationExistsAndIsValid()
    {
        $this->assertTrue(class_exists('Moose\Maam\Annotation\Both'));

        $reflectionClass = new \ReflectionClass('Moose\Maam\Annotation\Both');
        $this->assertContains('@Annotation', $reflectionClass->getDocComment());
    }

    public function testHasCorrectShortName()
    {
        $both = new Both([]);
        $this->assertSame('Both', $both->getShortName());
    }
}
