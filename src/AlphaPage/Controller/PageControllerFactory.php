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


        $router = $serviceLocator->getServiceLocator()->get('router');
        $services['router'] = $router;

        $request = $serviceLocator->getServiceLocator()->get('request');
        $services["request"] = $request;

        $routerMatch = $router->match($request);

        //Get Page Dependencies
        $name = $routerMatch->getParam("name");
        $page = $entityManager->getRepository('AlphaPage\Entity\Page')->findOneBy(['name' => $name]);
        $dependencies = $page->getDependencies();
        foreach ($dependencies as $dependency) {
            $services[$dependency->getServiceName()] = $serviceLocator->getServiceLocator()->get($dependency->getServiceName());
        }

        return new PageController($config, $entityManager, $pageService, $services);
    }

}
