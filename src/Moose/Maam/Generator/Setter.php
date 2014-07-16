<?php
/**
 * Maam
 */
namespace Moose\Maam\Generator;

use Moose\Maam\Annotation\FluentAwareInterface;

/**
 * Class to generate setter code.
 */
class Setter
{
    /**
     * Writes the setter.
     *
     * @param string $propertyName The name of the property
     * @param FluentAwareInterface $annotation
     * @return string
     */
    public function generate($propertyName, FluentAwareInterface $annotation)
    {
        $methodSuffix = ucfirst($propertyName);

        $returnType = $annotation->getFluent() ? 'self' : 'void';

        $code = <<<HEREDOC
    /**
     * Sets the ${propertyName}.
     *
     * @param mixed \$${propertyName}
     * @return ${returnType}
     */
    public function set${methodSuffix}(\$${propertyName})
    {
        \$this->${propertyName} = \$${propertyName};
HEREDOC;

        if ($annotation->getFluent()) {
            $code .= "\n        return \$this;";
        }

        $code .= "\n    }";

        return $code;
    }
}
