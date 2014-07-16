<?php
/**
 * Maam
 */
namespace Moose\Maam\Annotation;

/**
 * Interface that Maam annotations must implement.
 */
interface MaamAnnotation
{
    /**
     * Returns the "short name" of this annotation, which will be used to invoke the
     * appropriate generation method.
     *
     * @return string
     */
    public function getShortName();
}
