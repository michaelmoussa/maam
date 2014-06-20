<?php

namespace Moose\Maam\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Setter extends Annotation implements MaamAnnotationInterface
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getShortName()
    {
        return 'Setter';
    }
}
