<?php

namespace AlphaPage\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $sr) {

        $serviceLocator = $sr->getServiceLocator();
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $pageCollectionService = $serviceLocator->get('AlphaPage\Service\PageCollection');


        $router = $serviceLocator->get('router');
        $request = $serviceLocator->get('request');

        $routerMatch = $router->match($request);
        $pageCollectionName = $routerMatch->getMatchedRouteName();

        return new PageCollectionController($entityManager, $pageCollectionService, $router, $pageCollectionName);
    }

}
