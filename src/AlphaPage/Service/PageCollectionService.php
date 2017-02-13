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

    public function getPageCollectionTypeByName($name) {
        $collectionType = $this->entityManager->getRepository('AlphaPage\Entity\PageCollectionType')
                ->findOneBy(['name' => $name]);

        return $collectionType;
    }

    public function getPageCollectionByType($collectionType) {

        $pageCollection = $this->entityManager->getRepository('AlphaPage\Entity\PageCollection')
                ->findBy(['collectionType' => $collectionType]);

        return $pageCollection;
    }

    public function getPageCollectionItemById($id) {
        return $this->entityManager->getRepository('AlphaPage\Entity\PageCollection')->find($id);
    }

    /**
     * 
     * @param Object $type AlphaPage\Entity\PageCollectionType
     * @return array Array of objects of AlphaPage\Entity\PageCollection
     */
    public function getRecentPageCollectionItemsByType($type) {
        $query = $this->entityManager->createQuery(
                "SELECT n FROM \AlphaPage\Entity\PageCollection n "
                . "WHERE n.collectionType = :type "
                . "ORDER BY n.id DESC "
        );

        $query->setParameter('type', $type);

        $pageCollectionItems = $query->setMaxResults(4)->getResult();

        return $pageCollectionItems;
    }

    public function getPageCollectionCountForYearsAndMonths($name) {
        $collectionTypeId = $this->entityManager->getRepository('AlphaPage\Entity\PageCollectionType')
                        ->findOneBy(['name' => $name])->getId();

        $sql = " SELECT YEAR(DATE) as year, MONTH( DATE ) as month, COUNT( id ) as count
                 FROM  `alpha_pages_collections`
                 WHERE id <> '-1' AND `page_collection_type_id` = :type
                 GROUP BY YEAR( DATE ) , MONTH( DATE ) 
                 ORDER BY DATE DESC";

        $prepartedStatement = $this->entityManager->getConnection()->prepare($sql);
        $prepartedStatement->execute(['type' => $collectionTypeId]);

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

}
