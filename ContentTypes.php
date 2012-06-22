<?php

/**
 * @author Ashish Kumar
 * @desc Plural Model class for GamificationContentType DB class
 */
class Gamification_ContentTypes extends Gamification_AbstractPlural {

    protected $_dbClass = 'ContentType';

    /**
     *
     * @param type $activityObject
     * @param type $contentType
     * @param type $clientId
     * @param type $createdBy 
     */
    public static function createContentTypes($activityObject, $contentType, $clientId, $createdBy) {
        if (empty($activityObject))
            throw new Exception('Please select Object Type.');
        if (empty($contentType))
            throw new Exception('Please select Content Type.');
        if (empty($clientId) || !is_numeric($clientId))
            throw new Exception('ClientId should be a natural number.');
        if (empty($createdBy))
            throw new Exception('CreatedBy should be a natural number.');
        try {
            $contentTypeDbObject = new Gamification_Db_ContentType('add');
            $contentTypeDbObject->set("activityObject||$activityObject", "contentType||$contentType", "clientId||$clientId", "createdBy||$createdBy");
            return $contentTypeDbObject->getLastInsertedId();
        } catch (Exception $e) {
//            Instapress_Core_Helper::describe($e->getMessage());
//            Instapress_Core_Helper::describe($e->getTraceAsString());
            Log4Php_Logger::configure(APP_PATH.'/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog( '','',Log4Php_LoggerLevel::getLevelError(),$e->getMessage());
        }
    }

    /**
     *
     * @return type 
     */
    public function getAllOurContentTypes($userId=0) {
        try {
            if (!empty($userId))
                parent::__construct("count||Y", "createdBy||$userId");
            else
                parent::__construct("count||Y");
            if ($this->getTotalCount() == 0)
                return false;
            if (!empty($userId))
                parent::__construct("quantity||" . $this->getTotalCount(), "createdBy||$userId");
            else
                parent::__construct("quantity||" . $this->getTotalCount());
            return $this->_matchedRecords;
        } catch (Exception $e) {
            Instapress_Core_Helper::describe($e->getMessage());
            Instapress_Core_Helper::describe($e->getTraceAsString());
        }
    }

    /**
     *
     * @return type 
     */
    public function getAllOurContentTypesOnActivityObject($activityObject, $userId=0) {
        try {
            if (!empty($userId))
                parent::__construct("activityObject||$activityObject", "count||Y", "createdBy||$userId");
            else
                parent::__construct("activityObject||$activityObject", "count||Y");
            if ($this->getTotalCount() == 0)
                return false;
            if (!empty($userId))
                parent::__construct("activityObject||$activityObject", "quantity||" . $this->getTotalCount(), "createdBy||$userId");
            else
                parent::__construct("activityObject||$activityObject", "quantity||" . $this->getTotalCount());
            return $this->_matchedRecords;
        } catch (Exception $e) {
            Instapress_Core_Helper::describe($e->getMessage());
            Instapress_Core_Helper::describe($e->getTraceAsString());
        }
    }

    /**
     *
     * @param type $activityObject
     * @param type $userId
     * @return type 
     */
    public function getContentTypeOnBehalfOfUser($activityObject, $userId) {
        try {
            $resultSetArray = $this->getAllOurContentTypesOnActivityObject($activityObject, $userId);
            if (is_array($resultSetArray) && count($resultSetArray) > 0) {
                $finalResultArray = array();
                foreach ($resultSetArray as $resultSetDataObject) {
                    $finalResultArray[] = $resultSetDataObject->getContentType();
                }
            }
            return (is_array($finalResultArray) && count($finalResultArray) > 0) ? $finalResultArray : false;
        } catch (Exception $e) {
            Instapress_Core_Helper::describe($e->getMessage());
            Instapress_Core_Helper::describe($e->getTraceAsString());
        }
    }

}

?>
