<?php

namespace AlphaPage\Service;

use Doctrine\ORM\EntityManager;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageService {

    private $config;
    private $entityManager;

    public function __construct($config, EntityManager $entityManager) {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function getPageByName($name) {
        return $this->entityManager->getRepository('AlphaPage\Entity\Page')->findOneBy(['name' => $name]);
    }

    public function getPageById($id) {
        return $this->entityManager->getRepository('AlphaPage\Entity\Page')->find($id);
    }

    public function updatePage($id, $data, $user = null) {

        $now = date_create(date('Y-m-d H:i:s'));
        $page = $this->entityManager->getRepository('AlphaPage\Entity\Page')->find($id);
        $page->setContent($data["content"]);
        $page->setLastModified($now);
        $page->setEditor($user);
        $this->entityManager->flush();
        return $page;
    }

}
