<?php

namespace AlphaPage\Controller;

use AlphaPage\Service\PageService;
use Alpha\Controller\AlphaActionController;
use Zend\View\Model\ViewModel;
use AlphaPage\Form\PageForm;
use Alpha\Form\AlphaFormFilter;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageController extends AlphaActionController
{

    private $pageService;
    private $services;
    private $page;

    public function __construct($config, $entityManager, PageService $pageService, $services, $page, $router, AlphaFormFilter $alphaFormFilter, $alphaFormProcess, $alphaStateBuilder)
    {
        parent::__construct($config, $services['authentication'], $entityManager, $router, $alphaFormFilter, $alphaFormProcess, $alphaStateBuilder);

        $this->pageService = $pageService;
        $this->services = $services;
        $this->page = $page;
    }

    public function editAction()
    {

        $name = $this->params('name', null);
        $page = $this->pageService->getPageByName($name);

        if (!$this->isAllowed($page, 'edit'))
        {
            $this->flashMessenger()->addWarningMessage('You are not allowed to edit this page! Please contact you application administrator');
            return $this->redirect()->toRoute('dashboard');
        }

        $form = new PageForm();
        $form->bind($page);

        if ($this->getRequest()->isPost())
        {
            $user = $this->authenticationService->getIdentity();
            $route = $this->entityManager->getRepository('Alpha\Entity\AlphaRoute')->findOneBy(['page' => $page]);

            $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
            $this->pageService->updatePage(
                    $page->getId(), $page->getName(), $data['content'], $route->getRouteGuardRoles()[0]->getId(), $user, $page->getPageManagerRole(), $this->entityManager
            );
            $this->flashMessenger()->addSuccessMessage('Page updated succesfully!');
            $this->redirect()->toRoute('dashboard');
        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('page', $page);
        $viewModel->setVariable('form', $form);
        return $viewModel;
    }

    public function previewAction()
    {

        $name = $this->params('name');

        $page = $this->entityManager->getRepository('AlphaPage\Entity\Page')->findOneBy(['name' => $name]);
        $route = $this->entityManager->getRepository('Alpha\Entity\AlphaRoute')->findOneBy(['page' => $page]);
        $request = $this->getRequest();
        $data = array_merge_recursive($request->getPost()->toArray(), $request->getFiles()->toArray());
        $p = $this->pageService->updatePage($page->getId(), $route->getName(), $data['content'], '1', $this->authenticationService->getIdentity(), '4');
        $view = new ViewModel();
        $view->setVariable('page', $p);
        $view->setVariable('services', $this->services);
        $view->setTemplate('alpha-page/page/view.phtml');
        $view->setVariable('preview', true);
        return $view;
    }

    public function viewAction()
    {

        //Create title if specified for route and pass it to layout and view
        $this->buildTitle($this->getRoute()->getTitleBuilder());

        //Create meta tags if specified for route and pass it to layout and view
        $this->buildMeta($this->getRoute()->getMetaBuilder());

        //Create state if specified for route and pass it to layout and view
        $result = $this->alphaStateBuilder->buildState($this->getRoute()->getStateBuilder());
        if ($result instanceof \Zend\Http\Response)
            return $result;
        else
            $this->setVariables($result);

        if (!empty($this->page->getLayout()))
            $this->alphaLayoutTemplate = $this->page->getLayout();

        //SET CONTENT AND SERVICE
        $this->setVariable('page', $this->page);
        $this->setVariable('services', $this->services);



        return $this->alphaReturn();
    }

}
