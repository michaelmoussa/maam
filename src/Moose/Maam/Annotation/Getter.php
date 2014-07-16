<?php
/**
 * Maam
 */
namespace Moose\Maam\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Annotation indicating that a getter method should be generated.
 *
 * @Annotation
 */
class Getter extends Annotation implements MaamAnnotation
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
