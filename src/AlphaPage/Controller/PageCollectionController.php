<?php

namespace AlphaPage\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use AlphaPage\Service\PageCollectionService;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionController extends AbstractActionController {

    private $entityManager;
    private $pageCollectionService;
    private $pageCollectionName;

    public function __construct(EntityManager $entityManager, PageCollectionService $pageCollectionService, $pageCollectionName) {
        $this->entityManager = $entityManager;
        $this->pageCollectionService = $pageCollectionService;
        $this->pageCollectionName = $pageCollectionName;
    }

    //Front end
    public function listAction() {

        if (empty($this->pageCollectionName))
            return $this->redirect()->toRoute('home');

        $pageCollection = $this->pageCollectionService->getPageCollectionByName($this->pageCollectionName);
        $pageCollectionCountsForYearsAndMonths = $this->pageCollectionService->getPageCollectionItemCountForYearsAndMonths($pageCollection);

        return new ViewModel([
            'pageCollection' => $pageCollection,
            'pageCollectionCount' => $pageCollectionCountsForYearsAndMonths,
        ]);
    }

    public function itemAction() {

        $id = $this->params('param1');

        if (empty($id))
            return $this->redirect()->toRoute('home');

        $pageCollectionItem = $this->pageCollectionService->getPageCollectionItemById($id);
        $recentPageCollectionItems = $this->pageCollectionService->getRecentPageCollectionItems($pageCollectionItem);

        return new ViewModel([
            'pageCollectionItem' => $pageCollectionItem,
            'recentPageCollectionItems' => $recentPageCollectionItems
        ]);
    }

    //Backend Actions
    public function manageAction() {
        $collections = $this->pageCollectionService->getAllPageCollections();
        return new ViewModel(['collections' => $collections]);
    }

    public function createAction() {
        //TODO: Create a page collection item
    }

    public function updateAction() {
        //TODO: Update a page collection item by id
    }

    public function deleteAction() {
        //TODO: Delete a page collection item by id
    }

}
