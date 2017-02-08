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

        $user = $authentication->getIdentity();

        if (!empty($user))
            $userRoleId = $user->getRoles()[0]->getId();
        else
            $userRoleId = null;
        
        return new AlphaPageManagerAssertion($user, $userRoleId);
    }

}
