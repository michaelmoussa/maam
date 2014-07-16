<?php
/**
 * Maam
 */
namespace Moose\Maam\Annotation;

/**
 * Interface indicating that the annotation can signify that a fluent interface is desired on the generated method.
 */
interface FluentAware
{
    /**
     * Returns whether or not a fluent interface should be used.
     *
     * @return bool
     */
    public function getFluent();
}
