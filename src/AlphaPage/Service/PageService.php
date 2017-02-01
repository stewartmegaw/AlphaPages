<?php

namespace AlphaPage\Service;

use Doctrine\ORM\EntityManager;
use AlphaUserBase\Service\UserService;
use Alpha\Entity\AlphaRoute;
use AlphaPage\Entity\Page;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageService {

    private $config;
    private $entityManager;
    private $userService;

    public function __construct($config, EntityManager $entityManager, UserService $userService) {
        $this->config = $config;
        $this->userService = $userService;
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

    public function createPage($routeName, $code, $user) {
        $now = date_create(date('Y-m-d H:i:s'));

        $page = new Page();
        $page->setName($routeName);
        $page->setEditor($user);
        $page->setContent($code);
        $page->setLastModified($now);
        $page->setLayout(NULL);
        $this->entityManager->persist($page);

        $route = new AlphaRoute();
        $route->setName($routeName);
        $route->setRoute('/' . $routeName);
        $route->setDefaultController('AlphaPage\Controller\Page');
        $route->setDefaultAction('view');
        $route->setType(AlphaRoute::TYPE_LITERAL);
        $route->setParentRoute(NULL);
        $route->setPage($page);
        $this->entityManager->persist($route);

        $this->entityManager->flush();
    }

    public function deletePage($id) {

        $page = $this->entityManager->getRepository('AlphaPage\Entity\Page')->find($id);
        if (!empty($page)) {
            $route = $this->entityManager->getRepository('Alpha\Entity\AlphaRoute')->findOneBy(['page' => $page]);
            if (!empty($route)) {
                $this->entityManager->remove($route);
                $this->entityManager->flush();
            }
            $this->entityManager->remove($page);
            $this->entityManager->flush();
        }
    }

}
