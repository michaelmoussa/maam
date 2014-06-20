<?php

namespace MaamTest;

use Doctrine\ORM\Mapping as ORM;
use Moose\Maam\Annotation as Maam;

/**
 * @ORM\Entity
 */
class DoctrineOrmEntity
{
    /**
     * @ORM\Id
     * @Maam\Getter
     */
    protected $id;
}
