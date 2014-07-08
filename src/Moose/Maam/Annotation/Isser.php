<?php
/**
 * Maam
 */
namespace Moose\Maam\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Annotation indicating that an "is<propertyName>" method should be generated.
 *
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
