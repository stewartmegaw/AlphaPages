<?php

namespace AlphaPage\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $services = [];

        $config = $serviceLocator->getServiceLocator()->get('config');
        $services['config'] = $config;

        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $services['entityManager'] = $entityManager;


        $pageService = $serviceLocator->getServiceLocator()->get('AlphaPage\Service\Page');
        $services['pageService'] = $pageService;

        $auth = $serviceLocator->getServiceLocator()->get('AlphaUserBase\Service\Authentication');
        $services['authentication'] = $auth;

        $router = $serviceLocator->getServiceLocator()->get('router');
        $services['router'] = $router;

        $request = $serviceLocator->getServiceLocator()->get('request');
        $services["request"] = $request;
        $routerMatch = $router->match($request);
        $routeName = $routerMatch->getMatchedRouteName();

        $alphaFormFilter = $serviceLocator->getServiceLocator()->get('Alpha\Form\AlphaFormFilter');

        $alphaFormProcess = $serviceLocator->getServiceLocator()->get('Alpha\Service\AlphaFormProcess');

        switch ($routeName)
        {
            case "crud-page":
                $name = $routerMatch->getParam("name");
                $page = $entityManager->getRepository('AlphaPage\Entity\Page')->findOneBy(['name' => $name]);
                $dependencies = $page->getDependencies();
                foreach ($dependencies as $dependency)
                {
                    $services[$dependency->getServiceName()] = $serviceLocator->getServiceLocator()->get($dependency->getServiceName());
                }
                break;

            default:
                $routeRepo = $entityManager->getRepository('\Alpha\Entity\AlphaRoute');

                $parentRoute = null;
                if (strpos($routeName, '/') !== false)
                {
                    $parentRouteName = explode('/', $routeName)[0];
                    $routeName = explode('/', $routeName)[1];
                    $parentRoute = $routeRepo->findOneBy(['name' => $parentRouteName, 'parentRoute' => null]);
                }

                if (empty($parentRoute))
                    $route = $entityManager->getRepository('\Alpha\Entity\AlphaRoute')->findOneBy(['name' => $routeName, 'parentRoute' => NULL]);
                else
                    $route = $entityManager->getRepository('\Alpha\Entity\AlphaRoute')->findOneBy(['name' => $routeName, 'parentRoute' => $parentRoute]);

                $page = $route->getPage();
                $dependencies = $page->getDependencies();

                foreach ($dependencies as $dependency)
                {
                    $services[$dependency->getServiceName()] = $serviceLocator->getServiceLocator()->get($dependency->getServiceName());
                }
                break;
        }



        return new PageController($config, $entityManager, $pageService, $services, $page, $router, $alphaFormFilter, $alphaFormProcess);
    }

}
