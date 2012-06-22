<?php

/**
 * 
 * @desc Plural Model class for UserCollectibleRel DB class
 */
class Gamification_UserCollectibleRels extends Gamification_AbstractPlural {

    protected $_dbClass = 'UserCollectibleRel';

    /**
     * @author Mayank Gupta 20111214$.
     * @desc Generalized method to fetch data.
     * @param Optional Integer $userId
     * @param Optional Integer $publicationId
     * @return Array containg objects
     */
    public function getAllGamificationUserCollectibleRelData($userId=0, $publicationId=0) {
        try {
            if (!empty($userId) && !empty($publicationId))
                parent::__construct("userId||$userId", "publicationId||$publicationId", "count||Y");
            elseif (!empty($userId))
                parent::__construct("userId||$userId", "count||Y");
            elseif (!empty($publicationId))
                parent::__construct("publicationId||$publicationId", "count||Y");
            else
                parent::__construct("count||Y");
            $totalRecords = $this->_totalCount;
            if ($totalRecords > 0) {
                if (!empty($userId) && !empty($publicationId))
                    parent::__construct("userId||$userId", "publicationId||$publicationId", "order||N", "quantity||$totalRecords");
                elseif (!empty($userId))
                    parent::__construct("userId||$userId", "order||N", "quantity||$totalRecords");
                elseif (!empty($publicationId))
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
