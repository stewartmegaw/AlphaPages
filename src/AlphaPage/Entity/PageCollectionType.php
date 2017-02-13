<?php

namespace AlphaPage\Entity;

use Alpha\Entity\AlphaEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="alpha_pages_collections_types") 
 */
class PageCollectionType extends AlphaEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $name;

    /** @ORM\Column(type="string") */
    protected $description;

}
