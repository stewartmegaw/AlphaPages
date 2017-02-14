<?php

namespace AlphaPage\Service;

use Doctrine\ORM\EntityManager;
use AlphaPage\Entity\PageCollection;
use AlphaPage\Entity\PageCollectionItem;
use AlphaPage\Entity\PageCollectionItemFiles;

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

        //UPLOADING BANNER FILES
        $thumb_file_id = $this->upload_file($item, $data["file"]);

        //CREATING AND UPLOADING THUMBNAIL FROM FIRST BANNER FILE
        if ($thumb_file_id !== null) {
            $thumb_basename = basename($data["file"]["name"]);
            $thumb_path = "data/uploads_tmp/" . $thumb_basename;
            if ($this->createThumbnail("data/uploads/" . $thumb_file_id, $thumb_path, 240, 180)) {
                $this->upload_thumbnail($item, $thumb_basename, $thumb_path);
            }
        }
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
            //UPLOADING BANNER FILES
            $thumb_file_id = $this->upload_file($item, $data["file"]);

            //CREATING AND UPLOADING THUMBNAIL FROM FIRST BANNER FILE
            if ($thumb_file_id !== null) {
                $thumb_basename = basename($data["file"]["name"]);
                $thumb_path = "data/uploads_tmp/" . $thumb_basename;
                if ($this->createThumbnail("data/uploads/" . $thumb_file_id, $thumb_path, 240, 180)) {
                    $this->upload_thumbnail($item, $thumb_basename, $thumb_path);
                }
            }
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

            $this->entityManager->remove($file);
        }

        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    private function createThumbnail($filepath, $thumbpath, $thumbnail_width, $thumbnail_height, $background = false) {
        list($original_width, $original_height, $original_type) = getimagesize($filepath);

        /* if ($original_width > $original_height) {
          $new_width = $thumbnail_width;
          $new_height = intval($original_height * $new_width / $original_width);
          } else {
          $new_height = $thumbnail_height;
          $new_width = intval($original_width * $new_height / $original_height);
          } */

        $desired_height = floor($original_height * ($thumbnail_width / $original_width));


        //$dest_x = intval(($thumbnail_width - $new_width) / 2);
        //$dest_y = intval(($thumbnail_height - $new_height) / 2);
        if ($original_type === 1) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        } else if ($original_type === 2) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        } else if ($original_type === 3) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        } else {
            return false;
        }
        $old_image = $imgcreatefrom($filepath);
        $new_image = imagecreatetruecolor($thumbnail_width, $desired_height); // creates new image, but with a black background
        // figuring out the color for the background
        if (is_array($background) && count($background) === 3) {
            list($red, $green, $blue) = $background;
            $color = imagecolorallocate($new_image, $red, $green, $blue);
            imagefill($new_image, 0, 0, $color);
            // apply transparent background only if is a png image
        } else if ($background === 'transparent' && $original_type === 3) {
            imagesavealpha($new_image, TRUE);
            $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
            imagefill($new_image, 0, 0, $color);
        }
        imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $thumbnail_width, $desired_height, $original_width, $original_height);
        $imgt($new_image, $thumbpath);
        return file_exists($thumbpath);
    }

    private function upload_thumbnail($item, $thumb_basename, $thumb_path) {
        $adapter = new \RdnUpload\Adapter\Local('data/uploads', '/files');
        $uploads = new \RdnUpload\Container($adapter);

        $input = new \RdnUpload\File\File($thumb_basename, $thumb_path);
        $image_id = $uploads->upload($input);

        $itemFile = new PageCollectionItemFiles();
        $itemFile->setFile($image_id);
        $itemFile->setPageCollectionItem($item);
        $itemFile->setType(PageCollectionItem::ITEM_THUMB);
        $this->entityManager->persist($itemFile);
    }

    private function upload_file($item, $file) {
        $adapter = new \RdnUpload\Adapter\Local('data/uploads', '/files');
        $uploads = new \RdnUpload\Container($adapter);

        $thumb_id = null;
        //Uploading Images One by One
        if ($file["size"] > 0) {
            $input = new \RdnUpload\File\Input($file);
            $image_id = $uploads->upload($input);
            $thumb_id = $image_id;

            $itemFile = new PageCollectionItemFiles();
            $itemFile->setFile($image_id);
            $itemFile->setPageCollectionItem($item);
            $itemFile->setType(PageCollectionItem::ITEM_BANNER);
            $this->entityManager->persist($itemFile);
        }

        return $thumb_id;
    }

    private function nl2br2($string) {
        $string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
        return $string;
    }

}
