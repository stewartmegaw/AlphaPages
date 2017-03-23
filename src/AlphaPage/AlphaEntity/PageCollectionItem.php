<?php

namespace AlphaPage\AlphaEntity;

use Alpha\Entity\AlphaEntity;

class PageCollectionItem extends AlphaEntity {

    const PREVIEW_ID = -1;
    const ITEM_BANNER = 1;
    const ITEM_THUMB = 2;

    /**
     * Mapped Via XML Mapping file using AlphaDriver
     */
    protected $id;
    protected $title;
    protected $routeLabel;
    protected $redirect;
    protected $smallDescription;
    protected $description;
    protected $date;
    protected $type;
    protected $dateCreated;
    protected $externalUrl;
    protected $pageCollection;
    protected $files;
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
