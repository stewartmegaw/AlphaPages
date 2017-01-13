<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AlphaPage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="page_dependencies") 
 */
class PageDependency {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="dependencies")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     *
     */
    protected $page;

    /** @ORM\Column(type="string") */
    protected $serviceName;

    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getPage() {
        return $this->page;
    }

    function setPage($page) {
        $this->page = $page;
    }

    public function getServiceName() {
        return $this->serviceName;
    }

    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
