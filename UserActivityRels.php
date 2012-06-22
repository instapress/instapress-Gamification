<?php

/**
 * @author Ashish Kumar
 * @desc Plural Model class for GamificationUserActivityRel DB class
 */
class Gamification_UserActivityRels extends Gamification_AbstractPlural {

    protected $_dbClass = 'UserActivityRel';

    /**
     * @author Mayank Gupta 20111214$.
     * @desc Generalized method to fetch data.
     * @param Optional Integer $userId
     * @param Optional Integer $publicationId
     * @return Array containg objects
     */
    public function getAllGamificationUserActivityRelData($userId, $publicationId=0, $quantity=20, $pageNumber=1) {
        if (empty($userId) || !is_numeric($userId))
            throw new Exception('UserId should be a natural number.');
        try {
            if (!empty($publicationId))
                parent::__construct("userId||$userId", "publicationId||$publicationId", "count||Y");
            else
                parent::__construct("userId||$userId", "count||Y");
            if ($this->getTotalCount() == 0) {
                return false;
//                parent::__construct("count||Y");
//                if ($this->getTotalCount() == 0)
//                    return false;
//                if ($this->getTotalCount() < $quantity)
//                    $quantity = $this->getTotalCount();
//                parent::__construct("quantity||$quantity");
            } else {
                if ($this->getTotalCount() < $quantity && $this->getTotalPages() < $pageNumber) {
                    $quantity = $this->getTotalCount();
                    $pageNumber = $this->getTotalPages();
                }
                if (!empty($publicationId))
                    parent::__construct("userId||$userId", "publicationId||$publicationId", "quantity||$quantity", "pageNumber||$pageNumber");
                else
                    parent::__construct("userId||$userId", "quantity||$quantity", "pageNumber||$pageNumber");
            }
            $result = array();
            $result['data'] = $this->_matchedRecords;
            $result['totalResult'] = $this->getTotalCount();
            $result['totalPages'] = $this->getTotalPages();
            return $result;
        } catch (Exception $e) {
//            Instapress_Core_Helper::describe($e->getMessage());
//            Instapress_Core_Helper::describe($e->getTraceAsString());
            Log4Php_Logger::configure(APP_PATH.'/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog( '','',Log4Php_LoggerLevel::getLevelError(),$e->getMessage());
        }
    }

    public function getAllActivities($publicationId=0, $quantity=20) {
        try {
            if (!empty($publicationId))
                parent::__construct("publicationId||$publicationId", "count||Y");
            else
                parent::__construct("count||Y");
            if ($this->getTotalCount() == 0) {
                return false;
//                parent::__construct("count||Y");
//                if ($this->getTotalCount() == 0)
//                    return false;
//                if ($this->getTotalCount() < $quantity)
//                    $quantity = $this->getTotalCount();
//                parent::__construct("quantity||$quantity");
            } else {
                if ($this->getTotalCount() < $quantity)
                    $quantity = $this->getTotalCount();
                if (!empty($publicationId))
                    parent::__construct("publicationId||$publicationId", "quantity||$quantity");
                else
                    parent::__construct("quantity||$quantity");
            }
            return $this->_matchedRecords;
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
