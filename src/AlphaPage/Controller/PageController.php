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
    private $page;

    public function __construct($config, EntityManager $entityManager, PageService $pageService, $services, $page) {
        $this->config = $config;
        $this->pageService = $pageService;
        $this->entityManager = $entityManager;
        $this->services = $services;
        $this->page = $page;
    }

    public function editAction() {

        $name = $this->params('name', null);
        $page = $this->pageService->getPageByName($name);

        if (!$this->isAllowed($page, 'edit')) {
            $this->flashMessenger()->addWarningMessage('You are not allowed to edit this page! Please contact you application administrator');
            return $this->redirect()->toRoute('dashboard');
        }

        $form = new PageForm();
        $form->bind($page);

        if ($this->getRequest()->isPost()) {
            $user = $this->zfcUserAuthentication()->getIdentity();
            $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
            $this->pageService->updatePage($page->getId(), $data, $user);
            $this->flashMessenger()->addSuccessMessage('Page updated succesfully!');
            $this->redirect()->toRoute('dashboard');
        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('page', $page);
        $viewModel->setVariable('form', $form);
        return $viewModel;
    }

    public function previewAction() {

        $name = $this->params('name');

        $request = $this->getRequest();
        $data = array_merge_recursive($request->getPost()->toArray(), $request->getFiles()->toArray());
        $page = $this->pageService->updatePage(\AlphaPage\Entity\Page::PREVIEW_PAGE_ID, $data);
        $view = new ViewModel();
        $view->setVariable('page', $page);
        $view->setVariable('services', $this->services);
        $view->setTemplate('alpha-page/page/view.phtml');
        $view->setVariable('preview', true);
        return $view;
    }

    public function viewAction() {

        if (!empty($this->page->getLayout()))
            $this->layout($this->page->getLayout());

        //View Model
        $viewModel = new ViewModel();

        //SET CONTENT AND SERVICE
        $viewModel->setVariable('page', $this->page);
        $viewModel->setVariable('services', $this->services);
        $this->layout()->setVariable('page', $this->page);
        $this->layout()->setVariable('services', $this->services);

        return $viewModel;
    }

}
