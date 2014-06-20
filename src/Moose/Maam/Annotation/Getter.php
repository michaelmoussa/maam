<?php

namespace Moose\Maam\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Getter extends Annotation implements MaamAnnotationInterface
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getShortName()
    {
        return 'Getter';
    }
}
