<?php

namespace AlphaPage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Alpha\Entity\AlphaEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="alpha_page_collection_item_files") 
 */
class PageCollectionItemFiles extends AlphaEntity {

    const ARTICLE_BANNER = 1;
    const ARTICLE_THUMB = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="PageCollectionItem", inversedBy="files")
     * @ORM\JoinColumn(name="page_collection_item_id", referencedColumnName="id")
     *
     */
    protected $pageCollectionItem;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     */
    protected $file;

    /**
     * @ORM\Column(type="integer")
     *
     */
    protected $type;

}
