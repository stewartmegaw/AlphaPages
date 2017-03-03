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
            'AlphaPage\Controller\PageCollection' => 'AlphaPage\Controller\PageCollectionControllerFactory',
            'AlphaPage\Controller\PageCollectionItem' => 'AlphaPage\Controller\PageCollectionItemControllerFactory',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            //ASSERTIONS
            'assertion.AlphaPageManager' => 'AlphaPage\Assertion\AlphaPageManagerAssertionFactory',
            //OTHERS
            'AlphaPage\Service\Page' => 'AlphaPage\Service\PageServiceFactory',
            'AlphaPage\Service\PageCollection' => 'AlphaPage\Service\PageCollectionServiceFactory',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'alpha_xml_driver' => array(
                'class' => 'Alpha\MappingDriver\AlphaDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/AlphaEntity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                    __NAMESPACE__ . '\AlphaEntity' => 'alpha_xml_driver'
                )
            ),
            'orm_dynamic' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                    __NAMESPACE__ . '\AlphaEntity' => 'alpha_xml_driver'
                )
            )
        ),
    ),
    'bjyauthorize' => array(
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'Collections' => array(),
                'Page' => array(),
            ),
        ),
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('admin', 'alpha'), 'Collections', array('edit-view')),
                    array(array('admin', 'alpha'), 'Page', array('edit'), 'assertion.AlphaPageManager'),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'alpha-page-collections' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/collection[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'AlphaPage\Controller\PageCollection',
                        'action' => 'manage'
                    )
                )
            ),
            'alpha-page-collection-items' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/collection[/:collectionId]/items[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'AlphaPage\Controller\PageCollectionItem',
                        'action' => 'manage'
                    )
                )
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
