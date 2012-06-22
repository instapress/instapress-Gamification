<?php

/**
 * @author Ashish Kumar
 * @desc Plural Model class for GamificationVerbMaster DB class
 */
class Gamification_VerbMasters extends Gamification_AbstractPlural {

    protected $_dbClass = 'VerbMaster';

    public function getAllVerbMasters($clientId=1) {
        try {
            parent::__construct("count||Y", "clientId||$clientId");
            if ($this->getTotalCount() == 0)
                return false;
            parent::__construct("quantity||" . $this->getTotalCount(), "clientId||$clientId", "sortColumn||gamificationVerbName", "sortOrder||ASC");
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
