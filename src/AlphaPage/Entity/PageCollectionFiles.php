<?php

namespace AlphaPage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Alpha\Entity\AlphaEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="alpha_pages_collections_files") 
 */
class PageCollectionFiles extends AlphaEntity {

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
     * @ORM\ManyToOne(targetEntity="PageCollection", inversedBy="files")
     * @ORM\JoinColumn(name="page_collection_id", referencedColumnName="id")
     *
     */
    protected $pageCollection;

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
