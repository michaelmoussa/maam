<?php

namespace Moose\Maam\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Direct extends Annotation implements MaamAnnotationInterface
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getShortName()
    {
        return 'Direct';
    }
}
