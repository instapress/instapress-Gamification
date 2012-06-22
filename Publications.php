<?php

/**
 * Singular Publication class.
 *
 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
 * @version 1.0
 * @copyright Instamedia
 */
class Gamification_Publications extends Gamification_AbstractPlural {

    protected $_dbClass = 'Publication';

    /*
     * @author Iftikar Khan
     * @return array Gamification_Publication
     */

    public function getAllPublication($clientId=1) {
        try {
            parent::__construct("count||Y", "clientId||$clientId");
            $totalCount = $this->getTotalCount();
            if ($totalCount > 0) {
                parent::__construct("quantity||$totalCount", "clientId||$clientId", "sortColumn||publicationName", "sortOrder||ASC");
                return $this->_matchedRecords;
            }else
                return array();
        } catch (Exception $e) {
//            Instapress_Core_Helper::describe($e->getMessage());
//            Instapress_Core_Helper::describe($e->getTraceAsString());
            Log4Php_Logger::configure(APP_PATH.'/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog( '','',Log4Php_LoggerLevel::getLevelError(),$e->getMessage());
            return array();
        }
    }

}