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
    'router' => array(
        'routes' => array(
            'alpha-page' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/home[/:name][/:param1][/:param2]',
                    'defaults' => array(
                        'controller' => 'AlphaPage\Controller\Page',
                        'action' => 'view',
                    ),
                ),
            ),
            'crud-page' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/page[/:action][/:name]',
                    'defaults' => array(
                        'controller' => 'AlphaPage\Controller\Page',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
