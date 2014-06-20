<?php

namespace Moose\Maam\Annotation;

interface MaamAnnotationInterface
{
    /**
     * Returns the "short name" of this annotation, which will be used to invoke the
     * appropriate generation method.
     *
     * @return string
     */
    public function getShortName();
}
