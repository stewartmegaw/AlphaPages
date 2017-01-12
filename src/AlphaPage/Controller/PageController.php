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

    public function __construct($config, EntityManager $entityManager, PageService $pageService) {
        $this->config = $config;
        $this->pageService = $pageService;
        $this->entityManager = $entityManager;
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
            $this->redirect()->toRoute('members-area/dashboard');
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
        $name = $this->params('name');
        $page = $this->pageService->getPageByName($name);
        if ($page->getName() === 'charitable-work')
            $this->layout('layout/charitablework-layout');
        return new ViewModel(['page' => $page]);
    }

}
