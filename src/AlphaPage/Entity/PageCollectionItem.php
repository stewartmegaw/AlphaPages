<?php

namespace AlphaPage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Alpha\Entity\AlphaEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="alpha_page_collection_items") 
 */
class PageCollectionItem extends AlphaEntity {

    const ITEM_BANNER = 1;
    const ITEM_THUMB = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $title;

    /** @ORM\Column(type="text", name="small_description") */
    protected $smallDescription;

    /** @ORM\Column(type="text", name="description") */
    protected $description;

    /** @ORM\Column(type="datetime", name="date") */
    protected $date;

    //Map this field to collection item type (
    //Colletion Type -> 
    //Collection -> 
    //Collection Items [Differentiable by collection item type]

    /** @ORM\Column(type="integer", name="type") */
    protected $type;

    /** @ORM\Column(type="datetime", name="date_created", nullable=true) */
    protected $dateCreated;

    /** @ORM\Column(type="string", name="external_url") */
    protected $externalUrl;

    /** @ORM\OneToMany(targetEntity="PageCollectionItemFiles", mappedBy="pageCollectionItem") */
    protected $files;

    /**
     * @ORM\ManyToOne(targetEntity="PageCollection", inversedBy="items")
     * @ORM\JoinColumn(name="page_collection_id", referencedColumnName="id")
     */
    protected $pageCollection;

    public function __construct() {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
