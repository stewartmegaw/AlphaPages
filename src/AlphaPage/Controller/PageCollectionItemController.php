<?php

namespace AlphaPage\Controller;

use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use AlphaPage\Service\PageCollectionService;
use AlphaPage\Form\PageCollectionItemForm;
use AlphaPage\Form\PageCollectionItemFormFilter;
use DoctrineORMModule\Form\Element as DoctrineElement;
use Doctrine\Common\Collections\Criteria;

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
        $id = $this->params('collectionId');

        if (empty($id))
            return $this->redirect()->toRoute('home');

        $pageCollection = $this->pageCollectionService->getPageCollectionById($id);
        return new ViewModel(['pageCollection' => $pageCollection]);
    }

    public function createAction() {

        $collectionId = $this->params('collectionId');
        if (empty($collectionId)) {
            return $this->redirect()->toRoute('home');
        }

        $pageCollection = $this->pageCollectionService->getPageCollectionById($collectionId);

        $form = new PageCollectionItemForm();
        $filter = new PageCollectionItemFormFilter();

        if ($pageCollection->getType() === \AlphaPage\Entity\PageCollection::NESTED_TYPE_COLLECTION) {
            $e = new DoctrineElement\EntitySelect('parentItem');
            $e->setAttributes(array('id' => 'parentItemId', 'type' => 'select', 'allow_empty' => true, 'required' => false));
            $options = array(
                'property' => 'title',
                'target_class' => 'AlphaPage\Entity\PageCollectionItem',
                'empty_option' => "Don't have any parent item",
                'use_hidden_element' => true,
                'disable_inarray_validator' => true,
                'is_method' => true,
                'find_method' => array(
                    'name' => 'matching',
                    'params' => array(
                        'criteria' => Criteria::create()->where(
                                Criteria::expr()->eq('pageCollection', $pageCollection)
                        ),
                    ),
                ),
                'label_generator' => function($targetEntity) {
            $parents = $targetEntity->getParentsRecursive();
            $parents = array_reverse($parents);
            $hierarchy = '';
            foreach ($parents as $parent)
                $hierarchy .= ' > ' . $parent->getTitle();
            return (empty($hierarchy) ? '' : substr($hierarchy, 3) . ' > ') . $targetEntity->getTitle();
        },
            );
            $e->setOptions($options);
            $e->setLabel('Parent Item (if applicable)')->getProxy()->setObjectManager($this->entityManager);
            $form->add($e);

            $form->add(array(
                'name' => 'routeLabel',
                'type' => 'text',
                'attributes' => array(
                    'required' => 'required',
                    'placeholder' => 'Route Label',
                    'class' => 'form-control',
                ),
                'options' => array(
                    'label' => 'Route Label',
                    'label_attributes' => array(
                        'class' => 'label',
                    ),
                ),
            ));

            $e = new DoctrineElement\EntitySelect('redirect');
            $e->setAttributes(array('id' => 'redirectId', 'type' => 'select', 'allow_empty' => true, 'required' => false));
            $options = array(
                'property' => 'title',
                'target_class' => 'AlphaPage\Entity\PageCollectionItem',
                'empty_option' => "No redirect",
                'use_hidden_element' => true,
                'disable_inarray_validator' => true,
                'is_method' => true,
                'find_method' => array(
                    'name' => 'matching',
                    'params' => array(
                        'criteria' => Criteria::create()->where(
                                Criteria::expr()->eq('pageCollection', $pageCollection)
                        ),
                    ),
                ),
                'label_generator' => function($targetEntity) {

            return $targetEntity->getId() . ' - ' . $targetEntity->getTitle();
        },
            );
            $e->setOptions($options);
            $e->setLabel('Redirect To (if applicable)')->getProxy()->setObjectManager($this->entityManager);
            $form->add($e);
        }


        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
            $form->setData($data);
            $form->setInputFilter($filter);
            if ($form->isValid()) {
                try {
                    $this->pageCollectionService->createPageCollectionItem($collectionId, $data);
                    $this->flashMessenger()->addSuccessMessage('Collection Item Added Succesfully!');
                    return $this->redirect()->toRoute('alpha-page-collection-items', ['collectionId' => $collectionId, 'action' => 'manage']);
                } catch (\Exception $ex) {
                    $this->flashMessenger()->addWarningMessage('Unable to add collection item, please try again!');
                    return $this->redirect()->toRoute('alpha-page-collection-items', ['collectionId' => $collectionId, 'action' => 'manage']);
                }
            }
        }
        return new ViewModel(['form' => $form, 'collection' => $pageCollection]);
    }

    public function updateAction() {

        $collectionId = $this->params('collectionId', null);
        $itemId = $this->params('id', null);

        $form = new PageCollectionItemForm();
        $filter = new PageCollectionItemFormFilter();

        if (empty($collectionId) || empty($itemId))
            return $this->redirect()->toUrl('home');

        $item = $this->pageCollectionService->getPageCollectionItemById($itemId);
        $collection = $this->pageCollectionService->getPageCollectionById($collectionId);

        if ($collection->getType() === \AlphaPage\Entity\PageCollection::NESTED_TYPE_COLLECTION) {
            $e = new DoctrineElement\EntitySelect('parentItem');
            $e->setAttributes(array('id' => 'parentItemId', 'type' => 'select', 'allow_empty' => true, 'required' => false));
            $options = array(
                'property' => 'title',
                'target_class' => 'AlphaPage\Entity\PageCollectionItem',
                'empty_option' => "Don't have any parent item",
                'use_hidden_element' => true,
                'disable_inarray_validator' => true,
                'is_method' => true,
                'find_method' => array(
                    'name' => 'matching',
                    'params' => array(
                        'criteria' => Criteria::create()->where(
                                Criteria::expr()->eq('pageCollection', $collection)
                        ),
                    ),
                ),
                'label_generator' => function($targetEntity) {
            $parents = $targetEntity->getParentsRecursive();
            $parents = array_reverse($parents);
            $hierarchy = '';
            foreach ($parents as $parent)
                $hierarchy .= ' > ' . $parent->getTitle();
            return (empty($hierarchy) ? '' : substr($hierarchy, 3) . ' > ') . $targetEntity->getTitle();
        },
            );
            $e->setOptions($options);
            $e->setLabel('Parent Item (if applicable)')->getProxy()->setObjectManager($this->entityManager);
            $form->add($e);

            $form->add(array(
                'name' => 'routeLabel',
                'type' => 'text',
                'attributes' => array(
                    'required' => 'required',
                    'placeholder' => 'Route Label',
                    'class' => 'form-control',
                ),
                'options' => array(
                    'label' => 'Route Label',
                    'label_attributes' => array(
                        'class' => 'label',
                    ),
                ),
            ));

            $e = new DoctrineElement\EntitySelect('redirect');
            $e->setAttributes(array('id' => 'redirectId', 'type' => 'select', 'allow_empty' => true, 'required' => false));
            $options = array(
                'property' => 'title',
                'target_class' => 'AlphaPage\Entity\PageCollectionItem',
                'empty_option' => "No redirect",
                'use_hidden_element' => true,
                'disable_inarray_validator' => true,
                'is_method' => true,
                'find_method' => array(
                    'name' => 'matching',
                    'params' => array(
                        'criteria' => Criteria::create()->where(
                                Criteria::expr()->eq('pageCollection', $collection)
                        ),
                    ),
                ),
                'label_generator' => function($targetEntity) {

            return $targetEntity->getId() . ' - ' . $targetEntity->getTitle();
        },
            );
            $e->setOptions($options);
            $e->setLabel('Redirect To (if applicable)')->getProxy()->setObjectManager($this->entityManager);
            $form->add($e);
        }

        $form->bind($item);

        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
            $form->setData($data);
            $form->setInputFilter($filter);
            if ($form->isValid()) {
                try {
                    $this->pageCollectionService->updatePageCollectionItem($item->getId(), $data);
                    $this->flashMessenger()->addSuccessMessage('Collection Item Added Succesfully!');
                    return $this->redirect()->toRoute('alpha-page-collection-items', ['collectionId' => $collectionId, 'action' => 'manage']);
                } catch (\Exception $ex) {
                    $this->flashMessenger()->addWarningMessage('Unable to add collection item, please try again!');
                    return $this->redirect()->toRoute('alpha-page-collection-items', ['collectionId' => $collectionId, 'action' => 'manage']);
                }
            }
        }

        return new ViewModel(['form' => $form, 'collection' => $collection, 'item' => $item]);
    }

    public function deleteAction() {
        $itemId = $this->params('id', null);
        $collectionId = $this->params('collectionId', null);

        if (empty($itemId)) {
            $this->flashMessenger()->addWarningMessage('Something went wrong! Please try again');
            return $this->redirect()->toRoute('alpha-page-collection-items', ['collectionId' => $collectionId, 'action' => 'manage']);
        }

        try {
            $this->pageCollectionService->deletePageCollectionItem($itemId);
            $this->flashMessenger()->addSuccessMessage('Collection Item Deleted Succesfully!');
            return $this->redirect()->toRoute('alpha-page-collection-items', ['collectionId' => $collectionId, 'action' => 'manage']);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addWarningMessage('Unable to delete collection item, please try again!');
            return $this->redirect()->toRoute('alpha-page-collection-items', ['collectionId' => $collectionId, 'action' => 'manage']);
        }
    }

    public function previewAction() {
        $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
        $this->pageCollectionService->updatePageCollectionItem(\AlphaPage\Entity\PageCollectionItem::PREVIEW_ID, $data);
        return $this->redirect()->toRoute('news-and-events/news-and-events-article', ['param1' => -1, 'param2' => 'preview']);
    }

}
