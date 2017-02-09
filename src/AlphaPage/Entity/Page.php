<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AlphaPage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="alpha_pages") 
 */
class Page implements ResourceInterface {

    const PREVIEW_PAGE_ID = -1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $name;

    /** @ORM\Column(type="text") */
    protected $content;

    /** @ORM\Column(type="datetime", name="last_modified", nullable=true) */
    protected $lastModified;

    /** @ORM\ManyToOne(targetEntity="\AlphaUserBase\Entity\AlphaUserBase") */
    protected $editor;

    /** @ORM\OneToMany(targetEntity="PageDependency", mappedBy="page") */
    protected $dependencies;

    /** @ORM\Column(type="string", nullable=true) */
    protected $layout;

    /** @ORM\Column(type="integer") */
    protected $pageManagerRole;

    public function __construct() {
        $this->dependencies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getResourceId() {
        return 'Page';
    }

    function setPageManagerRole($pageManagerRole) {
        $this->pageManagerRole = $pageManagerRole;
    }

    function getPageManagerRole() {
        return $this->pageManagerRole;
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getContent() {
        return $this->content;
    }

    function getLastModified() {
        return $this->lastModified;
    }

    function getEditor() {
        return $this->editor;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setContent($content) {
        $this->content = $content;
    }

    function setLastModified($lastModified) {
        $this->lastModified = $lastModified;
    }

    function setEditor($editor) {
        $this->editor = $editor;
    }

    function setDependencies($dependencies) {
        $this->dependencies = $dependencies;
    }

    function getDependencies() {
        return $this->dependencies;
    }

    function setLayout($layout) {
        $this->layout = $layout;
    }

    function getLayout() {
        return $this->layout;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
