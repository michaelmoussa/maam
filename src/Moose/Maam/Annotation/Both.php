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
class Both extends Annotation implements MaamAnnotation, FluentAware
{
    /**
     * Whether or not to generate a Fluent interface when writing the setter.
     *
     * @var bool
     */
    public $fluent = false;

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
        return 'Both';
    }
}
