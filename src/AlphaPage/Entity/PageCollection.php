<?php

namespace AlphaPage\Entity;

use Alpha\Entity\AlphaEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="alpha_page_collections") 
 */
class PageCollection extends AlphaEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $name;

    /** @ORM\Column(type="string", nullable=true) */
    protected $description;

    /** @ORM\OneToMany(targetEntity="PageCollectionItem", mappedBy="pageCollection") */
    protected $items;

    public function __construct() {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
