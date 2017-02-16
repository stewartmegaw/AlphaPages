<?php

namespace AlphaPage\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {

        $services = [];

        $config = $serviceLocator->getServiceLocator()->get('config');
        $services['config'] = $config;

        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $services['entityManager'] = $entityManager;


        $pageService = $serviceLocator->getServiceLocator()->get('AlphaPage\Service\Page');
        $services['pageService'] = $pageService;

        $auth = $serviceLocator->getServiceLocator()->get('AlphaUserBase\Service\Authentication');
        $services['authentication'] = $auth;

        $router = $serviceLocator->getServiceLocator()->get('router');
        $services['router'] = $router;

        $request = $serviceLocator->getServiceLocator()->get('request');
        $services["request"] = $request;
        $routerMatch = $router->match($request);
        $routeName = $routerMatch->getMatchedRouteName();

        switch ($routeName) {
            case "crud-page":
                $name = $routerMatch->getParam("name");
                $page = $entityManager->getRepository('AlphaPage\Entity\Page')->findOneBy(['name' => $name]);
                $dependencies = $page->getDependencies();
                foreach ($dependencies as $dependency) {
                    $services[$dependency->getServiceName()] = $serviceLocator->getServiceLocator()->get($dependency->getServiceName());
                }
                break;

            default:
                $route = $entityManager->getRepository('\Alpha\Entity\AlphaRoute')->findOneBy(['name' => $routeName, 'parentRoute' => null]);
                $page = $route->getPage();
                $dependencies = $page->getDependencies();

                foreach ($dependencies as $dependency) {
                    $services[$dependency->getServiceName()] = $serviceLocator->getServiceLocator()->get($dependency->getServiceName());
                }
                break;
        }



        return new PageController($config, $entityManager, $pageService, $services, $page);
    }

}
