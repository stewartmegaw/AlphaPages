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

    protected $user;
    protected $userRoleId;

    public function __construct($user, $userRoleId) {
        $this->user = $user;
        $this->userRoleId = $userRoleId;
    }

    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null) {

        if ($resource instanceof Page) {

            if (empty($resource->getPageManagerRole()))
                return false;

            if ($this->userRoleId >= $resource->getPageManagerRole())
                return true;
        } else {
            return true;
        }
    }

}
