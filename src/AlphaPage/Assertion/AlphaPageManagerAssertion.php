<?php

namespace AlphaPage\Assertion;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Doctrine\ORM\EntityManager;
use AlphaPage\Entity\Page;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class AlphaPageManagerAssertion implements AssertionInterface {

    protected $authentication;
    protected $entityManager;

    public function __construct($authentication, EntityManager $entityManager) {
        $this->authentication = $authentication;
        $this->entityManager = $entityManager;
    }

    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null) {

        if ($resource instanceof Page) {
            return false;
        } else {
            return true;
        }
    }

}
