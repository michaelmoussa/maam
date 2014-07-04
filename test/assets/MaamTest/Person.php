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
     * @Maam\Both
     */
    protected $middleInitial;

    /**
     * @Maam\Setter
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
