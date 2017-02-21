<?php

namespace AlphaPage\Controller;

use Zend\View\Model\ViewModel;
use Alpha\Controller\AlphaAppController;
use Doctrine\ORM\EntityManager;
use AlphaPage\Service\PageCollectionService;
use AlphaPage\Form\PageCollectionForm;
use AlphaPage\Form\PageCollectionFormFilter;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionController extends AlphaAppController {

    private $entityManager;
    private $pageCollectionService;
    private $pageCollectionName;
    private $router;

    public function __construct(EntityManager $entityManager, PageCollectionService $pageCollectionService, $router, $pageCollectionName, $config) {
        parent::__construct($config);
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->pageCollectionService = $pageCollectionService;
        $this->pageCollectionName = $pageCollectionName;
    }

    //Front end
    public function listAction() {
        $request = $this->getRequest();
        $routeName = $this->router->match($request)->getMatchedRouteName();

        $routeRepo = $this->entityManager->getRepository('Alpha\Entity\AlphaRoute');

        //Check if route is a child route
        $parentRoute = null;
        if (strpos($routeName, '/') !== false) {
            $parentRouteName = explode('/', $routeName)[0];
            $routeName = explode('/', $routeName)[1];
            $parentRoute = $routeRepo->findOneBy(['name' => $parentRouteName, 'parentRoute' => null]);
        }

        $route = $routeRepo->findOneBy(['name' => $routeName, 'parentRoute' => NULL]);

        //if child route
        //page collection and page will always be linked to parent route 
        //child route will just be used for data manipulation
        if (!empty($parentRoute)) {
            $pageCollection = $parentRoute->getPageCollection();
            $page = $parentRoute->getPage();
        } else {
            $pageCollection = $route->getPageCollection();
            $page = $route->getPage();
        }


        //If no collection is assoicated retun to home
        if (empty($pageCollection))
            return $this->redirect()->toRoute('home');

        if ($pageCollection->getType() === \AlphaPage\Entity\PageCollection::NESTED_TYPE_COLLECTION) {

            if (!empty($page->getLayout()))
                $this->alphaLayoutTemplate = $page->getLayout();

            $items = $pageCollection->getItems();

            if (empty($items))
                return $this->redirect()->toRoute('home');

            $firstItem = $items[0];

            $param1 = $this->params('param1', null);
            $param2 = $this->params('param2', null);
            $param3 = $this->params('param3', null);


            if (empty($param1)) {
                $item = $firstItem;
            } else if (empty($param2)) {
                $item = $this->pageCollectionService->getPageCollectionItemByRouteLabel($pageCollection, $param1);
            } else if (empty($param3)) {
                $item = $this->pageCollectionService->getPageCollectionItemByRouteLabel($pageCollection, $param2);
            } else {
                $item = $this->pageCollectionService->getPageCollectionItemByRouteLabel($pageCollection, $param3);
            }

            $this->alphaPage = $page;
            $this->setCollectionItem($item);
            $this->alphaTemplate = 'alpha-page/page/view.phtml';
            return $this->alphaReturn();
//            $view = new ViewModel();
//            $view->setTemplate('alpha-page/page/view.phtml');
//            $view->setVariable('page', $page);
//            $view->setVariable('pageCollection', $pageCollection);
//            $view->setVariable('item', $item);
//            return $view;
        }



        //get page collection items for listing
        $pageCollectionItems = $pageCollection->getItems();

        //check if filter params exist
        //if does filter the items for listing according to filter
        $year = $this->params('param1', null);
        $month = $this->params('param2', null);
        if (!empty($year) && !empty($month)) {
            $pageCollectionItems = $this->pageCollectionService->filterPageCollectionByYearAndMonth($pageCollection, $year, $month);
        }

        $pageCollectionCountsForYearsAndMonths = $this->pageCollectionService->getPageCollectionItemCountForYearsAndMonths($pageCollection);

        $view = new ViewModel();
        $view->setTemplate('alpha-page/page/view.phtml');
        $view->setVariable('page', $page);
        $view->setVariable('pageCollection', $pageCollection);
        $view->setVariable('pageCollectionItems', $pageCollectionItems);
        $view->setVariable('pageCollectionCount', $pageCollectionCountsForYearsAndMonths);
        return $view;
    }

    public function itemAction() {

        $id = $this->params('param1');

        if (empty($id))
            return $this->redirect()->toRoute('home');

        $routeName = $this->router->match($this->getRequest())->getMatchedRouteName();

        $parentRoute = null;
        if (strpos($routeName, '/') !== false) {
            $parentRouteName = explode('/', $routeName)[0];
            $routeName = explode('/', $routeName)[1];
            $parentRoute = $this->entityManager->getRepository('Alpha\Entity\AlphaRoute')->findOneBy(['name' => $parentRouteName, 'parentRoute' => null]);
        }

        $route = $this->entityManager->getRepository('Alpha\Entity\AlphaRoute')->findOneBy(['name' => $routeName, 'parentRoute' => $parentRoute]);
        $page = $route->getPage();

        $pageCollectionItem = $this->pageCollectionService->getPageCollectionItemById($id);
        $recentPageCollectionItems = $this->pageCollectionService->getRecentPageCollectionItems($pageCollectionItem->getPageCollection());

        if (!empty($page->getLayout()))
            $this->layout($page->getLayout());

        $view = new ViewModel();
        $view->setTemplate('alpha-page/page/view.phtml');
        $view->setVariable('page', $page);
        $view->setVariable('pageCollectionItem', $pageCollectionItem);
        $view->setVariable('recentPageCollectionItems', $recentPageCollectionItems);
        return $view;
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
