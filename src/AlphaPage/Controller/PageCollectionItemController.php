<?php

namespace AlphaPage\Controller;

use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use AlphaPage\Service\PageCollectionService;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionItemController extends AbstractActionController {

    private $config;
    private $entityManager;
    private $pageCollectionService;

    public function __construct(EntityManager $entityManager, PageCollectionService $pageCollectionService, $config) {
        $this->config = $config;
        $this->entityManager = $entityManager;
        $this->pageCollectionService = $pageCollectionService;
    }

    public function manageAction() {
        $name = $this->params('name');

        if (empty($name))
            return $this->redirect()->toRoute('home');

        $pageCollection = $this->pageCollectionService->getPageCollectionByName($name);
        return new ViewModel(['pageCollection' => $pageCollection]);
    }

    public function createAction() {
        return new ViewModel();
    }

    public function updateAction() {
        return new ViewModel();
    }

    public function deleteAction() {
        return new ViewModel();
    }

}
