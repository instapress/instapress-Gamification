<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_user_badge_rel
 */
class Gamification_Db_UserBadgesRel extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_user_badge_rel';
    protected $_clauseColumnNames = array('userBadgeRelId','userId', 'badgeId', 'badgeLevelType', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'publicationId', 'higestLevel', 'createdTime', 'clientId');
    protected $_updateColumnNames = array('userBadgeRelId','userId', 'badgeLevelType', 'badgeId', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'publicationId', 'higestLevel', 'createdTime', 'clientId');
    protected $_sortColumnNames = array('userBadgeRelId','userId', 'badgeId', 'badgeLevelType', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'publicationId', 'higestLevel', 'createdTime', 'clientId');
    protected $_foreignKey = 'userBadgeRelId';
    protected $_expandableTables = array();

    function add() {

        $userId = isset($this->_arrayUpdatedData['userId']) ? trim($this->_arrayUpdatedData['userId']) : '';
        if (empty($userId) || !is_numeric($userId)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter userId and it should be numeric"));
        }
        $badgeId = isset($this->_arrayUpdatedData['badgeId']) ? trim($this->_arrayUpdatedData['badgeId']) : '';
        if (empty($badgeId) || !is_numeric($badgeId)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter badgeId and it should be numeric"));
        }
        $activityOwnership = isset($this->_arrayUpdatedData['activityOwnership']) ? trim($this->_arrayUpdatedData['activityOwnership']) : '';
        if (empty($activityOwnership)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter activityOwnership"));
        }
        $activityObject = isset($this->_arrayUpdatedData['activityObject']) ? trim($this->_arrayUpdatedData['activityObject']) : '';
        if (empty($activityObject)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter activityObject"));
        }
        $activityVerb = isset($this->_arrayUpdatedData['activityVerb']) ? trim($this->_arrayUpdatedData['activityVerb']) : '';
        if (empty($activityVerb)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter activityVerb"));
        }
        $activityCommonObject = isset($this->_arrayUpdatedData['activityCommonObject']) ? trim($this->_arrayUpdatedData['activityCommonObject']) : '';
        if (empty($activityCommonObject)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter activityCommonObject"));
        }
        $activityCommonVerb = isset($this->_arrayUpdatedData['activityCommonVerb']) ? trim($this->_arrayUpdatedData['activityCommonVerb']) : '';
        if (empty($activityCommonVerb)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter activityCommonVerb"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter publicationId and it should be numeric"));
        }
        $badgeLevelType = isset($this->_arrayUpdatedData['badgeLevelType']) ? trim($this->_arrayUpdatedData['badgeLevelType']) : '';
        if (empty($badgeLevelType)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter badgeLevelType"));
        }
        $higestLevel = isset($this->_arrayUpdatedData['higestLevel']) ? trim($this->_arrayUpdatedData['higestLevel']) : '';
        if (empty($higestLevel)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter higestLevel"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter clientId and it should be numeric"));
        }
        $query = array();
        $query['userId'] = $userId;
        $query['badgeId'] = $badgeId;
        $query['activityOwnership'] = $activityOwnership;
        $query['activityObject'] = $activityObject;
        $query['activityVerb'] = $activityVerb;
        $query['activityCommonObject'] = $activityCommonObject;
        $query['activityCommonVerb'] = $activityCommonVerb;
        $query['publicationId'] = $publicationId;
        $query['badgeLevelType'] = $badgeLevelType;
        $query['higestLevel'] = $higestLevel;
        $query['clientId'] = $clientId;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $userBadgeRelId = isset($this->_arrayUpdatedData['userBadgeRelId']) ? trim($this->_arrayUpdatedData['userBadgeRelId']) : "";
        if (empty($userBadgeRelId) || !is_numeric($userBadgeRelId)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter userBadgeRelId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " userBadgeRelId = '$userBadgeRelId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $userBadgeRelId = isset($this->_arrayUpdatedData['userBadgeRelId']) ? trim($this->_arrayUpdatedData['userBadgeRelId']) : "";
        if (empty($userBadgeRelId) || !is_numeric($userBadgeRelId)) {
            throw new Exception(gettext("gamification_user_badge_rel") . gettext("must enter userBadgeRelId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " userBadgeRelId = '$userBadgeRelId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
