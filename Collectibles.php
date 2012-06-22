<?php

/**
 * @author Ashish Kumar
 * @desc Plural Model class for GamificationCollectible DB class
 */
class Gamification_Collectibles extends Gamification_AbstractPlural {

    protected $_dbClass = 'Collectible';

    public static function createCollectibles($publicationId, $activityObject, $contentType, $activityCount, $collectibleName, $collectibleDescription, $clientId, $createdBy, $collectibleImageUrl='') {
        if (empty($publicationId) || !is_numeric($publicationId))
            throw new Exception('PublicationId should be a natural number.');
        if (empty($activityObject))
            throw new Exception('Please select Object Type.');
        if (empty($contentType))
            throw new Exception('Please select Content Type.');
        if (empty($activityCount) || !is_numeric($activityCount))
            throw new Exception('ActivityCount should be a natural number.');
        if (empty($collectibleName))
            throw new Exception('CollectibleName couldn\'t blank.');
        if (empty($collectibleDescription))
            throw new Exception('CollectibleDescription couldn\'t blank.');
//        if (empty($collectibleImageUrl))
//            throw new Exception('CollectibleImageUrl couldn\'t blank.');
        if (empty($clientId) || !is_numeric($clientId))
            throw new Exception('ClientId should be a natural number.');
        if (empty($createdBy))
            throw new Exception('CreatedBy should be a natural number.');

        try {
            $collectibleDbObject = new Gamification_Db_Collectible('add');
            $collectibleDbObject->set("publicationId||$publicationId", "activityObject||$activityObject", "contentType||$contentType", "activityCount||$activityCount", "collectibleName||$collectibleName", "collectibleDescription||$collectibleDescription", "collectibleImageUrl||$collectibleImageUrl", "clientId||$clientId", "createdBy||$createdBy");
            return $collectibleDbObject->getLastInsertedId();
        } catch (Exception $e) {
//            Instapress_Core_Helper::describe($e->getMessage());
//            Instapress_Core_Helper::describe($e->getTraceAsString());
            Log4Php_Logger::configure(APP_PATH.'/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog( '','',Log4Php_LoggerLevel::getLevelError(),$e->getMessage());
        }
    }

    /**
     * @author Mayank Gupta 20111214$.
     * @desc Generalized method to fetch data.
     * @param Optional Integer $publicationId
     * @return Array containg objects
     */
    public function getAllGamificationCollectiblesData($publicationId=0) {
        try {
            if (!empty($publicationId))
                parent::__construct("publicationId||$publicationId", "count||Y");
            else
                parent::__construct("count||Y");
            $totalRecords = $this->_totalCount;
            if ($totalRecords > 0) {
                if (!empty($publicationId))
                    parent::__construct("publicationId||$publicationId", "order||N", "quantity||$totalRecords");
                else
                    parent::__construct("order||N", "quantity||$totalRecords");
                return $this->_matchedRecords;
            }
            return false;
        } catch (Exception $e) {
//            Instapress_Core_Helper::describe($e->getMessage());
//            Instapress_Core_Helper::describe($e->getTraceAsString());
            Log4Php_Logger::configure(APP_PATH.'/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog( '','',Log4Php_LoggerLevel::getLevelError(),$e->getMessage());
        }
    }

}

?>
