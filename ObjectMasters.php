<?php

/**
 * @author Ashish Kumar
 * @desc Plural Model class for GamificationObjectMaster DB class
 */
class Gamification_ObjectMasters extends Gamification_AbstractPlural {

    protected $_dbClass = 'ObjectMaster';

    /**
     * @author: Mayank Gupta 20111212$.
     * @desc: Method return all table data if records exists.
     * @return type 
     */
    public function getAllObjectMasters($clientId=1) {
        try {
            parent::__construct("count||Y", "clientId||$clientId");
            if ($this->getTotalCount() == 0)
                return false;
            parent::__construct("quantity||" . $this->getTotalCount(), "clientId||$clientId", "sortColumn||gamificationObjectName", "sortOrder||ASC");
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
