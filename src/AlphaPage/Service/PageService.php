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

    public function updatePage($id, $routeName, $content, $routeGuardRoleId, $user = null, $pageManagerRoleId) {

        $now = date_create(date('Y-m-d H:i:s'));
        $page = $this->entityManager->getRepository('AlphaPage\Entity\Page')->find($id);
        $page->setName($routeName);
        $page->setContent($content);
        $page->setLastModified($now);
        $page->setEditor($user);
        $page->setPageManagerRole($pageManagerRoleId);

        $routes = $this->entityManager->getRepository('Alpha\Entity\AlphaRoute')->findBy(['page' => $page]);
        foreach ($routes as $route) {
            $route->setName($routeName);
            //$route->setRoute('/' . $routeName);
            $this->updatePageRouteGuardRole($route, $routeGuardRoleId);
        }

        $this->entityManager->flush();
        return $page;
    }

    public function createPage($routeName, $content, $routeGuardRole, $user, $pageManagerRole) {
        $now = date_create(date('Y-m-d H:i:s'));

        $page = new Page();
        $page->setName($routeName);
        $page->setEditor($user);
        $page->setContent($content);
        $page->setLastModified($now);
        $page->setLayout(NULL);
        $page->setPageManagerRole($pageManagerRole);
        $this->entityManager->persist($page);

        $route = new AlphaRoute();
        $route->setName($routeName);
        $route->setRoute('/' . $routeName);
        $route->setDefaultController('AlphaPage\Controller\Page');
        $route->setDefaultAction('view');
        $route->setType(AlphaRoute::TYPE_LITERAL);
        $route->setParentRoute(NULL);
        $route->setPage($page);
        $route->addRouteGuardRole($routeGuardRole);
        $this->entityManager->persist($route);

        $this->entityManager->flush();
    }

    public function deletePage($id) {

        $page = $this->entityManager->getRepository('AlphaPage\Entity\Page')->find($id);

        if (!empty($page)) {
            $route = $this->entityManager->getRepository('Alpha\Entity\AlphaRoute')->findOneBy(['page' => $page]);
            //if page is associated to route, remove all route guard roles and set page as null
            if (!empty($route)) {
                if (!empty($route->getRouteGuardRoles())) {
                    foreach ($route->getRouteGuardRoles() as $role) {
                        $sql = "DELETE from alpha_routes_guards "
                                . "WHERE route_id={$route->getId()} "
                                . "AND role_id={$role->getId()}";

                        $direct_db_connection = $this->entityManager->getConnection();
                        $direct_db_connection->executeUpdate($sql);
                    }
                }
                $route->setPage(NULL);
                $this->entityManager->flush();
            }
            $this->entityManager->remove($page);
            $this->entityManager->flush();
        }
    }

    public function updatePageRouteGuardRole($route, $roleId) {
        $oldRouteGuardRoles = $route->getRouteGuardRoles();
        foreach ($oldRouteGuardRoles as $role) {
            $sql = "DELETE from alpha_routes_guards "
                    . "WHERE route_id={$route->getId()} "
                    . "AND role_id={$role->getId()}";

            $direct_db_connection = $this->entityManager->getConnection();
            $direct_db_connection->executeUpdate($sql);
        }

        //UPDATING ROLE
        $newRouteGuardRole = $this->entityManager->getRepository('\AlphaUserBase\Entity\Role')->find($roleId);
        $route->addRouteGuardRole($newRouteGuardRole);
    }

}
