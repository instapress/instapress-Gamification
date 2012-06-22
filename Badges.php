<?php

/**
 * @author Ashish Kumar
 * @desc Plural Model class for GamificationBadge DB class
 */
class Gamification_Badges extends Gamification_AbstractPlural {

    protected $_dbClass = 'Badge';

    /**
     * @author Mayank Gupta 20111214$.
     * @desc Generalized method to fetch data.
     * @param Optional Integer $publicationId
     * @return Array containg objects
     */
    public function getAllGamificationBadgesData($publicationId=0) {
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
