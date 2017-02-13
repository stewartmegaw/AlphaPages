<?php

namespace AlphaPage\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionServiceFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->get('config');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        return new PageCollectionService($config, $entityManager);
    }

}
