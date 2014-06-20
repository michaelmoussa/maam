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
     * @Maam\Setter
     */
    protected $lastName;

    /**
     * @Maam\Getter
     * @Maam\Setter
     */
    protected $dateOfBirth;
}
