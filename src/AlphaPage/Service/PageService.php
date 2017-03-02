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

    public function updatePage($id, $routeName, $content, $routeGuardRoleId, $user = null, $pageManagerRoleId, $entityManager) {

        $now = date_create(date('Y-m-d H:i:s'));
        $page = $entityManager->getRepository('AlphaPage\Entity\Page')->find($id);
        $page->setName($routeName);
        $page->setContent($content);
        $page->setLastModified($now);
        $page->setEditor(NULL);
        $page->setPageManagerRole($pageManagerRoleId);

        $routes = $entityManager->getRepository('Alpha\Entity\AlphaRoute')->findBy(['page' => $page]);
        foreach ($routes as $route) {
            $route->setName($routeName);
            //$route->setRoute('/' . $routeName);
            $this->updatePageRouteGuardRole($route, $routeGuardRoleId, $entityManager);
        }

        $entityManager->flush();
        return $page;
    }

    public function createPage($routeName, $content, $routeGuardRole, $user, $pageManagerRole, $entityManager) {
        $now = date_create(date('Y-m-d H:i:s'));

        $page = new Page();
        $page->setName($routeName);
        $page->setEditor(NULL);
        $page->setContent($content);
        $page->setLastModified($now);
        $page->setLayout(NULL);
        $page->setPageManagerRole($pageManagerRole);
        $entityManager->persist($page);

        $route = new AlphaRoute();
        $route->setName($routeName);
        $route->setRoute('/' . $routeName);
        $route->setDefaultController('AlphaPage\Controller\Page');
        $route->setDefaultAction('view');
        $route->setType(AlphaRoute::TYPE_LITERAL);
        $route->setParentRoute(NULL);
        $route->setPage($page);
        $route->addRouteGuardRole($routeGuardRole);
        $entityManager->persist($route);

        $entityManager->flush();
    }

    public function deletePage($id, $entityManager) {

        $page = $entityManager->getRepository('AlphaPage\Entity\Page')->find($id);

        if (!empty($page)) {
            $route = $entityManager->getRepository('Alpha\Entity\AlphaRoute')->findOneBy(['page' => $page]);
            //if page is associated to route, remove all route guard roles and set page as null
            if (!empty($route)) {
                if (!empty($route->getRouteGuardRoles())) {
                    foreach ($route->getRouteGuardRoles() as $role) {
                        $sql = "DELETE from alpha_routes_guards "
                                . "WHERE route_id={$route->getId()} "
                                . "AND role_id={$role->getId()}";

                        $direct_db_connection = $entityManager->getConnection();
                        $direct_db_connection->executeUpdate($sql);
                    }
                }
                $route->setPage(NULL);
                $entityManager->flush();
            }
            $entityManager->remove($page);
            $entityManager->flush();
        }
    }

    public function updatePageRouteGuardRole($route, $roleId, $entityManager) {
        $oldRouteGuardRoles = $route->getRouteGuardRoles();
        foreach ($oldRouteGuardRoles as $role) {
            $sql = "DELETE from alpha_routes_guards "
                    . "WHERE route_id={$route->getId()} "
                    . "AND role_id={$role->getId()}";

            $direct_db_connection = $entityManager->getConnection();
            $direct_db_connection->executeUpdate($sql);
        }

        //UPDATING ROLE
        $newRouteGuardRole = $entityManager->getRepository('\AlphaUserBase\Entity\Role')->find($roleId);
        $route->addRouteGuardRole($newRouteGuardRole);
    }

}
