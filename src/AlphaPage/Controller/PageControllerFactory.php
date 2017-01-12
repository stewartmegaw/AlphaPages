<?php

namespace AlphaPage\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->getServiceLocator()->get('config');
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $pageService = $serviceLocator->getServiceLocator()->get('AlphaPage\Service\Page');

        return new PageController($config, $entityManager, $pageService);
    }

}
