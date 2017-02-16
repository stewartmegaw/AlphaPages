<?php

namespace AlphaPage\Service;

use Doctrine\ORM\EntityManager;
use AlphaPage\Entity\PageCollection;
use AlphaPage\Entity\PageCollectionItem;
use AlphaFiles\Service\AlphaFileService;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionService {

    private $config;
    private $entityManager;
    private $alphaFileService;

    public function __construct($config, EntityManager $entityManager, AlphaFileService $alphaFileService) {
        $this->config = $config;
        $this->entityManager = $entityManager;
        $this->alphaFileService = $alphaFileService;
    }

    public function getPageCollectionByName($name) {
        return $this->entityManager->getRepository('AlphaPage\Entity\PageCollection')->findOneBy(['name' => $name]);
    }

    public function getPageCollectionById($id) {
        return $this->entityManager->getRepository('AlphaPage\Entity\PageCollection')->find($id);
    }

    public function getPageCollectionByType($collectionType) {
        $pageCollection = $this->entityManager->getRepository('AlphaPage\Entity\PageCollection')->findBy(['collectionType' => $collectionType]);
        return $pageCollection;
    }

    public function getPageCollectionItemById($id) {
        return $this->entityManager->getRepository('AlphaPage\Entity\PageCollectionItem')->find($id);
    }

    /**
     * 
     * @param Object $type AlphaPage\Entity\PageCollectionType
     * @return array Array of objects of AlphaPage\Entity\PageCollection
     */
    public function getRecentPageCollectionItems($pageCollection) {
        $query = $this->entityManager->createQuery(
                "SELECT n FROM \AlphaPage\Entity\PageCollectionItem n "
                . "WHERE n.pageCollection = :pageCollection "
                . "ORDER BY n.id DESC "
        );

        $query->setParameter('pageCollection', $pageCollection);

        $pageCollectionItems = $query->setMaxResults(4)->getResult();

        return $pageCollectionItems;
    }

    public function getPageCollectionItemCountForYearsAndMonths($pageCollection) {

        $sql = " SELECT YEAR(DATE) as year, MONTH( DATE ) as month, COUNT( id ) as count
                 FROM  `alpha_page_collection_items`
                 WHERE id <> '-1' AND `page_collection_id` = :id
                 GROUP BY YEAR( DATE ) , MONTH( DATE ) 
                 ORDER BY DATE DESC";

        $prepartedStatement = $this->entityManager->getConnection()->prepare($sql);
        $prepartedStatement->execute(['id' => $pageCollection->getId()]);
        return $prepartedStatement->fetchAll();
    }

    public function filterPageCollectionByYearAndMonth($pageCollection, $year, $month) {

        $collection = $pageCollection;

        $startDate = new \DateTime();
        $startDate->setDate($year, $month, '01');
        $startDate->setTime('00', '00', '00');

        $endDate = new \DateTime();
        $endDate->setDate($year, $month, '01');
        $endDate->setTime('00', '00', '00');
        $endDate->modify("+1 Month");



        $query = $this->entityManager->createQuery(
                "SELECT 
                    n FROM \AlphaPage\Entity\PageCollectionItem n
                 WHERE 
                    n.date >= :startdate AND 
                    n.date <= :enddate   AND
                    n.pageCollection = :collection AND 
                    n.id <> :previewid
                 ORDER BY
                    n.date ASC"
        );

        $query->setParameter('startdate', $startDate);
        $query->setParameter('enddate', $endDate);
        $query->setParameter('collection', $collection);
        $query->setParameter('previewid', -1);

        $articles = $query->getResult();

        return $articles;
    }

    //Page Collections CRUD Stuff
    public function getAllPageCollections() {
        return $this->entityManager->getRepository('AlphaPage\Entity\PageCollection')->findAll();
    }

    public function createPageCollection($data) {
        $collection = new PageCollection();
        $collection->setName($data['name']);
        $collection->setDescription($data['description']);
        $this->entityManager->persist($collection);
        $this->entityManager->flush();
    }

    public function updatePageCollection($id, $data) {
        $collection = $this->getPageCollectionById($id);
        $collection->setName($data['name']);
        $collection->setDescription($data['description']);
        $this->entityManager->flush();
    }

    public function deletePageCollection($id) {

        $adapter = new \RdnUpload\Adapter\Local('data/uploads', '/files');
        $uploads = new \RdnUpload\Container($adapter);

        $collection = $this->getPageCollectionById($id);

        $items = $collection->getItems();

        foreach ($items as $item) {
            $files = $item->getFiles();
            foreach ($files as $file) {
                if ($uploads->has($file->getFile()))
                    $uploads->delete($file->getFile());

                $this->entityManager->remove($file);
            }

            $this->entityManager->remove($item);
        }

        $this->entityManager->remove($collection);
        $this->entityManager->flush();
    }

    public function createPageCollectionItem($collectionId, $data) {

        $collection = $this->getPageCollectionById($collectionId);

        $item = new PageCollectionItem();
        $now = date_create(date("Y-m-d H:i:s"));

        $item->setTitle(trim($data["title"]));
        $item->setType($data["type"]);
        $item->setDate(date_create($data["date"]));
        if ($data["externalUrl"] !== "") {
            $item->setExternalUrl($data["externalUrl"]);
        } else {
            $item->setExternalUrl("www.google.com");
        }
        $item->setSmallDescription($data["smallDescription"]);
        $item->setDescription($this->nl2br2($data["description"]));
        $item->setDateCreated($now);
        $item->setPageCollection($collection);

        $this->alphaFileService->addImageFile($item, $data['file']);

        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    public function updatePageCollectionItem($id, $data) {

        $adapter = new \RdnUpload\Adapter\Local('data/uploads', '/files');
        $uploads = new \RdnUpload\Container($adapter);

        $item = $this->getPageCollectionItemById($id);
        $now = date_create(date("Y-m-d H:i:s"));

        $item->setTitle(trim($data["title"]));
        $item->setType($data["type"]);
        $item->setDate(date_create($data["date"]));
        if ($data["externalUrl"] !== "") {
            $item->setExternalUrl($data["externalUrl"]);
        } else {
            $item->setExternalUrl("www.google.com");
        }
        $item->setSmallDescription($data["smallDescription"]);
        $item->setDescription($this->nl2br2($data["description"]));
        $item->setDateCreated($now);

        if ($data['file']['size'] > 0) {
            $files = $item->getFiles();
            foreach ($files as $file) {
                if ($uploads->has($file->getFile()))
                    $uploads->delete($file->getFile());

                $this->entityManager->remove($file);
            }
            $this->alphaFileService->addImageFile($item, $data['file']);
        }

        $this->entityManager->flush();
    }

    public function deletePageCollectionItem($id) {

        $adapter = new \RdnUpload\Adapter\Local('data/uploads', '/files');
        $uploads = new \RdnUpload\Container($adapter);

        $item = $this->getPageCollectionItemById($id);

        $files = $item->getFiles();
        foreach ($files as $file) {
            if ($uploads->has($file->getFile()))
                $uploads->delete($file->getFile());

            $item->deleteFile($file);
            $this->entityManager->remove($file);
            $this->entityManager->flush();
        }


        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    private function nl2br2($string) {
        $string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
        return $string;
    }

}
