<?php

namespace AlphaPage\Entity;

use Alpha\Entity\AlphaEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="alpha_page_collection_items") 
 */
class PageCollectionItem extends AlphaEntity {

    const PREVIEW_ID = -1;
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

    /** @ORM\Column(type="string", nullable=true) */
    protected $routeLabel;

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

    /**
     * @ORM\ManyToOne(targetEntity="PageCollection", inversedBy="items")
     * @ORM\JoinColumn(name="page_collection_id", referencedColumnName="id")
     */
    protected $pageCollection;

    /**
     * @ORM\ManyToMany(targetEntity="AlphaFiles\Entity\AlphaFile")
     * @ORM\JoinTable(name="alpha_page_collection_item_files",
     *      joinColumns={@ORM\JoinColumn(name="alpha_page_collection_item_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="alpha_file_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $files;

    /** @ORM\ManyToOne(targetEntity="PageCollectionItem") */
    protected $parentItem;

    public function __construct() {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getParentsRecursive($topItemId = null, $includeTopItem = false) {
        $parents = array();

        if (!empty($this->parentItem)) {
            if (!(!empty($topItemId) && !$includeTopItem && $this->parentItem->getId() == $topItemId)) {
                $parents[] = $this->getParentItem();
                if (!(!empty($topItemId) && $this->parentFilterId->getId() == $topItemId))
                    $parents = array_merge($parents, $this->getParentItem()->getParentsRecursive($topItemId, $includeTopItem));
            }
        }

        return $parents;
    }

}
