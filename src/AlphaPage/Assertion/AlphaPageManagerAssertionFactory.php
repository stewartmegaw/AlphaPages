<?php

namespace AlphaPage\Assertion;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use AlphaPage\Assertion\AlphaPageManagerAssertion;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class AlphaPageManagerAssertionFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $authentication = $serviceLocator->get('zfcuser_auth_service');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        return new AlphaPageManagerAssertion($authentication, $entityManager);
    }

}
