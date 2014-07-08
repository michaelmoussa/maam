<?php
/**
 * Maam
 */
namespace Moose\Maam\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Annotation indicating that a setter method should be generated.
 *
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
