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

    public function listAction() {

        if (empty($this->pageCollectionName))
            return $this->redirect()->toRoute('home');

        $pageCollectionType = $this->pageCollectionService->getPageCollectionTypeByName($this->pageCollectionName);
        $pageCollection = $this->pageCollectionService->getPageCollectionByType($pageCollectionType);
        $pageCollectionCountsForYearsAndMonths = $this->pageCollectionService->getPageCollectionCountForYearsAndMonths($this->pageCollectionName);

        return new ViewModel([
            'pageCollection' => $pageCollection,
            'pageCollectionCount' => $pageCollectionCountsForYearsAndMonths,
            'pageCollectionType' => $pageCollectionType,
        ]);
    }

    public function itemAction() {

        $id = $this->params('param1');
        if (empty($id))
            return $this->redirect()->toRoute('home');

        $pageCollectionItem = $this->pageCollectionService->getPageCollectionItemById($id);
        $recentPageCollectionItems = $this->pageCollectionService->getRecentPageCollectionItemsByType($pageCollectionItem->getCollectionType());

        return new ViewModel([
            'pageCollectionItem' => $pageCollectionItem,
            'recentPageCollectionItems' => $recentPageCollectionItems
        ]);
    }

}
