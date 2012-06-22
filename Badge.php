<?php

/**
 * @author Ashish Kumar
 * @desc Singular Model class for GamificationBadge DB class
 */
class Gamification_Badge extends Gamification_AbstractSingular {

    protected $_dbClass = 'Badge';
    protected $_foreignKey = 'badgeId';

    /**
     * @author Ashish Kumar 20111216$.
     * @desc Method to get the list of user's information who got selected badge.
     * @return Array containg userName,userImage,userId
     */
    public function getBadgeData() {
        try {
            $record = array();
            $badgeId = $this->getBadgeId();
            $obj = new Gamification_UserBadgesRels("badgeId||$badgeId");
            $total = $obj->getTotalCount();
            if ($total > 0) {
                $obj = new Gamification_UserBadgesRels("badgeId||$badgeId", "quantity||$total");
                for ($i = 0; $i < $total; $i++) {
                    $userId = $obj->getUserId($i);
                    $userObj = new Instacheckin_User($userId);
                    $firstName = $userObj->getUserFirstName();
                    $lastName = $userObj->getUserLastName();
                    $record[$i]['userName'] = $firstName . " " . $lastName;
                    $record[$i]['userImage'] = $userObj->getUserImage();
                    $record[$i]['userId'] = $userId;
                }
            }
        } catch (Exception $e) {
//            Instapress_Core_Helper::describe($e->getMessage());
//            Instapress_Core_Helper::describe($e->getTraceAsString());
        }
        return $record;
    }
}
?>
