<?php

/**
 * @author Mayank Gupta 20111214$.
 * @desc Plural Model class for FriendActivityStream DB class.
 */
class Gamification_FriendActivityStreams extends Gamification_AbstractPlural {

    protected $_dbClass = 'FriendActivityStream';

    /**
     * @author: Mayank Gupta 20111214$.
     * @desc Method fetch all their Friends data from gamification_friend_activity_stream table.
     * @param Integer $userId
     * @return Array that contains object. 
     */
    public function getUserFriendsActivityStream($userId, $publicationId, $quantity=20) {
        if (empty($userId) || !is_numeric($userId))
            throw new Exception('UserId should be a natural number.');
        if (empty($publicationId) || !is_numeric($publicationId))
            throw new Exception('PublicationId should be a natural number.');
        try {
//            if (!empty($publicationId))
//                parent::__construct("userId||$userId", "publicationId||$publicationId", "count||Y");
//            else
//                parent::__construct("userId||$userId", "count||Y");
            parent::__construct("userId||$userId", "publicationId||$publicationId", "count||Y");
            if ($this->getTotalCount() == 0)
                return false;
            if ($this->getTotalCount() < $quantity)
                $quantity = $this->getTotalCount();
            parent::__construct("userId||$userId", "publicationId||$publicationId", "quantity||$quantity");
//            if ($this->getTotalCount() == 0) {
//                parent::__construct("count||Y");
//                if ($this->getTotalCount() == 0)
//                    return false;
//                if ($this->getTotalCount() < $quantity)
//                    $quantity = $this->getTotalCount();
//                parent::__construct("quantity||$quantity");
//            }else {
//                if ($this->getTotalCount() < $quantity)
//                    $quantity = $this->getTotalCount();
//                if (!empty($publicationId))
//                    parent::__construct("userId||$userId", "publicationId||$publicationId", "quantity||$quantity");
//                else
//                    parent::__construct("userId||$userId", "quantity||$quantity");
//            }
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
