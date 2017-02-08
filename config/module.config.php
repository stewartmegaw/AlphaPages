<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AlphaPage;

return array(
    'controllers' => array(
        'factories' => array(
            'AlphaPage\Controller\Page' => 'AlphaPage\Controller\PageControllerFactory',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            //ASSERTIONS
            'assertion.AlphaPageManager' => 'AlphaPage\Assertion\AlphaPageManagerAssertionFactory',
            //OTHERS
            'AlphaPage\Service\Page' => 'AlphaPage\Service\PageServiceFactory',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        ),
    ),
    'bjyauthorize' => array(
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'Collections' => array(),
            ),
        ),
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('alpha'), 'Collections', array('edit-view')),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'bjyauthorize' => array(
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'Page' => array(),
            ),
        ),
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    //array(array('admin', 'alpha'), 'Page', array('edit'), 'assertion.AlphaPageManager'),
                ),
            ),
        ),
    ),
);
