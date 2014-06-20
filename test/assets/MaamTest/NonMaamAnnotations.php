<?php

namespace MaamTest;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class NonMaamAnnotations
{
    /**
     * @ORM\Id
     */
    protected $id;
}
