<?php

/**
 * @author: Ashish Kumar
 * @desc: This file contains miscellenous functions.
 */
class Gamification_Utility {

    /**
     * @author Ashish Kumar 20111213$.
     * @desc Method use to handle the activity done by the user and reward different badges,collectible or moderation to the eligible users
     * @param Integer $userId
     * @param Integer $contentId
     * @param String $contentType
     * @param Integer $contentCreatorId
     * @param String $activityOwnership
     * @param String $activityObject
     * @param String $activityVerb
     * @param Integer $publicationId
     * @param Integer $clientId
     */
    //  Gamification_Utility::postRequestToGamify($createdBy, $elementId, $groupName, $createdBy, "MINE", $activityObject, "ADD", $publicationId, 1);
    public static function postRequestToGamify($userId, $contentId, $contentType, $contentCreatorId, $activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId = 1) {
        $record = array();

        $userId = trim($userId);
        $contentId = trim($contentId);
        $contentType = trim($contentType);
        $contentCreatorId = trim($contentCreatorId);
        $activityOwnership = trim($activityOwnership);
        $activityObject = trim($activityObject);
        $activityVerb = trim($activityVerb);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($contentId)) {
            $contentId = 0;
        }
        if (empty($contentType)) {
            $contentType = 'none';
        }
        if (empty($userId) || empty($contentCreatorId) || empty($activityOwnership) || empty($activityObject) || empty($activityVerb) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or contentCreatorId or activityOwnership or activityObject or activityVerb or publicationId or clientId is not available";
        } else {
            try {

                // getting the name of activity
                $activityName = $activityOwnership . "_" . $activityObject . "_" . $activityVerb;

                $activityTypeObject = new Gamification_ActivityTypes("activityName||$activityName", "publicationId||$publicationId", "count||Y", "order||N");
                $activityCount = $activityTypeObject->getTotalCount();
                if ($activityCount > 0) {

                    $activityTypeObject = new Gamification_ActivityTypes("activityName||$activityName", "publicationId||$publicationId", "quantity||1");
                    $activityTypeId = $activityTypeObject->getActivityTypeId();
                    $activityCommonObject = $activityTypeObject->getActivityCommonObject();
                    $activityCommonVerb = $activityTypeObject->getActivityCommonVerb();
                    $activityPointsType = $activityTypeObject->getActivityPointsType();
                    $activityPoints = $activityTypeObject->getActivityPoints();
                    $activityText = $activityTypeObject->getActivityText();
                    if ($activityCommonObject == 'YES' && $activityCommonVerb == 'YES') {
                        $comman = 'YES';
                    } else {
                        $comman = 'NO';
                    }
                    if (self::isUserEligibile($activityTypeId, $userId, $publicationId, $clientId)) {
                        $activityText = self::getActivityText($activityText, $activityObject, $userId);
                        // updating user activity rel table
                        $userActivityId = self::updateUserActivityRel($userId, $activityOwnership, $activityObject, $activityVerb, $activityCommonObject, $activityCommonVerb, $clientId, $contentId, $contentType, $activityPoints, $activityPointsType, $publicationId, $activityText);
                        if ($userActivityId) {
                            self::createFriendActivities($userId, $publicationId, $userActivityId, $activityObject, $activityVerb, $comman);
                            if (self::isBadgeExist($activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId)) {
                                $returnArray = self::getBadgeLevel($userId, $activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId);
                                if (isset($returnArray["error"])) {
                                    $record["error"] = TRUE;
                                    $record['error_msg'] = $returnArray["error_msg"];
                                } else {
                                    $userbadgeLevelType = $returnArray['badgeLevelType'];
                                    $returnArray = self::getUserTotalPointsByActivity($userId, $activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId); //get TotalPoint According to activtyTypeId
                                    if (isset($returnArray["error"])) {
                                        $record["error"] = TRUE;
                                        $record['error_msg'] = $returnArray["error_msg"];
                                    } else {
                                        $totalActivityPoints = $returnArray["totalActivityPoints"];

                                        $returnArray = self::getUserNewPoints($userbadgeLevelType, $totalActivityPoints, $activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId); //point required to get new badge
                                        if (isset($returnArray["error"])) {
                                            $record["error"] = TRUE;
                                            $record['error_msg'] = $returnArray["error_msg"];
                                        } else {
                                            $userNewPoints = $returnArray['userNewPoints'];
                                            $userNewbadgeLevelType = $userbadgeLevelType + 1;
                                            $returnArray = self::getHighestLevelOfActivity($activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId);
                                            if (isset($returnArray["error"])) {
                                                $record["error"] = TRUE;
                                                $record['error_msg'] = $returnArray["error_msg"];
                                            } else {
                                                if ($userNewbadgeLevelType <= $returnArray["highestlevelId"]) {
                                                    $returnArray = self::pointRequiredForBadge($activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId, $userNewbadgeLevelType); //required Badge point
                                                    if (isset($returnArray["error"])) {
                                                        $record["error"] = TRUE;
                                                        $record['error_msg'] = $returnArray["error_msg"];
                                                    } else {
                                                        $requiredPoints = $returnArray['requiredPoints'];
                                                        $badgeId = $returnArray['badgeId'];
                                                        if ($requiredPoints <= $userNewPoints) {
                                                            $returnArray = self::assignBadgeToUser($userId, $userNewbadgeLevelType, $badgeId, $activityOwnership, $activityObject, $activityVerb, $activityCommonObject, $activityCommonVerb, $publicationId, $clientId);
                                                            if (isset($returnArray["error"])) {
                                                                $record["error"] = TRUE;
                                                                $record['error_msg'] = $returnArray["error_msg"];
                                                            } else {

                                                                $record['badgeSuccess'] = $notificationText = "you achieved new badge";
                                                                // getting the name of activity

                                                                $activityTypeObject1 = new Gamification_ActivityTypes("activityName||MINE_BADGE_UNLOCKED", "publicationId||$publicationId");
                                                                $count = $activityTypeObject1->getTotalCount();
                                                                if ($count > 0) {

                                                                    $activityTypeObject1 = new Gamification_ActivityTypes("activityName||MINE_BADGE_UNLOCKED", "publicationId||$publicationId", "quantity||1");
                                                                    $commonObject = $activityTypeObject1->getActivityCommonObject();
                                                                    $commonVerb = $activityTypeObject1->getActivityCommonVerb();
                                                                    $pointsType = $activityTypeObject1->getActivityPointsType();
                                                                    $points = $activityTypeObject1->getActivityPoints();
                                                                    $text = $activityTypeObject1->getActivityText();
                                                                }
                                                                self::updateUserActivityRel($userId, 'MINE', 'BADGE', 'UNLOCKED', $commonObject, $commonVerb, $clientId, $badgeId, "none", $points, $pointsType, $publicationId, $text);
                                                                self::setNotification($badgeId, 'badge', 'unread', $notificationText, $userId, $publicationId, $clientId);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $record["error"] = TRUE;
                                $record['error_msg'] = "badge not exist for activity " . $activityName;
//                        echo "badge not exist for activity " . $activityName;
//                        echo"<br/>";
                            }
                            // assigning the moderator and levels
                            $returnArray = self::getUserTotalPoints($userId, $publicationId, $clientId);
                            if (isset($returnArray["error"])) {
                                $record["error"] = TRUE;
                                $record['error_msg'] = $returnArray["error_msg"];
                            } else {
                                $totalPositivePoints = $returnArray["totalPositivePoints"];

                                // assigning levels
                                $userLevelRelObj = new Gamification_UserLevelRels();
                                $levelDataArray = $userLevelRelObj->getAllGamificationUserLevelRelData($userId, $publicationId, $clientId);
                                if ($levelDataArray) {
                                    $userLevelNo = $levelDataArray[0]->getUserLevelNo();
                                } else {
                                    $userLevelNo = 0;
                                }
                                $userLevelNo = $userLevelNo + 1;

                                $userLevelObj = new Gamification_UserLevelTypes("userLevelNo||$userLevelNo", "clientId||$clientId");
                                $total = $userLevelObj->getTotalCount();
                                if ($total > 0) {
                                    $userLevelObj = new Gamification_UserLevelTypes("userLevelNo||$userLevelNo", "clientId||$clientId", "quantity||1");
                                    $unlokingPoints = $userLevelObj->getUnlockingPoints();
                                    $userLevelTypeId = $userLevelObj->getUserLevelTypeId();

                                    if ($unlokingPoints <= $totalPositivePoints) {
                                        $returnArray = self::assignLevelToUser($userId, $publicationId, $userLevelNo, $clientId);
                                        if (isset($returnArray["error"])) {
                                            $record["error"] = TRUE;
                                            $record['error_msg'] = $returnArray["error_msg"];
                                        } else {
                                            $record['LevelSuccess'] = $notificationText = "you achieved new Level";
                                            self::setNotification($userLevelTypeId, 'level', 'unread', $notificationText, $userId, $publicationId, $clientId);
                                        }
                                    } else {
                                        $requiredPoints = $unlokingPoints - $totalPositivePoints;
                                    }
                                }

                                // assigning moderator
                                $userModerationRelObj = new Gamification_UserModerationRels();
                                $userModerationArray = $userModerationRelObj->getAllGamificationUserModerationRelData($userId, $publicationId, $clientId);
                                if ($userModerationArray) {
                                    $moderatorNo = $userModerationArray[0]->getModeratorNo();
                                } else {
                                    $moderatorNo = 0;
                                }
                                $moderatorNo = $moderatorNo + 1;

                                $moderatorTypeObj = new Gamification_ModeratorTypes("moderatorNo||$moderatorNo", "clientId||$clientId");
                                $total = $moderatorTypeObj->getTotalCount();
                                if ($total > 0) {
                                    $moderatorTypeObj = new Gamification_ModeratorTypes("moderatorNo||$moderatorNo", "clientId||$clientId", "quantity||1");
                                    $unlokingPoints = $moderatorTypeObj->getUnlockingPoints();
                                    $moderationTypeId = $moderatorTypeObj->getModeratorTypeId();

                                    if ($unlokingPoints <= $totalPositivePoints) {
                                        $returnArray = self::assignModeratorToUser($userId, $publicationId, $moderatorNo, $clientId);
                                        if (isset($returnArray["error"])) {
                                            $record["error"] = TRUE;
                                            $record['error_msg'] = $returnArray["error_msg"];
                                        } else {
                                            $record['ModerationSuccess'] = $notificationText = "you achieved new Moderation Level";
                                            self::setNotification($moderationTypeId, 'moderator', 'unread', $notificationText, $userId, $publicationId, $clientId);
                                        }
                                    } else {
                                        $requiredPoints = $unlokingPoints - $totalPositivePoints;
                                    }
                                }
                            }
                            // ulocking collectables
                            $collectableObj = new Gamification_Collectibles("activityObject||$activityObject", "contentType||$contentType", "publicationId||$publicationId", "clientId||$clientId", "count||Y", "order||N");
                            if ($collectableObj->getTotalCount() > 0) {
                                $collectableObj = new Gamification_Collectibles("activityObject||$activityObject", "contentType||$contentType", "publicationId||$publicationId", "clientId||$clientId", "quantity||1");
                                $requiredActivityCount = $collectableObj->getActivityCount();
                                $collectibleId = $collectableObj->getCollectibleId();
                                // check for object , contentType
                                $userActivityObj = new Gamification_UserActivityRels("activityObject||$activityObject", "contentType||$contentType", "publicationId||$publicationId", "clientId||$clientId", "count||Y", "order||N");
                                $userActivityCount = $userActivityObj->getTotalCount();
                                if ($userActivityCount >= $requiredActivityCount) {
                                    // assign Collectible To User
                                    $returnArray = self::assignCollectibleToUser($collectibleId, $publicationId, $activityObject, $contentType, $userId, $clientId);
                                    if (isset($returnArray["error"])) {
                                        $record["error"] = TRUE;
                                        $record['error_msg'] = $returnArray["error_msg"];
                                    } else {
                                        $record['CollectibleSuccess'] = $notificationText = "you achieved new Collectible";
                                        self::setNotification($collectibleId, 'collectible', 'unread', $notificationText, $userId, $publicationId, $clientId);
                                    }
                                }
                            }
                        } else {
                            $record["error"] = TRUE;
                            $record["error_msg"] = "Activity already done by this user";
                        }
                    } else {
                        $record["error"] = TRUE;
                        $record["error_msg"] = "user is not eligible to perform Activity " . $activityName . "";
                    }
                } else {
                    $record["error"] = TRUE;
                    $record["error_msg"] = "Activity " . $activityName . " is invalid";
                }
            } catch (Exception $e) {
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        if (isset($record["error"])) {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "publicationId=" . $publicationId . "\nrecord=" . $record);
        }
        return $record;
    }

    /**
     * @author Ashish Kumar 20111216$.
     * @desc Method use to check the eligibility of user to perform some activity
     * @param Integer $activityTypeId
     * @param Integer $userId
     * @param Integer $publicationId
     * @param Integer $clientId
     * @return true is user is eligible otherwise false
     */
    public static function isUserEligibile($activityTypeId, $userId, $publicationId, $clientId = 1) {
        $userId = trim($userId);
        $activityTypeId = trim($activityTypeId);
        $clientId = trim($clientId);
        $publicationId = trim($publicationId);
        try {
            $activityObj = new Gamification_ActivityType($activityTypeId);
            $userEligibilityPoints = $activityObj->getUserEligibilityPoints();
            $returnArray = self::getUserTotalPoints($userId, $publicationId, $clientId);
            $totalPositivePoints = $returnArray["totalPositivePoints"];
            if ($totalPositivePoints >= $userEligibilityPoints) {
                return true;
            }
        } catch (Exception $e) {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * @author Ashish Kumar 20111216$.
     * @desc Method use to get the activity text with username and object
     * @param String $activityText
     * @param String $activityObject
     * @param Integer $userId
     */
    public static function getActivityText($activityText, $activityObject, $userId) {
        $activityText = trim($activityText);
        $activityObject = trim($activityObject);
        $userId = trim($userId);
        if (empty($activityText) || empty($activityObject) || empty($userId)) {
            return $activityText;
        } else {
            try {

                $userObj = new Instacheckin_User($userId);
                $firstName = $userObj->getUserFirstName();
                $lastName = $userObj->getUserLastName();
                $name = $firstName . " " . $lastName;

                $activityObject = strtolower($activityObject);
                $activityText = str_replace('$user', $name, $activityText);
                $activityText = str_replace('$object', $activityObject, $activityText);
            } catch (Exception $e) {
                //   Instapress_Core_Helper::describe($e->getMessage());
                //    Instapress_Core_Helper::describe($e->getTraceAsString());
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $activityText;
    }

    /**
     * @author Ashish Kumar 20111213$.
     * @desc Method use to set the notification for the users 
     * @param Integer $notificationTypeId
     * @param String $notificationType
     * @param String $notificationStatus
     * @param String $notificationText
     * @param Integer $userId
     * @param Integer $publicationId
     * @param Integer $clientId
     */
    public static function setNotification($notificationTypeId, $notificationType, $notificationStatus, $notificationText, $userId, $publicationId, $clientId = 1) {
        $record = array();
        $notificationTypeId = trim($notificationTypeId);
        $notificationType = trim($notificationType);
        $notificationStatus = trim($notificationStatus);
        $notificationText = trim($notificationText);
        $userId = trim($userId);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($notificationTypeId) || empty($notificationType) || empty($notificationStatus) || empty($notificationText) || empty($userId) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "notificationTypeId or notificationType or notificationStatus or notificationText or userId or publicationId or clientId is not available";
        } else {
            try {
                $notificationObj = new Gamification_Db_Notification('add');
                $notificationObj->set("notificationTypeId||$notificationTypeId", "notificationType||$notificationType", "notificationStatus||$notificationStatus", "notificationText||$notificationText", "userId||$userId", "publicationId||$publicationId", "clientId||$clientId");
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = "Exception:" . $e->getMessage();
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar 20111213$.
     * @desc Method use to assign Collectible To User
     * @param Integer $collectibleId
     * @param Integer $publicationId
     * @param String $activityObject
     * @param String $contentType
     * @param Integer $userId
     * @param Integer $clientId
     */
    public static function assignCollectibleToUser($collectibleId, $publicationId, $activityObject, $contentType, $userId, $clientId = 1) {
        $record = array();
        $collectibleId = trim($collectibleId);
        $publicationId = trim($publicationId);
        $activityObject = trim($activityObject);
        $contentType = trim($contentType);
        $userId = trim($userId);
        $clientId = trim($clientId);
        if (empty($userId) || empty($contentType) || empty($activityObject) || empty($collectibleId) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or contentType or activityObject or  publicationId or collectibleId or clientId is not available";
        } else {
            try {
                $userCollectibleObj = new Gamification_UserCollectibleRels("activityObject||$activityObject", "contentType||$contentType", "publicationId||$publicationId", "userId||$userId", "clientId||$clientId", "collectibleId||$collectibleId", "count||Y", "order||N");
                $total = $userCollectibleObj->getTotalCount();
                if ($total == 0) {
                    $userCollectibleObj = new Gamification_Db_UserCollectibleRel('add');
                    $userCollectibleObj->set("activityObject||$activityObject", "contentType||$contentType", "publicationId||$publicationId", "userId||$userId", "clientId||$clientId", "collectibleId||$collectibleId");
                } else {
                    $record["error"] = TRUE;
                    $record["error_msg"] = "";
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = "Exception:" . $e->getMessage();
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar 20111213$.
     * @desc Method use to upadate the table user activity rel when user perform some activity
     * @param Integer $userId
     * @param String $activityOwnership
     * @param String $activityObject
     * @param String $activityVerb
     * @param String $activityCommonObject
     * @param String $activityCommonVerb
     * @param Integer $clientId
     * @param Integer $contentId
     * @param String $contentType
     * @param Integer $activityPoints
     * @param String $activityPointsType
     * @param Integer $publicationId
     */
    public static function updateUserActivityRel($userId, $activityOwnership, $activityObject, $activityVerb, $activityCommonObject, $activityCommonVerb, $clientId = 1, $contentId, $contentType, $activityPoints, $activityPointsType, $publicationId, $activityText) {
        $userId = trim($userId);
        $activityOwnership = trim($activityOwnership);
        $activityObject = trim($activityObject);
        $activityVerb = trim($activityVerb);
        $activityCommonObject = trim($activityCommonObject);
        $activityCommonVerb = trim($activityCommonVerb);
        $clientId = trim($clientId);
        $contentId = trim($contentId);
        $contentType = trim($contentType);
        $activityPoints = trim($activityPoints);
        $activityPointsType = trim($activityPointsType);
        $publicationId = trim($publicationId);
        $activityText = trim($activityText);

        $userActibityObj = new Gamification_UserActivityRels("userId||$userId", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "activityCommonObject||$activityCommonObject", "activityCommonVerb||$activityCommonVerb", "clientId||$clientId", "contentId||$contentId", "contentType||$contentType", "activityPoints||$activityPoints", "activityPointsType||$activityPointsType", "publicationId||$publicationId", "count||Y", "order||N");
        $total = $userActibityObj->getTotalCount();
        if ($total == 0) {
            try {
                $userActivityObj = new Gamification_Db_UserActivityRel('add');
                $userActivityObj->set("userId||$userId", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "activityCommonObject||$activityCommonObject", "activityCommonVerb||$activityCommonVerb", "clientId||$clientId", "contentId||$contentId", "contentType||$contentType", "activityPoints||$activityPoints", "activityPointsType||$activityPointsType", "publicationId||$publicationId", "activityText||$activityText");
                $userActivityId = $userActivityObj->getLastInsertedId();
                // now updating table UserPublicationStatus
                self::updateUserPublicationStatus($userId, $publicationId, $activityPoints, $activityPointsType, $clientId);
                return $userActivityId;
            } catch (Exception $e) {
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @author Ashish Kumar
     * @param int $userId <p>userId of user</p>
     * @param int $publicationId <p></p>
     * @param int $points <p></p>
     * @param int $pointType <p>'+ve' or '-ve'</p>
     * @desc insert record in the table user_publication_status
     */
    public static function updateUserPublicationStatus($userId, $publicationId, $points, $pointType, $clientId = 1) {
        try {
            $statusObj = new Gamification_Db_UserPublicationStatus();
            $statusObj->set("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y", "order||N");
            $total = $statusObj->getTotalCount();
            if ($total > 0) {

                $statusObj = new Gamification_Db_UserPublicationStatus();
                $statusObj->set("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "quantity||1");
                $positivePoints = $statusObj->get("totalPositivePoints");
                $negativePoints = $statusObj->get("totalNegativePoints");
                $userPublicationStatusId = $statusObj->get("userPublicationStatusId");
                // edit the table
                $statusObj = new Gamification_Db_UserPublicationStatus("edit");
                if ($pointType == "+ve") {
                    $positivePoints = $positivePoints + $points;
                    $statusObj->set("userPublicationStatusId||$userPublicationStatusId", "userId||$userId", "publicationId||$publicationId", "totalPositivePoints||$positivePoints", "lastActivityTime||NOW()", "clientId||$clientId");
                } else {
                    $negativePoints = $negativePoints + $points;
                    $statusObj->set("userPublicationStatusId||$userPublicationStatusId", "userId||$userId", "publicationId||$publicationId", "totalNegativePoints||$negativePoints", "lastActivityTime||NOW()", "clientId||$clientId");
                }
            } else {
                // add in the table
                $statusObj = new Gamification_Db_UserPublicationStatus("add");
                if ($pointType == "+ve") {
                    $statusObj->set("userId||$userId", "publicationId||$publicationId", "status||active", "totalPositivePoints||$points", "totalNegativePoints||0", "clientId||$clientId");
                } else {
                    $statusObj->set("userId||$userId", "publicationId||$publicationId", "status||active", "totalPositivePoints||0", "totalNegativePoints||$points", "clientId||$clientId");
                }
            }
        } catch (Exception $e) {
            // echo $e->getMessage();
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
        }
    }

    /**
     * @author Ashish Kumar
     * @desc Method use to get the current badge level of user for selected activity
     * @param int $userId <p>userId of user</p>
     * @param String $activityOwnership
     * @param String $activityObject
     * @param String $activityVerb
     * @param Integer $publicationId
     * @param Integer $clientId
     */
    public static function getBadgeLevel($userId, $activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $activityOwnership = trim($activityOwnership);
        $activityObject = trim($activityObject);
        $activityVerb = trim($activityVerb);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($userId) || empty($activityOwnership) || empty($activityObject) || empty($activityVerb) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or activityOwnership or activityObject or activityVerb or publicationId or clientId is not available";
        } else {
            try {
                $obj = new Gamification_Db_UserBadgesRel();
                $obj->set("userId||$userId", "publicationId||$publicationId", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "higestLevel||YES", "clientId||$clientId", "count||Y", "order||N");
                $totalMatchRecord = $obj->getTotalCount();
                if ($totalMatchRecord > 0) {
                    $obj = new Gamification_Db_UserBadgesRel();
                    $obj->set("userId||$userId", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "publicationId||$publicationId", "higestLevel||YES", "clientId||$clientId", "quantity||1");
                    $record['badgeLevelType'] = $obj->get("badgeLevelType");
                } else {
                    $record['badgeLevelType'] = 0;
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = "Exception:" . $e->getMessage();
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @desc Method use to get the total points of user for selected activity
     * @param int $userId <p>userId of user</p>
     * @param String $activityOwnership
     * @param String $activityObject
     * @param String $activityVerb
     * @param Integer $publicationId
     * @param Integer $clientId
     */
    public static function getUserTotalPointsByActivity($userId, $activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $activityOwnership = trim($activityOwnership);
        $activityObject = trim($activityObject);
        $activityVerb = trim($activityVerb);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($userId) || empty($activityOwnership) || empty($activityObject) || empty($activityVerb) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or activityOwnership or activityObject or activityVerb or publicationId or clientId  is not available";
        } else {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            try {
                $activityObj = new Gamification_Db_UserActivityRel();
                $activityObj->set("userId||$userId", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "publicationId||$publicationId", "clientId||$clientId", "count||Y", "order||N");
                $totalRecord = $activityObj->getTotalCount();
                $totalPoint = 0;
                if ($totalRecord > 0) {
                    $activityObj = new Gamification_Db_UserActivityRel();
                    $activityObj->set("userId||$userId", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "publicationId||$publicationId", "clientId||$clientId", "quantity||$totalRecord");

                    for ($i = 0; $i < $totalRecord; $i++) {
                        try {
                            $pointType = $activityObj->get("activityPointsType", $i);
                            $point = $activityObj->get("activityPoints", $i);
                            $totalPoint = $totalPoint + $point;
                        } catch (Exception $e) {
                            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                        }
                    }
                }
                $record["totalActivityPoints"] = $totalPoint;
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = "Exception:" . $e->getMessage();
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @desc Method use to calculate the user's new points(points achieved after getting last badge)
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     * <p>userNewPoints</p>
     */
    public static function getUserNewPoints($userCurrentLevel, $userTotalPoints, $activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId = 1) {
        $record = array();
        $userCurrentLevel = trim($userCurrentLevel);
        $userTotalPoints = trim($userTotalPoints);
        $clientId = trim($clientId);

        $activityOwnership = trim($activityOwnership);
        $activityObject = trim($activityObject);
        $activityVerb = trim($activityVerb);
        $publicationId = trim($publicationId);
        if (empty($activityOwnership) || empty($activityObject) || empty($activityVerb) || empty($publicationId) || empty($userTotalPoints) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "activityOwnership or activityObject or activityVerb or publicationId or userTotalPoints or clientId is not available";
        } else {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            try {
                $oldPoints = 0;
                for ($i = 1; $i <= $userCurrentLevel; $i++) {
                    try {
                        $return = self::pointRequiredForBadge($activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId, $i);
                        $oldPoints = $oldPoints + $return['requiredPoints'];
                    } catch (Exception $e) {
                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
                $newPoints = $userTotalPoints - $oldPoints;
                $record['userNewPoints'] = $newPoints;
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = "Exception:" . $e->getMessage();

                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @desc Method use to calculate the points required to get the current badgeLevel
     * @param String $activityOwnership
     * @param String $activityObject
     * @param String $activityVerb
     * @param Integer $publicationId
     * @param Integer $clientId
     * @param Integer $badgeLevelType
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     * <p>requiredPoints</p>
     * <p>badgeId</p>
     */
    public static function pointRequiredForBadge($activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId = 1, $badgeLevelType) {
        $record = array();
        $badgeLevelType = trim($badgeLevelType);
        $activityOwnership = trim($activityOwnership);
        $activityObject = trim($activityObject);
        $activityVerb = trim($activityVerb);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($badgeLevelType) || empty($activityOwnership) || empty($activityObject) || empty($activityVerb) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "badgeLevelType or activityOwnership or activityObject or activityVerb or publicationId or clientId is not available";
        } else {
            try {
                $levelObj = new Gamification_Db_Badge();
                $levelObj->set("badgeLevelType||$badgeLevelType", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "publicationId||$publicationId", "clientId||$clientId", "count||Y", "order||N");
                $totalRecords = $levelObj->getTotalCount();
                if ($totalRecords > 0) {
                    $levelObj = new Gamification_Db_Badge();
                    $levelObj->set("badgeLevelType||$badgeLevelType", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "publicationId||$publicationId", "clientId||$clientId", "quantity||$totalRecords");

                    $activityCount = $levelObj->get("activityCount");

                    $activityName = $activityOwnership . "_" . $activityObject . "_" . $activityVerb;
                    $activityTypeObject = new Gamification_ActivityTypes("activityName||$activityName", "publicationId||$publicationId", "clientId||$clientId", "quantity||1");
                    $activityTypeId = $activityTypeObject->getActivityTypeId();

                    $record['requiredPoints'] = self::getActivityPoint($activityTypeId, $activityCount);
                    $record['badgeId'] = $levelObj->get("badgeId");
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = "Exception:" . $e->getMessage();
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }

        return $record;
    }

    /**
     * @author Ashish Kumar
     * @param int $userActivityTypeId <p></p>
     * @param int $activityCount <p>how many times activity is performed</p>
     * @desc calculate points achieved by performing current activity by given no of time
     * @return int $requiredPoint
     */
    public static function getActivityPoint($activityTypeId, $activityCount) {
        try {
            $activityObj = new Gamification_ActivityType($activityTypeId);
            $point = $activityObj->getActivityPoints();
            $pointType = $activityObj->getActivityPointsType();
            $requiredPoint = $point * $activityCount;
            return $requiredPoint;
        } catch (Exception $e) {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
        }
    }

    /**
     * @author Ashish Kumar
     * @desc Method use to get the highest badge level for cuttent activity
     * @param String $activityOwnership
     * @param String $activityObject
     * @param String $activityVerb
     * @param Integer $publicationId
     * @param Integer $clientId
     */
    public static function getHighestLevelOfActivity($activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId = 1) {
        $record = array();
        $activityOwnership = trim($activityOwnership);
        $activityObject = trim($activityObject);
        $activityVerb = trim($activityVerb);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);

        if (empty($activityOwnership) || empty($activityObject) || empty($activityVerb) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "activityOwnership or activityObject or activityVerb or publicationId or clientId is not available";
        } else {
            try {
                $obj = new Gamification_Db_Badge();
                $obj->set("activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "publicationId||$publicationId", "clientId||$clientId", "count||Y", "order||N");
                $total = $obj->getTotalCount();
                if ($total > 0) {
//                    $obj = new Gamification_Db_Badge();
//                    $obj->set("activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "publicationId||$publicationId", "clientId||$clientId", "quantity||$total");
//                    $highestLevel = 0;
//                    for ($i = 0; $i < $total; $i++) {
//                        $newhighestLevel = $obj->get("badgeLevelType", $i);
//                        if ($newhighestLevel > $highestLevel) {
//                            $highestLevel = $newhighestLevel;
//                        }
//                    }
                    $record['highestlevelId'] = $total;
                } else {
                    $record["error"] = TRUE;
                    $record["error_msg"] = "no level for current activity";
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = $e->getMessage();
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @desc it assign the new badge to user
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     */
    public static function assignBadgeToUser($userId, $badgeLevelType, $badgeId, $activityOwnership, $activityObject, $activityVerb, $activityCommonObject, $activityCommonVerb, $publicationId, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $badgeId = trim($badgeId);
        $badgeLevelType = trim($badgeLevelType);

        $activityOwnership = trim($activityOwnership);
        $activityObject = trim($activityObject);
        $activityVerb = trim($activityVerb);
        $publicationId = trim($publicationId);
        $activityCommonObject = trim($activityCommonObject);
        $activityCommonVerb = trim($activityCommonVerb);
        $clientId = trim($clientId);
        if (empty($userId) || empty($badgeId) || empty($badgeLevelType) || empty($activityOwnership) || empty($activityObject) || empty($activityVerb) || empty($activityCommonObject) || empty($activityCommonVerb) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or badgeId or badgeLevelType or activityOwnership or activityObject or activityVerb or activityCommonObject or activityCommonVerb or publicationId or clientId is not available";
        } else {
            try {
                // edit the table entries
                $badgesRelObj = new Gamification_UserBadgesRels("userId||$userId", "publicationId||$publicationId", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "activityCommonObject||$activityCommonObject", "activityCommonVerb||$activityCommonVerb", "clientId||$clientId", "higestLevel||YES", "count||Y");
                if ($badgesRelObj->getTotalCount() > 0) {
                    $badgesRelObject = new Gamification_UserBadgesRels("userId||$userId", "publicationId||$publicationId", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "activityCommonObject||$activityCommonObject", "activityCommonVerb||$activityCommonVerb", "clientId||$clientId", "higestLevel||YES", "quantity||1");
                    $userBadgeRelId = $badgesRelObject->getUserBadgeRelId();
                    $badgesRelObj = new Gamification_Db_UserBadgesRel('edit');
                    $badgesRelObj->set("userBadgeRelId||$userBadgeRelId", "userId||$userId", "publicationId||$publicationId", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "activityCommonObject||$activityCommonObject", "activityCommonVerb||$activityCommonVerb", "clientId||$clientId", "higestLevel||NO");
                }

                // add the new row
                $badgesRelObj = new Gamification_Db_UserBadgesRel('add');
                $badgesRelObj->set("userId||$userId", "publicationId||$publicationId", "badgeId||$badgeId", "badgeLevelType||$badgeLevelType", "activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "activityCommonObject||$activityCommonObject", "activityCommonVerb||$activityCommonVerb", "clientId||$clientId", "higestLevel||YES");
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = "Exception:" . $e->getMessage(); ////
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @param int $userId <p></p>
     * @param int $publicationId <p></p>
     * @param int $clientId <p></p>
     * @desc get the total positive and total negative points of user at this publication
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     * <p>totalPositivePoints</p>
     * <p>totalNegativePoints</p>
     */
    public static function getUserTotalPoints($userId, $publicationId, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($userId) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or publicationId or clientId is not available";
        } else {
            try {
                $statusObj = new Gamification_UserPublicationStatuses("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y", "order||N");
                $totalRecord = $statusObj->getTotalCount();
                $totalPositivePoint = 0;
                $totalNegativePoint = 0;
                if ($totalRecord > 0) {
                    $statusObj = new Gamification_UserPublicationStatuses("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "quantity||1");
                    $totalPositivePoint = $statusObj->getTotalPositivePoints();
                    $totalNegativePoints = $statusObj->getTotalNegativePoints();
                }
                $record["totalPositivePoints"] = $totalPositivePoint;
                $record["totalNegativePoints"] = $totalNegativePoint;
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = "Exception:" . $e->getMessage(); /////////
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @param int $userId <p></p>
     * @param int $publicationId <p></p>
     * @param int $moderationTypeId <p>new moderator id</p>
     * @param int $clientId <p></p>
     * @desc it assign the new moderator level to user by updating the table GamificationUserModerationRel
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     */
    public static function assignModeratorToUser($userId, $publicationId, $moderatorNo, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $publicationId = trim($publicationId);
        $moderatorNo = trim($moderatorNo);
        $clientId = trim($clientId);
        if (empty($userId) || empty($publicationId) || empty($moderatorNo) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or publicationId or moderatorNo or clientId is not available";
        } else {
            try {
                $obj = new Gamification_UserModerationRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $totalRecord = $obj->getTotalCount();
                if ($totalRecord > 0) {
                    $obj = new Gamification_UserModerationRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "quantity||1");
                    $userModerationRelId = $obj->getUserModerationRelId();
                    // edit the table
                    $obj = new Gamification_Db_UserModerationRel('edit');
                    $obj->set("userModerationRelId||$userModerationRelId", "userId||$userId", "publicationId||$publicationId", "moderatorNo||$moderatorNo", "clientId||$clientId");
                } else {
                    // insert new record in the table
                    $obj = new Gamification_Db_UserModerationRel('add');
                    $obj->set("userId||$userId", "publicationId||$publicationId", "moderatorNo||$moderatorNo", "clientId||$clientId");
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = $e->getMessage();
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @param int $userId <p></p>
     * @param int $publicationId <p></p>
     * @param int $userLevelTypeId <p>new userLevel id</p>
     * @param int $clientId <p></p>
     * @desc it assign the new moderator level to user by updating the table GamificationUserModerationRel
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     */
    public static function assignLevelToUser($userId, $publicationId, $userLevelNo, $clientId) {
        $record = array();
        $userId = trim($userId);
        $publicationId = trim($publicationId);
        $userLevelNo = trim($userLevelNo);
        $clientId = trim($clientId);
        if (empty($userId) || empty($publicationId) || empty($userLevelNo) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or publicationId or userLevelNo or clientId is not available";
        } else {
            try {
                $userlevelObj = new Gamification_UserLevelRels();
                $userLevelArray = $userlevelObj->getAllGamificationUserLevelRelData($userId, $publicationId, $clientId);
                if ($userLevelArray) {
                    $userLevelRelId = $userLevelArray[0]->getUserLevelRelId();
                    // edit the table
                    $obj = new Gamification_Db_UserLevelRel('edit');
                    $obj->set("userLevelRelId||$userLevelRelId", "userId||$userId", "publicationId||$publicationId", "userLevelNo||$userLevelNo", "clientId||$clientId");
                } else {
                    // insert new record in the table
                    $obj = new Gamification_Db_UserLevelRel('add');
                    $obj->set("userId||$userId", "publicationId||$publicationId", "userLevelNo||$userLevelNo", "clientId||$clientId");
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = $e->getMessage();
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @desc Method use to check that current activity contain any badge or not
     * @param String $activityOwnership
     * @param String $activityObject
     * @param String $activityVerb
     * @param Integer $publicationId
     * @param Integer $clientId
     */
    public static function isBadgeExist($activityOwnership, $activityObject, $activityVerb, $publicationId, $clientId = 1) {
        $activityOwnership = trim($activityOwnership);
        $activityObject = trim($activityObject);
        $activityVerb = trim($activityVerb);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        try {
            $badgeObj = new Gamification_Badges("activityOwnership||$activityOwnership", "activityObject||$activityObject", "activityVerb||$activityVerb", "publicationId||$publicationId", "clientId||$clientId", "count||Y", "order||N");
            if ($badgeObj->getTotalCount() > 0)
                return true;
        } catch (Exception $e) {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * @author Ashish Kumar
     * @param int $userId <p></p>
     * @param int $publicationId <p></p>
     * @desc get the total badges of current user at this publication
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     */
    public static function getUserBadges($userId, $publicationId, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($userId) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or publicationId or clientId is not available";
        } else {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            try {
                $userBadgeObj = new Gamification_UserBadgesRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $totalRecord = $userBadgeObj->getTotalCount();
                $userBadgeObj = new Gamification_UserBadgesRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "quantity||$totalRecord");
                for ($i = 0; $i < $totalRecord; $i++) {
                    try {
                        $badgeId = $userBadgeObj->getBadgeId($i);
                        $badgeObj = new Gamification_Badge($badgeId);
                        $record[] = $badgeObj;
                    } catch (Exception $e) {
                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = $e->getMessage();

                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @param int $userId <p></p>
     * @param int $publicationId <p></p>
     * @desc get the total Collectibless of current user at this publication
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     */
    public static function getUserCollectibles($userId, $publicationId, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($userId) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or publicationId or clientId is not available";
        } else {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            try {
                $userCollectibleObj = new Gamification_UserCollectibleRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $totalRecord = $userCollectibleObj->getTotalCount();
                $userCollectibleObj = new Gamification_UserCollectibleRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "quantity||$totalRecord");
                for ($i = 0; $i < $totalRecord; $i++) {
                    try {
                        $collectibleId = $userCollectibleObj->getCollectibleId($i);
                        $collectibleObj = new Gamification_Collectible($collectibleId);
                        $record[] = $collectibleObj;
                    } catch (Exception $e) {
                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = $e->getMessage();
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @param int $userId <p></p>
     * @param int $publicationId <p></p>
     * @desc get the current level Info of current user at this publication
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     */
    public static function getUserLevel($userId, $publicationId, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($userId) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or publicationId or clientId is not available";
        } else {
            try {
                $userLevelObj = new Gamification_UserLevelRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId");
                $totalRecord = $userLevelObj->getTotalCount();
                if ($totalRecord > 0) {
                    $record['levelNo'] = $userLevelNo = $userLevelObj->getUserLevelNo();
                    $levelObj = new Gamification_UserLevelTypes("userLevelNo||$userLevelNo", "publicationId||$publicationId", "clientId||$clientId");
                    if ($levelObj->getTotalCount() > 0) {
                        $record['levelName'] = $userLevelName = $levelObj->getUserLevelName();
                        $record['levelImage'] = $imageUrl = $levelObj->getImageUrl();
                    }
                    $levelObj = new Gamification_UserLevelTypes("publicationId||$publicationId", "clientId||$clientId");
                    $record['totalLevel'] = $levelObj->getTotalCount();
                } else {
                    $record['levelNo'] = 1;
                    $levelObj = new Gamification_UserLevelTypes("userLevelNo||1", "publicationId||$publicationId", "clientId||$clientId");
                    if ($levelObj->getTotalCount() > 0) {
                        $record['levelName'] = $userLevelName = $levelObj->getUserLevelName();
                        $record['levelImage'] = $imageUrl = $levelObj->getImageUrl();
                    }
                    $levelObj = new Gamification_UserLevelTypes("publicationId||$publicationId", "clientId||$clientId");
                    $record['totalLevel'] = $levelObj->getTotalCount();
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = $e->getMessage();
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @param int $userId <p></p>
     * @param int $publicationId <p></p>
     * @desc get the total Moderation of current user at this publication
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     */
    public static function getUserModeration($userId, $publicationId, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($userId) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or publicationId or clientId is not available";
        } else {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            try {
                $userModerationObj = new Gamification_UserModerationRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $totalRecord = $userModerationObj->getTotalCount();
                $userModerationObj = new Gamification_UserModerationRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "quantity||$totalRecord");
                for ($i = 0; $i < $totalRecord; $i++) {
                    try {
                        $moderatorNo = $userModerationObj->getModeratorNo($i);
                        $moderationObj = new Gamification_ModeratorTypes("moderatorNo||$moderatorNo", "quantity||1");
                        $record[] = $moderationObj;
                    } catch (Exception $e) {
                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = $e->getMessage();

                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    public static function getUserRewards($userId, $publicationId, $clientId = 1) {
        $record = array();
        $userId = trim($userId);
        $publicationId = trim($publicationId);
        $clientId = trim($clientId);
        if (empty($userId) || empty($publicationId) || empty($clientId)) {
            $record["error"] = TRUE;
            $record["error_msg"] = "userId or publicationId or clientId is not available";
        } else {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            try {
                // getting the moderator
                $record['moderator'] = array();
                $userModerationObj = new Gamification_UserModerationRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $totalRecord = $userModerationObj->getTotalCount();
                $userModerationObj = new Gamification_UserModerationRels("userId||$userId", "publicationId||$publicationId", "quantity||$totalRecord", "clientId||$clientId");
                for ($i = 0; $i < $totalRecord; $i++) {
                    try {
                        $moderatorNo = $userModerationObj->getModeratorNo($i);
                        $moderationObj = new Gamification_ModeratorTypes("moderatorNo||$moderatorNo", "quantity||1");
                        $record['moderator'][$i] = $moderationObj;
                    } catch (Exception $e) {
                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
                // getting the level
                $record['level'] = array();
                $userModerationObj = new Gamification_UserLevelRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $totalRecord = $userModerationObj->getTotalCount();
                if ($totalRecord > 0) {
                    $userModerationObj = new Gamification_UserLevelRels("userId||$userId", "publicationId||$publicationId", "quantity||$totalRecord", "clientId||$clientId");
                    for ($i = 0; $i < $totalRecord; $i++) {
                        try {
                            $userLevelNo = $userModerationObj->getUserLevelNo($i);
                            $moderationObj = new Gamification_UserLevelTypes("userLevelNo||$userLevelNo", "publicationId||$publicationId", "quantity||1");
                            $record['level'][$i] = $moderationObj;
                        } catch (Exception $e) {
                            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                        }
                    }
                } else {
                    $userLevelNo = 1;
                    $moderationObj = new Gamification_UserLevelTypes("userLevelNo||$userLevelNo", "publicationId||$publicationId", "quantity||1");
                    $record['level'][$i] = $moderationObj;
                    try {
                        $levelTypeObj = new Gamification_Db_UserLevelRel('add');
                        $levelTypeObj->set("userId||$userId", "publicationId||$publicationId", "userLevelNo||$userLevelNo", "clientId||$clientId");
                    } catch (Exception $e) {
                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
                // getting the collectible
                $record['collectible'] = array();
                $userCollectibleObj = new Gamification_UserCollectibleRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $totalRecord = $userCollectibleObj->getTotalCount();
                $userCollectibleObj = new Gamification_UserCollectibleRels("userId||$userId", "publicationId||$publicationId", "quantity||$totalRecord", "clientId||$clientId");
                for ($i = 0; $i < $totalRecord; $i++) {
                    try {
                        $collectibleId = $userCollectibleObj->getCollectibleId($i);
                        $collectibleObj = new Gamification_Collectible($collectibleId);
                        $record['collectible'][$i] = $collectibleObj;
                    } catch (Exception $e) {
                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
                // getting the badge
                $record['badge'] = array();
                $userBadgeObj = new Gamification_UserBadgesRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $totalRecord = $userBadgeObj->getTotalCount();
                $userBadgeObj = new Gamification_UserBadgesRels("userId||$userId", "publicationId||$publicationId", "quantity||$totalRecord", "clientId||$clientId");
                for ($i = 0; $i < $totalRecord; $i++) {
                    try {
                        $badgeId = $userBadgeObj->getBadgeId($i);
                        $badgeObj = new Gamification_Badge($badgeId);
                        $record['badge'][$i] = $badgeObj;
                    } catch (Exception $e) {
                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                $record["error"] = TRUE;
                $record["error_msg"] = $e->getMessage();
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
        return $record;
    }

    /**
     * @author Ashish Kumar
     * @param int  $publicationId <p></p>
     * @desc get activityId by activity name
     * @return associative array that is
     * returned contains the following fields:
     * <p>error = BOOLEAN,error_msg</p>
     * <p>UserId</p>array
     * <p>UserPoints</p>array
     */
    public static function getTopContributors($publicationId = FALSE, $pageNo = 1, $quantity = 10, $clientId = 1) {
        $record = array();
        if ($publicationId) {
            $publicationId = trim($publicationId);
            if (empty($publicationId)) {
                $record["error"] = TRUE;
                $record["error_msg"] = "publicationId is not available";
            } else {
                try {
                    $obj = new Gamification_UserPublicationStatuses("publicationId||$publicationId", "status||active", "clientId||$clientId", "count||Y", "quantity||$quantity");
                    $record['totalCount'] = $total = $obj->getTotalCount();
                    $record['totalPages'] = $totalPage = $obj->getTotalPages();
                    $record['data'] = array();
                    if ($total > 0) {
                        $publicationObj = new Gamification_UserPublicationStatuses("publicationId||$publicationId", "status||active", "clientId||$clientId", "sortColumn||totalPositivePoints", "sortOrder||desc", "pageNumber||$pageNo", "quantity||$quantity");
                        $resultCount = $publicationObj->getResultCount();
                        for ($i = 0; $i < $resultCount; $i++) {
                            try {
                                $userId = $publicationObj->getUserId($i);
                                $record['data'][] = $userId;
                            } catch (Exception $e) {
                                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                            }
                        }
                    }
                } catch (Exception $e) {
                    $record["error"] = TRUE;
                    $record["error_msg"] = "Exception:" . $e->getMessage();
                    Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                    $log = Log4Php_Logger::getLogger('databaseAppender');
                    $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                }
            }
        }
        return $record;
    }

    public static function setPublication($publicationId = 149) {
        $_SESSION['publicationId'] = $publicationId;
        setcookie("publicationId", $publicationId, time() + 1800, "/");
    }

    public static function getPublication() {

        if (isset($_SESSION ['publicationId']) && $_SESSION ['publicationId'] != 0) {
            $publicationId = $_SESSION ['publicationId'];
            setcookie("publicationId", $publicationId, time() + 1800, "/");
            return $publicationId;
        }// then check the cookie
        else if (isset($_COOKIE ['publicationId'])) {
            $publicationId = $_COOKIE ['publicationId'];
            setcookie("publicationId", $publicationId, time() + 1800, "/");
            $_SESSION['publicationId'] = $publicationId;
            return $publicationId;
        }else
            return 149;
    }

    public static function getAllGroupsFromStructuredWiki() {
        $publicationId = isset($_SESSION ['publicationId']) ? $_SESSION ['publicationId'] : 0;
        if (empty($publicationId) || !is_numeric($publicationId))
            throw new Exception('PublicationId should be a natural number.');
        try {
            $structuredwikiObj = new StructuredWiki_Groups();
            return $structuredwikiObj->getAllGroups($publicationId);
        } catch (Exception $e) {
//            Instapress_Core_Helper::describe($e->getMessage());
//            Instapress_Core_Helper::describe($e->getTraceAsString());
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
        }
    }

    /**
     * @author Mayank Gupta 20111214$.
     * @desc Method use to create Friend Activity Stream, "STRICTNESS aplicable here because we are dealing with bulk inserts via STORED PROCEDURES". Changes make very carefully.
     * @param Integer $userId
     * @param Integer $publicationId
     * @param Integer $activityId
     * @param String $activityObject
     * @param String $activityVerb
     * @param String $comman
     */
    public static function createFriendActivities($userId, $publicationId, $activityId, $activityObject, $activityVerb, $comman, $activityText = 'activity text') {
        if (empty($userId) || !is_numeric($userId))
            throw new Exception('UserId should be a natural number.');
        if (empty($publicationId) || !is_numeric($publicationId))
            throw new Exception('PublicationId should be a natural number.');
        if (empty($activityId) || !is_numeric($activityId))
            throw new Exception('ActivityId should be a natural number.');
        if (empty($activityObject) || !is_string($activityObject) || strlen($activityObject) > 25)
            throw new Exception('ActivityObject should be a string and have at most 25 characters.');
        if (empty($activityVerb) || !is_string($activityVerb) || strlen($activityVerb) > 50)
            throw new Exception('ActivityVerb should be a string and have at most 50 characters.');
        if (empty($comman) || !is_string($comman) || ($comman != 'YES' && $comman != 'NO'))
            throw new Exception('Comman should be a string and can have value YES or NO');
        if (empty($activityText) || !is_string($activityText))
            throw new Exception('ActivityText should be a string.');

        //type conversion.
        $userId = (int) $userId;
        $publicationId = (int) $publicationId;
        $activityId = (int) $activityId;
//        $comman=$comman=='YES'?'Y':'N';

        try {
            $friendActivityStreamDbStream = new Gamification_Db_FriendActivityStream();
            $friendActivityStreamDbStream->specialQuery("call createPublicationFriendActivityStream($userId, $publicationId, $activityId, '$activityObject', '$activityVerb', '$comman', '$activityText')", false);
        } catch (Exception $e) {
//            Instapress_Core_Helper::describe($e->getMessage());
//            Instapress_Core_Helper::describe($e->getTraceAsString());
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
        }
    }

    /**
     * @author Mayank Gupta 20111224$
     * @desc   Method retrieve user friend Activities from gamification friend_activity_stream table.
     * @param  Integer $userId
     * @param  Integer $publicationId
     * @param  Optional Integer $quantity
     * @return Array
     */
    public static function getFriendActivities($userId, $publicationId, $quantity = 20) {
        if (empty($userId) || !is_numeric($userId))
            throw new Exception('UserId should be a natural number.');

        if (empty($publicationId) || !is_numeric($publicationId))
            throw new Exception('publicationId should be a natural number.');

        Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
        $log = Log4Php_Logger::getLogger('databaseAppender');
        try {
            $finalResultSetArray = $friendActivitiesDataArray = array();
            $friendActivitiesObject = new Gamification_FriendActivityStreams();
            $friendActivitiesDataArray = $friendActivitiesObject->getUserFriendsActivityStream($userId, $publicationId, $quantity);
            if (is_array($friendActivitiesDataArray) && count($friendActivitiesDataArray) > 0) {
                $cnt = 0;
                foreach ($friendActivitiesDataArray as $friendActivitiesDataObject) {
                    try {
                        $activityId = $imageId = 0;
                        $finalResultSetArray[$cnt]['friendUserId'] = $friendActivitiesDataObject->getFriendId();
                        $finalResultSetArray[$cnt]['activityObject'] = $friendActivitiesDataObject->getActivityObject();
                        $finalResultSetArray[$cnt]['activityVerb'] = $friendActivitiesDataObject->getActivityVerb();
                        $finalResultSetArray[$cnt]['comman'] = $friendActivitiesDataObject->getComman();
                        $finalResultSetArray[$cnt]['createdTime'] = $friendActivitiesDataObject->getCreatedTime();
                        $activityId = $friendActivitiesDataObject->getActivityId();
                        $finalResultSetArray[$cnt]['activityId'] = $activityId;
                        $userActivityRelSingularObject = new Gamification_UserActivityRel($activityId);
                        $finalResultSetArray[$cnt]['activityText'] = $userActivityRelSingularObject->getActivityText();
                        $finalResultSetArray[$cnt]['contentId'] = $userActivityRelSingularObject->getContentId();
                        $cnt++;
                    } catch (Exception $e) {
//                    describe($e->getMessage() . "\n" . $e->getTraceAsString());

                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
            }
            return (is_array($finalResultSetArray) && count($finalResultSetArray) > 0) ? $finalResultSetArray : false;
        } catch (Exception $e) {
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
    }

    /**
     * @author Mayank Gupta 20111224$
     * @desc   Method retrieve user Activities from gamification user_activity_rel table.
     * @param  Integer $userId
     * @param  Integer $publicationId
     * @param  Optional Integer $quantity
     * @return Array
     */
    public static function getUserActivities($userId, $publicationId = 0, $quantity = 20, $pageNumber = 1) {
        if (empty($userId) || !is_numeric($userId))
            throw new Exception('UserId should be a natural number.');

        if (empty($publicationId) || !is_numeric($publicationId))
            throw new Exception('publicationId should be a natural number.');
        Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
        $log = Log4Php_Logger::getLogger('databaseAppender');
        try {
            $finalResultSetArray = $userActivitiesDataArray = array();
            $userActivitiesObject = new Gamification_UserActivityRels();
            $userActivitiesDataArray = $userActivitiesObject->getAllGamificationUserActivityRelData($userId, $publicationId, $quantity, $pageNumber);
            $finalResultSetArray['info']['totalResult'] = $userActivitiesDataArray['totalResult'];
            $finalResultSetArray['info']['totalPages'] = $userActivitiesDataArray['totalPages'];
            if (is_array($userActivitiesDataArray['data']) && count($userActivitiesDataArray['data']) > 0) {
                $cnt = 0;
                foreach ($userActivitiesDataArray['data'] as $userActivitiesDataObject) {
                    try {
                        $activityId = $imageId = 0;
                        $finalResultSetArray['data'][$cnt]['activityObject'] = $userActivitiesDataObject->getActivityObject();
                        $finalResultSetArray['data'][$cnt]['activityVerb'] = $userActivitiesDataObject->getActivityVerb();
                        $finalResultSetArray['data'][$cnt]['comman'] = $userActivitiesDataObject->getComman();
                        $finalResultSetArray['data'][$cnt]['createdTime'] = $userActivitiesDataObject->getCreatedTime();
                        $finalResultSetArray['data'][$cnt]['activityText'] = $userActivitiesDataObject->getActivityText();
                        $finalResultSetArray['data'][$cnt]['contentId'] = $userActivitiesDataObject->getContentId();
                        $cnt++;
                    } catch (Exception $e) {
//                    describe($e->getMessage() . "\n" . $e->getTraceAsString());
                        Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                        $log = Log4Php_Logger::getLogger('databaseAppender');
                        $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                    }
                }
            }
            return (is_array($finalResultSetArray['data']) && count($finalResultSetArray['data']) > 0) ? $finalResultSetArray : false;
        } catch (Exception $e) {
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
    }

    public function getUserActivityStreamForUpdate($userId, $publicationId = 0, $clientId = 1, $quantity = 20, $pageNumber = 1) {
        Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
        $log = Log4Php_Logger::getLogger('databaseAppender');
        try {
            if (empty($userId) || !is_numeric($userId)) {
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "must enter userId and it should be numeric");
                return false;
            } else {
                if (empty($publicationId) || !is_numeric($publicationId)) {
                    $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "must enter publicationId and it should be numeric");
                    return false;
                } else {

                    $finalResultSetArray = array();
                    $activityObj = new Gamification_Db_UserActivityRel();
                    $activityObj->setForcedConditon("activityVerb IN('POST','BOOST')");
                    $activityObj->set("userId||$userId", "publicationId||$publicationId", "activityObject||UPDATE", "clientId||$clientId", "count||Y", "quantity||$quantity");
                    $totalPages = $activityObj->getTotalPages();
                    $finalResultSetArray['info']['totalResult'] = $activityObj->getTotalCount();
                    $finalResultSetArray['info']['totalPages'] = $totalPages;
                    if ($pageNumber <= $totalPages) {
                        $activityObj = new Gamification_Db_UserActivityRel();
                        $activityObj->setForcedConditon("activityVerb IN('POST','BOOST')");
                        $activityObj->set("userId||$userId", "publicationId||$publicationId", "activityObject||UPDATE", "clientId||$clientId", "pageNumber||$pageNumber", "quantity||$quantity");
                        $resultCount = $activityObj->getResultCount();
                        for ($i = 0; $i < $resultCount; $i++) {
                            $finalResultSetArray['data'][$i]['activityObject'] = $activityObj->get("activityObject", $i);
                            $finalResultSetArray['data'][$i]['activityVerb'] = $activityObj->get("activityVerb", $i);
                            $finalResultSetArray['data'][$i]['comman'] = $activityObj->get("comman", $i);
                            $finalResultSetArray['data'][$i]['createdTime'] = $activityObj->get("createdTime", $i);
                            $finalResultSetArray['data'][$i]['activityText'] = $activityObj->get("activityText", $i);
                            $finalResultSetArray['data'][$i]['contentId'] = $activityObj->get("contentId", $i);
                        }
                        return $finalResultSetArray;
                    }
                }
            }
        } catch (Exception $e) {
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
        return false;
    }

    public function getFriendsActivityStreamForUpdate($userId, $publicationId = 0, $clientId = 1, $quantity = 20, $pageNumber = 1) {
        Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
        $log = Log4Php_Logger::getLogger('databaseAppender');
        try {
            if (empty($userId) || !is_numeric($userId)) {
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "must enter userId and it should be numeric");
                return false;
            } else {
                if (empty($publicationId) || !is_numeric($publicationId)) {
                    $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "must enter publicationId and it should be numeric");
                    return false;
                } else {

                    $finalResultSetArray = array();
                    $activityObj = new Gamification_Db_FriendActivityStream();
                    $activityObj->setForcedConditon("activityVerb IN('POST','BOOST')");
                    $activityObj->set("userId||$userId", "publicationId||$publicationId", "activityObject||UPDATE", "count||Y", "quantity||$quantity");
                    $totalPages = $activityObj->getTotalPages();
                    $finalResultSetArray['info']['totalResult'] = $activityObj->getTotalCount();
                    $finalResultSetArray['info']['totalPages'] = $totalPages;
                    if ($pageNumber <= $totalPages) {
                        $activityObj = new Gamification_Db_FriendActivityStream();
                        $activityObj->setForcedConditon("activityVerb IN('POST','BOOST')");
                        $activityObj->set("userId||$userId", "publicationId||$publicationId", "activityObject||UPDATE", "pageNumber||$pageNumber", "quantity||$quantity");
                        $resultCount = $activityObj->getResultCount();
                        for ($i = 0; $i < $resultCount; $i++) {
                            $activityId = $activityObj->get("activityId", $i);
                            try {
                                $activityRelObj = new Gamification_UserActivityRel($activityId);
                                $finalResultSetArray['data'][$i]['activityObject'] = $activityRelObj->getActivityObject();
                                $finalResultSetArray['data'][$i]['activityVerb'] = $activityRelObj->getActivityVerb();
                                $finalResultSetArray['data'][$i]['comman'] = $activityRelObj->getComman();
                                $finalResultSetArray['data'][$i]['createdTime'] = $activityRelObj->getCreatedTime();
                                $finalResultSetArray['data'][$i]['activityText'] = $activityRelObj->getActivityText();
                                $finalResultSetArray['data'][$i]['contentId'] = $activityRelObj->getContentId();
                            } catch (Exception $e) {
                                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                            }
                        }
                        return $finalResultSetArray;
                    }
                }
            }
        } catch (Exception $e) {
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
        return false;
    }

    public function getUserActivityDataForVerbAndObject($userId, $publicationId = 0, $objectArray = FALSE, $verbArray = FALSE) {
        Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
        $log = Log4Php_Logger::getLogger('databaseAppender');
        try {

            if (empty($userId) || !is_numeric($userId)) {
                // error
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "must enter userId and it should be numeric");
                return false;
            } else {
                if (empty($publicationId) || !is_numeric($publicationId)) {
                    // error
                    $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "must enter userId and it should be numeric");
                    return false;
                } else {
                    $finalResultSetArray = array();
                    $cnt = 0;
                    if ($objectArray) {
                        if (is_array($objectArray)) {
                            if ($verbArray) {
                                if (is_array($verbArray)) {
                                    // both object and verb
                                    foreach ($objectArray as $object) {
                                        foreach ($verbArray as $verb) {
                                            $verb = strtoupper($verb);
                                            $object = strtoupper($object);
                                            $activityObj = new Gamification_UserActivityRels("userId||$userId", "publicationId||$publicationId", "activityObject||$object", "activityVerb||$verb");
                                            $quantity = $activityObj->getTotalCount();
                                            if ($quantity > 0) {
                                                $activityObj = new Gamification_UserActivityRels("userId||$userId", "publicationId||$publicationId", "activityObject||$object", "activityVerb||$verb", "quantity||$quantity");
                                                for ($i = 0; $i < $quantity; $i++) {
                                                    $finalResultSetArray['data'][$cnt]['activityObject'] = $activityObj->getActivityObject($i);
                                                    $finalResultSetArray['data'][$cnt]['activityVerb'] = $activityObj->getActivityVerb($i);
                                                    $finalResultSetArray['data'][$cnt]['comman'] = $activityObj->getComman($i);
                                                    $finalResultSetArray['data'][$cnt]['createdTime'] = $activityObj->getCreatedTime($i);
                                                    $finalResultSetArray['data'][$cnt]['activityText'] = $activityObj->getActivityText($i);
                                                    $finalResultSetArray['data'][$cnt]['contentId'] = $activityObj->getContentId($i);
                                                    $cnt++;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    // error
                                    $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "verbArray should be an array");
                                    return false;
                                }
                            } else {
                                // only object
                                foreach ($objectArray as $object) {
                                    $object = strtoupper($object);
                                    $activityObj = new Gamification_UserActivityRels("userId||$userId", "publicationId||$publicationId", "activityObject||$object");
                                    $quantity = $activityObj->getTotalCount();
                                    if ($quantity > 0) {
                                        $activityObj = new Gamification_UserActivityRels("userId||$userId", "publicationId||$publicationId", "activityObject||$object", "quantity||$quantity");
                                        for ($i = 0; $i < $quantity; $i++) {
                                            $finalResultSetArray['data'][$cnt]['activityObject'] = $activityObj->getActivityObject($i);
                                            $finalResultSetArray['data'][$cnt]['activityVerb'] = $activityObj->getActivityVerb($i);
                                            $finalResultSetArray['data'][$cnt]['comman'] = $activityObj->getComman($i);
                                            $finalResultSetArray['data'][$cnt]['createdTime'] = $activityObj->getCreatedTime($i);
                                            $finalResultSetArray['data'][$cnt]['activityText'] = $activityObj->getActivityText($i);
                                            $finalResultSetArray['data'][$cnt]['contentId'] = $activityObj->getContentId($i);
                                            $cnt++;
                                        }
                                    }
                                }
                            }
                        } else {
                            // error
                            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "objectArray should be an array");
                            return false;
                        }
                    } else if ($verbArray) {
                        if (is_array($verbArray)) {
                            // only verb
                            foreach ($verbArray as $verb) {
                                $verb = strtoupper($verb);
                                $activityObj = new Gamification_UserActivityRels("userId||$userId", "publicationId||$publicationId", "activityVerb||$verb");
                                $quantity = $activityObj->getTotalCount();
                                if ($quantity > 0) {
                                    $activityObj = new Gamification_UserActivityRels("userId||$userId", "publicationId||$publicationId", "activityVerb||$verb", "quantity||$quantity");
                                    for ($i = 0; $i < $quantity; $i++) {
                                        $finalResultSetArray['data'][$cnt]['activityObject'] = $activityObj->getActivityObject($i);
                                        $finalResultSetArray['data'][$cnt]['activityVerb'] = $activityObj->getActivityVerb($i);
                                        $finalResultSetArray['data'][$cnt]['comman'] = $activityObj->getComman($i);
                                        $finalResultSetArray['data'][$cnt]['createdTime'] = $activityObj->getCreatedTime($i);
                                        $finalResultSetArray['data'][$cnt]['activityText'] = $activityObj->getActivityText($i);
                                        $finalResultSetArray['data'][$cnt]['contentId'] = $activityObj->getContentId($i);
                                        $cnt++;
                                    }
                                }
                            }
                        } else {
                            // error
                            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), "verbArray should be an array");
                            return false;
                        }
                    } else { // neither object nor verb
                        $activityObj = new Gamification_UserActivityRels("userId||$userId", "publicationId||$publicationId");
                        $quantity = $activityObj->getTotalCount();
                        if ($quantity > 0) {
                            $activityObj = new Gamification_UserActivityRels("userId||$userId", "publicationId||$publicationId", "quantity||$quantity");
                            for ($i = 0; $i < $quantity; $i++) {
                                $finalResultSetArray['data'][$cnt]['activityObject'] = $activityObj->getActivityObject($i);
                                $finalResultSetArray['data'][$cnt]['activityVerb'] = $activityObj->getActivityVerb($i);
                                $finalResultSetArray['data'][$cnt]['comman'] = $activityObj->getComman($i);
                                $finalResultSetArray['data'][$cnt]['createdTime'] = $activityObj->getCreatedTime($i);
                                $finalResultSetArray['data'][$cnt]['activityText'] = $activityObj->getActivityText($i);
                                $finalResultSetArray['data'][$cnt]['contentId'] = $activityObj->getContentId($i);
                                $cnt++;
                            }
                        }
                    }
                    return $finalResultSetArray;
                }
            }
            return false;
        } catch (Exception $e) {
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
    }

    public static function getLevelTypeByPublication($publicationId) {
        $record = array();
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception('publicationId should be a natural number.or publication should not be empty');
        } else {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            try {
                $publicationlevelObj = new Gamification_UserLevelTypes("publicationId||$publicationId", "count||Y");
                $count = $publicationlevelObj->getTotalCount();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $j = $i + 1;
                        try {
                            $levelObj = new Gamification_UserLevelTypes("userLevelNo||$j", "publicationId||$publicationId");
                            $imageurl = "";
                            $imageurl = $levelObj->getImageUrl();
                            if (!empty($imageurl)) {
                                $imageurl = Instacheckin_Utility::getImageOnSizeFromUrl("http://www.instablogsimages.com/" . $levelObj->getImageUrl(), "s");
                            }
                        } catch (Exception $e) {

                            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                        }
                        $userLevelRelPluralObject = new Gamification_UserLevelRels("userLevelNo||$j", "publicationId||$publicationId", "count||Y");
                        $record[$levelObj->getUserLevelName()] = array('levelUserCount' => $userLevelRelPluralObject->getTotalCount(), 'levelImageURL' => $imageurl);
                    }
                }
            } catch (Exception $e) {
//                describe($e->getMessage());
//                describe($e->getTraceAsString());
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
            return $record;
        }
    }

    public static function isUserModerator($publicationId, $userId, $clientId = 1) {
        $Ismodarator = 'N';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception('publicationId should be a natural number.or publication should not be empty');
        } else if (empty($userId) || !is_numeric($userId)) {
            throw new Exception('userId should be a natural number.or userId should not be empty');
        } else {
            try {
                $ModObj = new Gamification_UserModerationRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $count = $ModObj->getTotalCount();
                if ($count > 0) {
                    $Ismodarator = 'Y';
                }
            } catch (Exception $e) {
//                describe($e->getMessage());
//                describe($e->getTraceAsString());
                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                $log = Log4Php_Logger::getLogger('databaseAppender');
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
            return $Ismodarator;
        }
    }

    public static function getActivityStream($publicationId = 0, $quantity = 20) {
        $finalResultSetArray = $friendActivitiesDataArray = array();
        $friendActivitiesObject = new Gamification_UserActivityRels();
        $friendActivitiesDataArray = $friendActivitiesObject->getAllActivities($publicationId, $quantity);
        Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
        $log = Log4Php_Logger::getLogger('databaseAppender');
        try {
            if (is_array($friendActivitiesDataArray) && count($friendActivitiesDataArray) > 0) {
                $cnt = 0;
                foreach ($friendActivitiesDataArray as $friendActivitiesDataObject) {
                    if (strtolower($friendActivitiesDataObject->getActivityObject()) == "entity" || strtolower($friendActivitiesDataObject->getActivityObject()) == "element") {
                        try {
                            $activityId = $contentId = $userId = $imageId = 0;
                            $userId = $friendActivitiesDataObject->getUserId();
//                            $userDetailSingularObject = new Instacheckin_User($userId);
                            $finalResultSetArray[$cnt]['userId'] = $userId;

//                            $finalResultSetArray[$cnt]['isModerator'] = $userDetailSingularObject->isModerator($publicationId);
//                            $finalResultSetArray[$cnt]['userImage'] = $userDetailSingularObject->getProfileImage("small");
//                            $finalResultSetArray[$cnt]['userName'] = $userDetailSingularObject->getUserFirstName() . ' ' . $userDetailSingularObject->getUserLastName();
                            $activityId = $friendActivitiesDataObject->getUserActivityId();
                            $finalResultSetArray[$cnt]['activityId'] = $activityId;
                            $contentId = $friendActivitiesDataObject->getContentId();
//                    $userActivityRelSingularObject = new Gamification_UserActivityRel($activityId);
//                    $contentId = $userActivityRelSingularObject->getContentId();
//                            if (strtolower($friendActivitiesDataObject->getActivityObject()) == "entity") {
//                                $contentId;
//                                $objEntity = new StructuredWiki_Entity($contentId);
//                                $elementSingularObj = new StructuredWiki_Element($objEntity->getprofileElementId());
//                            } else if (strtolower($friendActivitiesDataObject->getActivityObject()) == "element") {
//                                $contentId;
//                            $elementSingularObj = new StructuredWiki_Element($contentId);
//                            }
                            $elementValuesArray = StructuredWiki_WikiUtility::getElementValues($contentId);
                            $finalResultSetArray[$cnt]['elementId'] = $contentId; //$elementSingularObj->getElementId();
                            $finalResultSetArray[$cnt]['elementName'] = $elementValuesArray['elementName']; //$elementSingularObj->getElementName();
//                            $elementValuesArray = StructuredWiki_WikiUtility::getElementValues($elementSingularObj->getElementId());
                            $finalResultSetArray[$cnt]['elementDescription'] = $elementValuesArray['description'];
//                            $imageId = $elementSingularObj->getElementPrimaryImageId();
//                            if (!empty($imageId)) {
                            if (!empty($elementValuesArray['imageId'])) {
                                $finalResultSetArray[$cnt]['elementPrimaryImageId'] = $elementValuesArray['imageId']; //$imageId;
//                        $imageSingularObject = new StructuredWiki_Image($imageId);
//                        $finalResultSetArray[$cnt]['elementPrimaryImagePath'] = 'http://' . $imageSingularObject->getImageLink();
                            } else {
                                if (!empty($elementValuesArray['image'])) {
                                    $imageRecordIdObject = new StructuredWiki_Images();
                                    $imageId = $imageRecordIdObject->getEntityPrimaryImageRecordId($elementValuesArray['image']);
                                    $finalResultSetArray[$cnt]['elementPrimaryImageId'] = $imageId;
//                            $imageSingularObject = new StructuredWiki_Image($imageId);
//                            $finalResultSetArray[$cnt]['elementPrimaryImagePath'] = 'http://' . $imageSingularObject->getImageLink();
                                }
                            }
//                            $entityImageRecordId = StructuredWiki_WikiUtility::getEntityDefaultImage($elementSingularObj->getEntityId());
                            $entityImageRecordId = StructuredWiki_WikiUtility::getEntityDefaultImage($elementValuesArray['entityId']);
                            try {
                                if ($entityImageRecordId) {
                                    $entityImageLink = StructuredWiki_WikiUtility::getCroppedImage($entityImageRecordId, 40, 40, 'adaptive');
                                } else {
                                    $entityImageLink = false;
                                }
                            } catch (Exception $e) {
                                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                                $entityImageLink = false;
                            }

//                            $entityObj = new StructuredWiki_Entity($elementSingularObj->getEntityId());
                            $entityObj = new StructuredWiki_Entity($elementValuesArray['entityId']);
                            $finalResultSetArray[$cnt]['entityName'] = $entityObj->getEntityName();
                            $finalResultSetArray[$cnt]['entityImage'] = $entityImageLink;
                            $finalResultSetArray[$cnt]['entitySlug'] = $entityObj->getEntitySlug();
                            $finalResultSetArray[$cnt]['elementPrice'] = $elementValuesArray['price']; //$elementSingularObj->getElementPrice();
                            $finalResultSetArray[$cnt]['elementSlug'] = $elementValuesArray['elementSlug']; //$elementSingularObj->getElementSlug();
                            $finalResultSetArray[$cnt]['groupId'] = $elementValuesArray['groupId']; //$elementSingularObj->getGroupId();
                            $finalResultSetArray[$cnt]['entityId'] = $elementValuesArray['entityId']; //$elementSingularObj->getEntityId();
                            $finalResultSetArray[$cnt]['activityObject'] = $friendActivitiesDataObject->getActivityObject();
                            $finalResultSetArray[$cnt]['activityVerb'] = $friendActivitiesDataObject->getActivityVerb();
                            $finalResultSetArray[$cnt]['activityText'] = $friendActivitiesDataObject->getActivityText();
                            $finalResultSetArray[$cnt]['comman'] = $friendActivitiesDataObject->getComman();
                            $finalResultSetArray[$cnt]['createdTime'] = $friendActivitiesDataObject->getCreatedTime();
                            $cnt++;
                        } catch (Exception $e) {
//                        describe($e->getMessage());
//                        describe($e->getTraceAsString());

                            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                        }
                    }
                }
            }
            return (is_array($finalResultSetArray) && count($finalResultSetArray) > 0) ? $finalResultSetArray : false;
        } catch (Exception $e) {
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
    }

    public static function getfollowerInfo($userId, $publicationId, $clientId = 1) {
        $record = array();
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception('publicationId should be a natural number.or publication should not be empty');
        } else if (empty($userId) || !is_numeric($userId)) {
            throw new Exception('userId should be a natural number.or userId should not be empty');
        } else {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            try {
//                $userInfo = new Instacheckin_User($userId);
                $record['userId'] = $userId;
//                $record['name'] = $userInfo->getUserFirstName() . $userInfo->getUserLastName();
//                $record['userFirstName'] = $userInfo->getUserFirstName();
//                $record['userImage'] = $userInfo->getProfileImage("small");
//                $record['userLink'] = "/profile/" . $userInfo->getUserLogin();
                $totalPoint = array();
                $totalPoint = Gamification_Utility::getUserTotalPoints($userId, $publicationId, $clientId);
                $record['totalPoint'] = $totalPoint['totalPositivePoints'] - $totalPoint['totalNegativePoints'];
                $badgeName = new Gamification_UserBadgesRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId", "count||Y");
                $count = $badgeName->getTotalCount();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        try {
                            $badgeName = new Gamification_UserBadgesRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId");
                            $id = $badgeName->getBadgeId($i);
                            $badgeNames = new Gamification_Badges("badgeId||$id");
                            $record['badges'][$i]['badgeName'] = $badgeNames->getBadgeName();
                            $record['badges'][$i]['badgeUrl'] = $badgeNames->getImageUrl();
                            $record['badges'][$i]['badgeId'] = $id;
                        } catch (Exception $e) {
                            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                        }
                    }
                }
                $levelType = new Gamification_UserLevelRels("userId||$userId", "publicationId||$publicationId", "clientId||$clientId");
                if ($levelType->getResultCount() > 0) {
                    $levelNo = $levelType->get(0)->getUserLevelNo(0);
                    $LevelNames = new Gamification_UserLevelTypes("userLevelNo||$levelNo");
                    $record['levelType']['levelNo'] = $levelNo;
                    $record['levelType']['levelName'] = $LevelNames->getUserLevelName();
                    $record['levelType']['levelUrl'] = $LevelNames->getImageUrl();
                } else {
                    $levelNo = 1;
                    $LevelNames = new Gamification_UserLevelTypes("userLevelNo||$levelNo");
                    $record['levelType']['levelNo'] = $levelNo;
                    $record['levelType']['levelName'] = $LevelNames->getUserLevelName();
                    $record['levelType']['levelUrl'] = $LevelNames->getImageUrl();
                }
            } catch (Exception $e) {
//                describe($e->getMessage());
//                describe($e->getTraceAsString());

                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
            return $record;
        }
    }

    public static function getTimeLineEntityInfo($entityId, $publicationId, $clientId = 1) {
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception('publicationId should be a natural number.or publication should not be empty');
        } else if (empty($entityId) || !is_numeric($entityId)) {
            throw new Exception('entityId should be a natural number.or entityId should not be empty');
        } else {
            try {
                $record = array();
                $entityRecord = array();
                $entityObj = new StructuredWiki_Entities("entityId||$entityId", "publicationId||$publicationId");
                $profileElementId = $entityObj->getProfileElementId();
                $entityRecord = StructuredWiki_WikiUtility::getElementValues($profileElementId);
                $record['name'] = $entityRecord['name'];
                $record['entitySlug'] = $entityObj->getEntitySlug() . ".html";
                $record['gender'] = $entityRecord['gender'];
                if (!empty($entityRecord['networth'])) {
                    if ($entityRecord['networth'] < 1) {
                        $record['networth'] = $entityRecord['networth'] * 1000 . " M";
                    } else {
                        $record['networth'] = $entityRecord['networth'] . " B";
                    }
                }
                else
                    $record['networth'] = 0;

//                $record['networth'] = $entityRecord['networth'];
                $record['flag'] = $entityRecord['nationality'];
                $record['imageLink'] = false;
                try {
                    if (isset($entityRecord['image']) && is_numeric(($entityRecord['image']))) {
                        $imageObj = new StructuredWiki_Images();
                        $entityPrimaryImageId = $imageObj->getEntityPrimaryImageRecordId(($entityRecord['image']));
                        if (!empty($entityPrimaryImageId)) {
                            $entityImageLink = StructuredWiki_WikiUtility::getCroppedImage($entityPrimaryImageId, 101, 119, "adaptive");
                            if (trim($entityImageLink) == 'http://')
                                $record['imageLink'] = false;
                            else
                                $record['imageLink'] = $entityImageLink;
                        }
                    }
                } catch (Exception $e) {
                    Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                    $log = Log4Php_Logger::getLogger('databaseAppender');
                    $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                }
                $metallisticGroupArray = array("Yachts" => 4, "Estates" => 3, "Private jets" => 6);
                foreach ($metallisticGroupArray as $metallisticGroupKey => $metallisticGroupValue) {
                    $elementObj = new StructuredWiki_Elements("entityId||$entityId", "groupId||$metallisticGroupValue", "count||Y");
                    $record[$metallisticGroupKey] = $elementObj->getTotalCount();
                }
                return $record;
            } catch (Exception $e) {
//                describe($e->getMessage());
//                describe($e->getTraceAsString());
//                Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
//                $log = Log4Php_Logger::getLogger('databaseAppender');
//                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            }
        }
    }

    public static function getElementInfoByContentId($contentId) {
        Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
        $log = Log4Php_Logger::getLogger('databaseAppender');
        try {
            $record = array();
            $elementSingularObj = new StructuredWiki_Element($contentId);
            $record['elementId'] = $elementSingularObj->getElementId();
            $record['elementName'] = $elementSingularObj->getElementName();
            $elementValuesArray = StructuredWiki_WikiUtility::getElementValues($contentId);
            $record['elementDescription'] = $elementValuesArray['description'];
            $imageId = $elementSingularObj->getElementPrimaryImageId();
            if (!empty($imageId)) {
                $record['elementPrimaryImageId'] = $imageId;
//                        $imageSingularObject = new StructuredWiki_Image($imageId);
//                        $record['elementPrimaryImagePath'] = 'http://' . $imageSingularObject->getImageLink();
            } else {
                if (!empty($elementValuesArray['image'])) {
                    $imageRecordIdObject = new StructuredWiki_Images();
                    $imageId = $imageRecordIdObject->getEntityPrimaryImageRecordId($elementValuesArray['image']);
                    $record['elementPrimaryImageId'] = $imageId;
//                            $imageSingularObject = new StructuredWiki_Image($imageId);
//                            $record['elementPrimaryImagePath'] = 'http://' . $imageSingularObject->getImageLink();
                }
            }
            $entityImageRecordId = StructuredWiki_WikiUtility::getEntityDefaultImage($elementSingularObj->getEntityId());
            $entityImageLink = false;
            try {
                if ($entityImageRecordId) {
                    $entityImageLink = StructuredWiki_WikiUtility::getCroppedImage($entityImageRecordId, 40, 40, 'adaptive');
                } else {
                    $entityImageLink = false;
                }
            } catch (Exception $e) {
                $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                $entityImageLink = false;
            }

            $entityObj = new StructuredWiki_Entity($elementSingularObj->getEntityId());

            $record['entityId'] = $elementSingularObj->getEntityId();
            $record['entityName'] = $entityObj->getEntityName();
            $record['entityImage'] = $entityImageLink;
            $record['entitySlug'] = $entityObj->getEntitySlug();

//        if ($elementValuesArray['networth'] < 1) {
//           $record['elementPrice'] = $elementValuesArray['networth'] * 1000 . " M";
//        } else {
//            $record['elementPrice'] = $elementValuesArray['networth'] . " B";
//        }
            $record['elementPrice'] = $elementSingularObj->getElementPrice();
            $record['elementSlug'] = $elementSingularObj->getElementSlug();
            $record['groupId'] = $elementSingularObj->getGroupId();
            return $record;
        } catch (Exception $e) {
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
        }
    }

    /**
     * @author Mayank Gupta 20120103$
     * @desc   Method check for remote file existence.
     * @param  String $url
     * @return Boolean true on success false otherwise
     */
    public static function checkFileExistsOnRemoteServer($url) {
        if (empty($url))
            throw new Exception('Please set valid url.');

        $urlArray = parse_url($url);
        if (!is_array($urlArray) || count($urlArray) == 0)
            throw new Exception('Please set valid url.');

        $urlArray['port'] = isset($urlArray['port']) ? $urlArray['port'] : 80;
        $sh = fsockopen($urlArray['host'], $urlArray['port']) or die('cant open socket');
        fputs($sh, "HEAD {$urlArray['path']} HTTP/1.1\r\nHost: {$urlArray['host']}\r\n\r\n");
        while ($line = fgets($sh))
            if (preg_match('/^Content-Length: (d+)/', $line, $m))
                $size = $m[1];
        //echo isset($size) ? "size of $url file is $size" : 'no such file: ' . $url;
        return isset($size) ? true : false;
    }

    public static function mergeUserAccount($primaryUserId, $secondaryUserId, $clientId = 1) {
        if (empty($primaryUserId) || empty($secondaryUserId) || empty($publicationId))
            return false;
        $publicationArray1 = array();
        $activityRelObj1 = new Gamification_UserActivityRels("userId||$primaryUserId", "clientId||$clientId", "count||Y");
        $totalCount1 = $activityRelObj1->getTotalCount();
        if ($totalCount1 > 0) {
            $activityRelObj1 = new Gamification_UserActivityRels("userId||$primaryUserId", "clientId||$clientId", "quantity||$totalCount1");
            for ($i = 0; $i < $totalCount1; $i++) {
                $publicationArray1[] = $activityRelObj1->getPublicationId();
            }
        }
        $publicationArray2 = array();
        $activityRelObj2 = new Gamification_UserActivityRels("userId||$secondaryUserId", "clientId||$clientId", "count||Y");
        $totalCount2 = $activityRelObj2->getTotalCount();
        if ($totalCount2 > 0) {
            $activityRelObj2 = new Gamification_UserActivityRels("userId||$secondaryUserId", "clientId||$clientId", "quantity||$totalCount2");
            for ($i = 0; $i < $totalCount2; $i++) {
                $publicationArray2[] = $activityRelObj2->getPublicationId();
                $userActivityId = $activityRelObj2->getUserActivityId();

                try {
                    // edit the userId
                    $editUserActivityRelObj = new Gamification_Db_UserActivityRel('edit');
                    $editUserActivityRelObj->set("userActivityId||$userActivityId", "userId||$primaryUserId");
                } catch (Exception $e) {
                    Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                    $log = Log4Php_Logger::getLogger('databaseAppender');
                    $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                }
            }
        }
        // merging both publication array
        $publicationArray = array_merge($publicationArray1, $publicationArray2);
        $publicationArray = array_unique($publicationArray);

        foreach ($publicationArray as $publicationId) {
            self::mergeUserLevels($primaryUserId, $secondaryUserId, $publicationId, $clientId = 1);
            self::mergeUserModerations($primaryUserId, $secondaryUserId, $publicationId, $clientId = 1);
            self::mergeUserCollectibles($primaryUserId, $secondaryUserId, $publicationId, $clientId = 1);
            self::mergeUserBadges($primaryUserId, $secondaryUserId, $publicationId, $clientId = 1);
        }
        return true;
    }

    public static function mergeUserLevels($primaryUserId, $secondaryUserId, $publicationId, $clientId = 1) {
        if (empty($primaryUserId) || empty($secondaryUserId) || empty($publicationId))
            return false;
        $userLevelNo1 = 0;
        $userLevelNo2 = 0;
        $levelRelObj1 = new Gamification_UserLevelRels("userId||$primaryUserId", "clientId||$clientId", "publicationId||$publicationId");
        $totalCount1 = $levelRelObj1->getTotalCount();
        if ($totalCount1 > 0) {
            $userLevelNo1 = $levelRelObj1->getUserLevelNo();
            $userLevelRelId1 = $levelRelObj1->getUserLevelRelId();
        }
        $levelRelObj2 = new Gamification_UserLevelRels("userId||$secondaryUserId", "clientId||$clientId", "publicationId||$publicationId");
        $totalCount2 = $levelRelObj2->getTotalCount();
        if ($totalCount2 > 0) {
            $userLevelNo2 = $levelRelObj2->getUserLevelNo();
            $userLevelRelId2 = $levelRelObj2->getUserLevelRelId();
        }
        try {
            if ($userLevelNo1 != 0) {
                if ($userLevelNo2 > $userLevelNo1) {
                    // edit for primary userId
                    $editLevelRelObj = new Gamification_Db_UserLevelRel('edit');
                    $editLevelRelObj->set("userLevelRelId||$userLevelRelId1", "userLevelNo||$userLevelNo2");
                }
            }
            if ($userLevelNo2 != 0) {
                // deleting the record for secondary userId
                $deleteLevelRelObj = new Gamification_Db_UserLevelRel('delete');
                $deleteLevelRelObj->set("userLevelRelId||$userLevelRelId2");
            }
        } catch (Exception $e) {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
        return true;
    }

    public static function mergeUserModerations($primaryUserId, $secondaryUserId, $publicationId, $clientId = 1) {
        if (empty($primaryUserId) || empty($secondaryUserId) || empty($publicationId))
            return false;
        $userModerationNo1 = 0;
        $userModerationNo2 = 0;
        $moderationRelObj1 = new Gamification_UserModerationRels("userId||$primaryUserId", "clientId||$clientId", "publicationId||$publicationId");
        $totalCount1 = $moderationRelObj1->getTotalCount();
        if ($totalCount1 > 0) {
            $userModerationNo1 = $moderationRelObj1->getModeratorNo();
            $userModerationRelId1 = $moderationRelObj1->getUserModerationRelId();
        }
        $moderationRelObj2 = new Gamification_UserModerationRels("userId||$secondaryUserId", "clientId||$clientId", "publicationId||$publicationId");
        $totalCount2 = $moderationRelObj2->getTotalCount();
        if ($totalCount2 > 0) {
            $userModerationNo2 = $moderationRelObj2->getModeratorNo();
            $userModerationRelId2 = $moderationRelObj2->getUserModerationRelId();
        }
        try {
            if ($userModerationNo1 != 0) {
                if ($userModerationNo2 > $userModerationNo1) {
                    // edit for primary userId
                    $editModerationRelObj = new Gamification_Db_UserModerationRel('edit');
                    $editModerationRelObj->set("userModerationRelId||$userModerationRelId1", "moderatorNo||$userModerationNo2");
                }
            }
            if ($userModerationNo2 != 0) {
                // deleting the record for secondary userId
                $deleteModerationRelObj = new Gamification_Db_UserModerationRel('delete');
                $deleteModerationRelObj->set("userModerationRelId||$userModerationRelId2");
            }
        } catch (Exception $e) {
            Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
            $log = Log4Php_Logger::getLogger('databaseAppender');
            $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
            return false;
        }
        return true;
    }

    public static function mergeUserCollectibles($primaryUserId, $secondaryUserId, $publicationId, $clientId = 1) {
        if (empty($primaryUserId) || empty($secondaryUserId) || empty($publicationId))
            return false;
        $collectibleRelObj = new Gamification_UserCollectibleRels("userId||$secondaryUserId", "clientId||$clientId", "publicationId||$publicationId", "count||Y");
        $totalCount = $collectibleRelObj->getTotalCount();
        if ($totalCount > 0) {
            $collectibleRelObj = new Gamification_UserCollectibleRels("userId||$secondaryUserId", "clientId||$clientId", "publicationId||$publicationId", "quantity||$totalCount");
            for ($i = 0; $i < $totalCount; $i++) {
                $gamificationUserCollectibleRelId = $collectibleRelObj->getGamificationUserCollectibleRelId();
                $collectibleId = $collectibleRelObj->getCollectibleId();
                try {
                    $collectibleRelObj2 = new Gamification_UserCollectibleRels("userId||$primaryUserId", "clientId||$clientId", "publicationId||$publicationId", "collectibleId||$collectibleId", "count||Y");
                    $collectibleExist = $collectibleRelObj2->getTotalCount();
                    if ($collectibleExist == 0) {
                        // edit the userId
                        $editCollectibleObj = new Gamification_Db_UserCollectibleRel('edit');
                        $editCollectibleObj->set("gamificationUserCollectibleRelId||$gamificationUserCollectibleRelId", "userId||$primaryUserId");
                    } else {
                        // delete the entry for secondary if exist for primary Id
                        $deleteCollectibleObj = new Gamification_Db_UserCollectibleRel('delete');
                        $deleteCollectibleObj->set("gamificationUserCollectibleRelId||$gamificationUserCollectibleRelId");
                    }
                } catch (Exception $e) {
                    Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                    $log = Log4Php_Logger::getLogger('databaseAppender');
                    $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                }
            }
        }
        return true;
    }

    public static function mergeUserBadges($primaryUserId, $secondaryUserId, $publicationId, $clientId = 1) {
        if (empty($primaryUserId) || empty($secondaryUserId) || empty($publicationId))
            return false;
        $badgeRelObj = new Gamification_UserBadgesRels("userId||$secondaryUserId", "clientId||$clientId", "publicationId||$publicationId", "count||Y");
        $totalCount = $badgeRelObj->getTotalCount();
        if ($totalCount > 0) {
            $badgeRelObj = new Gamification_UserBadgesRels("userId||$secondaryUserId", "clientId||$clientId", "publicationId||$publicationId", "quantity||$totalCount");
            for ($i = 0; $i < $totalCount; $i++) {
                $userBadgeRelId = $badgeRelObj->getUserBadgeRelId();
                $badgeId = $badgeRelObj->getBadgeId();
                try {
                    $badgeRelObj2 = new Gamification_UserBadgesRels("userId||$primaryUserId", "clientId||$clientId", "publicationId||$publicationId", "badgeId||$badgeId", "count||Y");
                    $badgeExist = $badgeRelObj2->getTotalCount();
                    if ($badgeExist == 0) {
                        // edit the userId
                        $editBadgeObj = new Gamification_Db_UserBadgesRel('edit');
                        $editBadgeObj->set("userBadgeRelId||$userBadgeRelId", "userId||$primaryUserId");
                    } else {
                        // delete the entry for secondary if exist for primary Id
                        $deleteBadgeObj = new Gamification_Db_UserBadgesRel('delete');
                        $deleteBadgeObj->set("userBadgeRelId||$userBadgeRelId");
                    }
                } catch (Exception $e) {
                    Log4Php_Logger::configure(APP_PATH . '/config/log4php.xml');
                    $log = Log4Php_Logger::getLogger('databaseAppender');
                    $log->forcedLog('', '', Log4Php_LoggerLevel::getLevelError(), $e->getMessage());
                }
            }
        }
        return true;
    }

    public static function addNewPublication($instacheckinPublicationId, $clientId) {
        try {
            $instaPublicationObj = new Instacheckin_Publication($instacheckinPublicationId);
            $publicationName = $instaPublicationObj->getPublicationName();
            $publicationDescription = $instaPublicationObj->getSiteDescription();
            $publicationUrl = $instaPublicationObj->getPublicationUrl();
//            $gaProfileName = str_replace("http://", '', $gaProfileName);
            $activityTypeObject = new Gamification_Db_Publication('add');
            $activityTypeObject->set("publicationId||$instacheckinPublicationId", "clientId||$clientId", "publicationUrl||$publicationUrl", "publicationName||$publicationName", "publicationDescription||$publicationDescription", "publicationStartTime||Now()", "createdUserId||$clientId", "publicationActiveStatus||Y", "buyerId||$clientId");
            $newClientId = $clientId;
            $oldClientId = 1;
            self::copyPublicationsData('509', $instacheckinPublicationId, $oldClientId, $newClientId);
        } catch (Exception $e) {
            Instapress_Core_Helper::describe($e->getMessage());
            Instapress_Core_Helper::describe($e->getTraceAsString());
        }
    }

    /* copyPublicationData
      //copy one publication activity,levelType,badge,colletible, two another publications

     */

    public static function copyPublicationsData($oldPublicationId, $newPublicationId, $oldClientId, $newClientId) {
        if (empty($oldPublicationId) || !is_numeric($oldPublicationId)) {
            throw new Exception('oldPublicationId should be a natural number.or oldPublicationId should not be empty');
        }
        if (empty($newPublicationId) || !is_numeric($newPublicationId)) {
            throw new Exception('newPublicationId should be a natural number.or newPublicationId should not be empty');
        }
        if (empty($oldClientId) || !is_numeric($oldClientId)) {
            throw new Exception('clientId should be a natural number.or clientId should not be empty');
        }
        $successMsg = "";
        // copy all activityType
        $actityTypeObj = new Gamification_ActivityTypes("publicationId||$oldPublicationId", "clientId||$oldClientId", "count||Y");
        $totalActivityType = $actityTypeObj->getTotalCount();
        if ($totalActivityType > 0) {
            $actityTypeObj = new Gamification_ActivityTypes("publicationId||$oldPublicationId", "clientId||$oldClientId", "quantity||$totalActivityType", "sortColumn||createdTime", "sortOrder||DESC");
            $columnNames = $actityTypeObj->getColumnNames();
            for ($i = 0; $i < $totalActivityType; $i++) {
                $addString = "";
                foreach ($columnNames as $column) {
                    $value = $actityTypeObj->get($column, $i);
                    if ($column == 'publicationId') {
                        $value = $newPublicationId;
                    } else if ($column == 'clientId') {
                        $value = $newClientId;
                    } else if ($column == 'activityTypeId' || $column == 'createdTime') {
                        continue;
                    }
                    $value = str_replace("'", '"', $value);
                    $addString = $addString . "'" . $column . "||" . $value . "',";
                }
                $addString = rtrim($addString, ",");
                try {
                    // checking the existance
                    $checkObj = new Gamification_Db_ActivityType();
                    eval('$checkObj->set( ' . $addString . ' );');
                    if ($checkObj->getTotalCount() == 0) {
                        $addObj = new Gamification_Db_ActivityType('add');
                        eval('$addObj->set( ' . $addString . ' );');
                    }
                } catch (Exception $e) {
                    describe($e->getMessage() . "\n" . $e->getTraceAsString());
                }
            }
            $successMsg = $successMsg . "ActivityTypes created Successfully\n";
        }
// copy all badges
        $badgeObj = new Gamification_Badges("publicationId||$oldPublicationId", "clientId||$oldClientId", "count||Y");
        $totalBadge = $badgeObj->getTotalCount();
        if ($totalBadge > 0) {
            $badgeObj = new Gamification_Badges("publicationId||$oldPublicationId", "clientId||$oldClientId", "quantity||$totalBadge", "sortColumn||createdTime", "sortOrder||ASC");
            $columnNames = $badgeObj->getColumnNames();
            for ($i = 0; $i < $totalBadge; $i++) {
                $addString = "";
                foreach ($columnNames as $column) {
                    $value = $badgeObj->get($column, $i);
                    if ($column == 'publicationId') {
                        $value = $newPublicationId;
                    } else if ($column == 'clientId') {
                        $value = $newClientId;
                    } else if ($column == 'badgeId' || $column == 'createdTime') {
                        continue;
                    }
                    $addString = $addString . "'" . $column . "||" . $value . "',";
                }
                $addString = rtrim($addString, ",");
                try {
                    // checking the existance
                    $checkObj = new Gamification_Db_Badge();
                    eval('$checkObj->set( ' . $addString . ' );');
                    if ($checkObj->getTotalCount() == 0) {
                        $addObj = new Gamification_Db_Badge('add');
                        eval('$addObj->set( ' . $addString . ' );');
                    }
                } catch (Exception $e) {
                    describe($e->getMessage() . "\n" . $e->getTraceAsString());
                }
            }
            $successMsg = $successMsg . "Badges created Successfully\n";
        }
// copy all collectible
        $collectibleObj = new Gamification_Collectibles("publicationId||$oldPublicationId", "clientId||$oldClientId", "count||Y");
        $totalCollectible = $collectibleObj->getTotalCount();
        if ($totalCollectible > 0) {
            $collectibleObj = new Gamification_Collectibles("publicationId||$oldPublicationId", "clientId||$oldClientId", "quantity||$totalCollectible", "sortColumn||createdTime", "sortOrder||ASC");
            $columnNames = $collectibleObj->getColumnNames();
            for ($i = 0; $i < $totalCollectible; $i++) {
                $addString = "";
                foreach ($columnNames as $column) {
                    $value = $collectibleObj->get($column, $i);
                    if ($column == 'publicationId') {
                        $value = $newPublicationId;
                    } else if ($column == 'clientId') {
                        $value = $newClientId;
                    } else if ($column == 'collectibleId' || $column == 'createdTime') { // skip the primary key and createdTime
                        continue;
                    }
                    $addString = $addString . "'" . $column . "||" . $value . "',";
                }
                $addString = rtrim($addString, ",");
                try {
                    // checking the existance
                    $checkObj = new Gamification_Db_Collectible();
                    eval('$checkObj->set( ' . $addString . ' );');
                    if ($checkObj->getTotalCount() == 0) {
                        $addObj = new Gamification_Db_Collectible('add');
                        eval('$addObj->set( ' . $addString . ' );');
                    }
                } catch (Exception $e) {
                    describe($e->getMessage() . "\n" . $e->getTraceAsString());
                }
            }
            $successMsg = $successMsg . "Collectibles created Successfully\n";
        }
// copy all moderator_type
        $moderatorObj = new Gamification_ModeratorTypes("publicationId||$oldPublicationId", "clientId||$oldClientId", "count||Y");
        $totalModeratorType = $moderatorObj->getTotalCount();
        if ($totalModeratorType > 0) {
            $moderatorObj = new Gamification_ModeratorTypes("publicationId||$oldPublicationId", "clientId||$oldClientId", "quantity||$totalModeratorType", "sortColumn||createdTime", "sortOrder||ASC");
            $columnNames = $moderatorObj->getColumnNames();
            for ($i = 0; $i < $totalModeratorType; $i++) {
                $addString = "";
                foreach ($columnNames as $column) {
                    $value = $moderatorObj->get($column, $i);
                    if ($column == 'publicationId') {
                        $value = $newPublicationId;
                    } else if ($column == 'clientId') {
                        $value = $newClientId;
                    } else if ($column == 'moderatorTypeId' || $column == 'createdTime') { // skip the primary key and createdTime
                        continue;
                    }
                    $addString = $addString . "'" . $column . "||" . $value . "',";
                }
                $addString = rtrim($addString, ",");
                try {
                    // checking the existance
                    $checkObj = new Gamification_Db_ModeratorType();
                    eval('$checkObj->set( ' . $addString . ' );');
                    if ($checkObj->getTotalCount() == 0) {
                        $addObj = new Gamification_Db_ModeratorType('add');
                        eval('$addObj->set( ' . $addString . ' );');
                    }
                } catch (Exception $e) {
                    describe($e->getMessage() . "\n" . $e->getTraceAsString());
                }
            }
            $successMsg = $successMsg . "ModeratorTypes created Successfully\n";
        }
// copy all level_type
        $levelObj = new Gamification_UserLevelTypes("publicationId||$oldPublicationId", "clientId||$oldClientId", "count||Y");
        $totalUserLevelType = $levelObj->getTotalCount();
        if ($totalUserLevelType > 0) {
            $levelObj = new Gamification_UserLevelTypes("publicationId||$oldPublicationId", "clientId||$oldClientId", "quantity||$totalUserLevelType", "sortColumn||createdTime", "sortOrder||ASC");
            $columnNames = $levelObj->getColumnNames();
            for ($i = 0; $i < $totalUserLevelType; $i++) {
                $addString = "";
                foreach ($columnNames as $column) {
                    $value = $levelObj->get($column, $i);
                    if ($column == 'publicationId') {
                        $value = $newPublicationId;
                    } else if ($column == 'clientId') {
                        $value = $newClientId;
                    } else if ($column == 'userLevelTypeId' || $column == 'createdTime') {   // skip the primary key and createdTime
                        continue;
                    }
                    $addString = $addString . "'" . $column . "||" . $value . "',";
                }
                $addString = rtrim($addString, ",");
                try {
                    // checking the existance
                    $checkObj = new Gamification_Db_UserLevelType();
                    eval('$checkObj->set( ' . $addString . ' );');
                    if ($checkObj->getTotalCount() == 0) {
                        $addObj = new Gamification_Db_UserLevelType('add');
                        eval('$addObj->set( ' . $addString . ' );');
                    }
                } catch (Exception $e) {
                    describe($e->getMessage() . "\n" . $e->getTraceAsString());
                }
            }
            $successMsg = $successMsg . "UserLevelTypes created Successfully\n";
        }
        $pubObj = new Instacheckin_Publication($oldPublicationId);
        $firstPublication = $pubObj->getPublicationName();
        $pubObj = new Instacheckin_Publication($newPublicationId);
        $secondPublication = $pubObj->getPublicationName();
        $successMsg = "Data copied from " . $firstPublication . " to " . $secondPublication;

        return $successMsg;
    }

}

?>
