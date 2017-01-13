<?php

namespace AlphaPage\Controller;

use Doctrine\ORM\EntityManager;
use AlphaPage\Service\PageService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AlphaPage\Form\PageForm;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageController extends AbstractActionController {

    private $config;
    private $pageService;
    private $entityManager;
    private $services;

    public function __construct($config, EntityManager $entityManager, PageService $pageService, $services) {
        $this->config = $config;
        $this->pageService = $pageService;
        $this->entityManager = $entityManager;
        $this->services = $services;
        $this->services['config'] = $config;
        $this->services['entitymanager'] = $entityManager;
    }

    public function editAction() {
        $name = $this->params('name', null);
        $page = $this->pageService->getPageByName($name);
        $form = new PageForm();
        $form->bind($page);

        if ($this->getRequest()->isPost()) {
            $user = $this->zfcUserAuthentication()->getIdentity();
            $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
            $this->pageService->updatePage($page->getId(), $data, $user);
            $this->flashMessenger()->addSuccessMessage('Page updated succesfully!');
            $this->redirect()->toRoute('alpha-page', ['name' => 'dashboard']);
        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('page', $page);
        $viewModel->setVariable('form', $form);
        return $viewModel;
    }

    public function previewAction() {
        $request = $this->getRequest();
        $data = array_merge_recursive($request->getPost()->toArray(), $request->getFiles()->toArray());
        $page = $this->pageService->updatePage(\AlphaPage\Entity\Page::PREVIEW_PAGE_ID, $data);
        $view = new ViewModel();
        $view->setVariable('page', $page);
        $view->setTemplate('alpha-page/page/view.phtml');
        return $view;
    }

    public function viewAction() {

        //Get page content from db
        $page = $this->pageService->getPageByName($this->params('name'));

        //View Model
        $viewModel = new ViewModel();

        //SET CONTENT AND SERVICE
        $viewModel->setVariable('page', $page);
        $viewModel->setVariable('services', $this->services);

        //SET PARAMS
        $viewModel->setVariable('param1', $this->params('param1', null));
        $viewModel->setVariable('param2', $this->params('param2', null));

        return $viewModel;
    }

}
