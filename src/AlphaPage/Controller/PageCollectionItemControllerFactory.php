<?php

namespace AlphaPage\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionItemControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $sr) {

        $serviceLocator = $sr->getServiceLocator();
        $config = $serviceLocator->get('config');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $pageCollectionService = $serviceLocator->get('AlphaPage\Service\PageCollection');

        return new PageCollectionItemController($entityManager, $pageCollectionService, $config);
    }

}
