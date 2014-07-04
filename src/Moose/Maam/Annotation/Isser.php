<?php

namespace Moose\Maam\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Isser extends Annotation implements MaamAnnotationInterface
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getShortName()
    {
        return 'Isser';
    }
}
