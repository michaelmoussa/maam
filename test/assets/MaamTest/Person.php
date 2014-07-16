<?php

namespace MaamTest;

use Moose\Maam\Annotation as Maam;

class Person
{
    /**
     * @Maam\Getter
     */
    protected $firstName;

    /**
     * @Maam\Both(fluent=true)
     */
    protected $middleInitial;

    /**
     * @Maam\Setter(fluent=true)
     */
    protected $lastName;

    /**
     * @Maam\Isser
     */
    protected $alive;

    /**
     * @Maam\Direct
     */
    protected $direct;

    /**
     * @Maam\Getter
     * @Maam\Setter
     */
    protected $dateOfBirth;
}
