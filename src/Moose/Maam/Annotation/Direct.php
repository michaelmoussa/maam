<?php
/**
 * Maam
 */
namespace Moose\Maam\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Annotation indicating that a self-named getter method should be generated.
 *
 * @Annotation
 */
class Direct extends Annotation implements MaamAnnotation
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
