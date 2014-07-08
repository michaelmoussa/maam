<?php
/**
 * Maam
 */
namespace Moose\Maam\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Annotation indicating that both a getter and setter should be generated.
 *
 * @Annotation
 */
class Both extends Annotation implements MaamAnnotationInterface
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getShortName()
    {
        return 'Both';
    }
}
