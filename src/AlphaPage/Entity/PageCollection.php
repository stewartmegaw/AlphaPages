<?php

namespace AlphaPage\Entity;

use Alpha\Entity\AlphaEntityBase;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="alpha_page_collections") 
 */
class PageCollection extends AlphaEntityBase {

    const NESTED_TYPE_COLLECTION = 1;
    const LIST_TYPE_COLLECTION = 2;

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

    /** @ORM\OneToMany(targetEntity="AlphaPage\AlphaEntity\PageCollectionItem", mappedBy="pageCollection") */
    protected $items;

    /** @ORM\Column(type="integer") */
    protected $type;

    public function __construct() {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
