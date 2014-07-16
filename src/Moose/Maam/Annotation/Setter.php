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
class Setter extends Annotation implements MaamAnnotationInterface, FluentAware
{
    /**
     * Whether or not to generate a Fluent interface for this setter.
     *
     * @var bool
     */
    protected $fluent = false;

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function getFluent()
    {
        return $this->fluent;
    }

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
