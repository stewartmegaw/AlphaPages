<?php

namespace AlphaPage\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use AlphaPage\Service\PageCollectionService;
use AlphaPage\Form\PageCollectionForm;
use AlphaPage\Form\PageCollectionFormFilter;

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
        $form = new PageCollectionForm();
        $filter = new PageCollectionFormFilter();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            $form->setData($data);
            $form->setInputFilter($filter);

            if ($form->isValid()) {
                try {
                    $this->pageCollectionService->createPageCollection($data);
                    $this->flashMessenger()->addSuccessMessage('Collection Created Succesfully!');
                    return $this->redirect()->toRoute('alpha-page-collections');
                } catch (\Exception $ex) {
                    $this->flashMessenger()->addWarningMessage('Failed to create collection');
                    return $this->redirect()->toRoute('alpha-page-collections');
                }
                return $this->redirect()->toRoute('alpha-page-collections');
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function updateAction() {
        $form = new PageCollectionForm();
        $filter = new PageCollectionFormFilter();

        $id = $this->params('id', null);
        if (empty($id)) {
            $this->flashMessenger()->addWarningMessage('Unable to edit collection! Contact Administrator');
            return $this->redirect()->toRoute('alpha-page-collections');
        }

        $collection = $this->pageCollectionService->getPageCollectionById($id);
        $form->bind($collection);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            try {
                $this->pageCollectionService->updatePageCollection($id, $data);
                $this->flashMessenger()->addSuccessMessage('Collection Updated Succesfully!');
                return $this->redirect()->toRoute('alpha-page-collections');
            } catch (\Exception $ex) {
                $this->flashMessenger()->addWarningMessage('Failed to update collection..');
                return $this->redirect()->toRoute('alpha-page-collections');
            }
        }

        return new ViewModel(['form' => $form, 'collection' => $collection]);
    }

    public function deleteAction() {
        $id = $this->params('id', null);
        if (empty($id)) {
            $this->flashMessenger()->addWarningMessage('Unable to delete collection');
            return $this->redirect()->toRoute('alpha-page-collections');
        }

        try {
            $this->pageCollectionService->deletePageCollection($id);
            $this->flashMessenger()->addSuccessMessage('Collection deleted succesfully!');
            return $this->redirect()->toRoute('alpha-page-collections');
        } catch (\Exception $ex) {
            $this->flashMessenger()->addWarningMessage('Unable to delete collection');
            return $this->redirect()->toRoute('alpha-page-collections');
        }
    }

}
