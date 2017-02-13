<?php

namespace AlphaPage\Service;

use Doctrine\ORM\EntityManager;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionService {

    private $config;
    private $entityManager;

    public function __construct($config, EntityManager $entityManager) {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function getPageCollectionByName($name) {
        return $this->entityManager->getRepository('AlphaPage\Entity\PageCollection')
                        ->findOneBy(['name' => $name]);
    }

    public function getPageCollectionByType($collectionType) {

        $pageCollection = $this->entityManager->getRepository('AlphaPage\Entity\PageCollection')
                ->findBy(['collectionType' => $collectionType]);

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
                 FROM  `alpha_pages_collection_items`
                 WHERE id <> '-1' AND `page_collection_id` = :id
                 GROUP BY YEAR( DATE ) , MONTH( DATE ) 
                 ORDER BY DATE DESC";

        $prepartedStatement = $this->entityManager->getConnection()->prepare($sql);
        $prepartedStatement->execute(['id' => $pageCollection->getId()]);
        return $prepartedStatement->fetchAll();
    }

    public function filterPageCollectionByYearAndMonth($year, $month) {

        $startDate = new \DateTime();
        $startDate->setDate($year, $month, '01');
        $startDate->setTime('00', '00', '00');

        $endDate = new \DateTime();
        $endDate->setDate($year, $month, '01');
        $endDate->setTime('00', '00', '00');
        $endDate->modify("+1 Month");



        $query = $this->entityManager->createQuery(
                "SELECT 
                    n FROM \MembersArea\Entity\NewsAndEvents n
                 WHERE 
                    n.date >= :startdate AND 
                    n.date <= :enddate   AND
                    n.id <> :previewid
                 ORDER BY
                    n.date ASC"
        );

        $query->setParameter('startdate', $startDate);
        $query->setParameter('enddate', $endDate);
        $query->setParameter('previewid', NewsAndEvents::PREVIEW_ARTICLE_ID);

        $articles = $query->getResult();

        return $articles;
    }

    //Page Collections CRUD Stuff
    public function getAllPageCollections() {
        return $this->entityManager->getRepository('AlphaPage\Entity\PageCollection')->findAll();
    }

    public function createPageCollection() {
        
    }

    public function updatePageCollection() {
        
    }

    public function deletePageCollection() {
        
    }

}
