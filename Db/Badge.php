<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_badge
 */
class Gamification_Db_Badge extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_badge';
    protected $_clauseColumnNames = array('badgeId', 'publicationId', 'badgeLevelType', 'badgeName', 'badgeDesc', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'clientId', 'createdBy', 'createdTime', 'multipleBadges', 'activityCount', 'imageUrl');
    protected $_updateColumnNames = array('badgeId','badgeName', 'publicationId', 'badgeLevelType', 'badgeDesc', 'activityCommonObject', 'activityCommonVerb', 'activityOwnership', 'activityObject', 'activityVerb', 'multipleBadges', 'createdBy', 'createdTime', 'clientId', 'activityCount', 'imageUrl');
    protected $_sortColumnNames = array('badgeId','badgeName', 'publicationId', 'badgeLevelType', 'badgeDesc', 'activityCommonObject', 'activityCommonVerb', 'activityOwnership', 'activityObject', 'activityVerb', 'multipleBadges', 'createdBy', 'createdTime', 'clientId', 'activityCount', 'imageUrl');
    protected $_foreignKey = 'badgeId';
    protected $_expandableTables = array();

    function add() {

        $badgeName = isset($this->_arrayUpdatedData['badgeName']) ? trim($this->_arrayUpdatedData['badgeName']) : '';
        if (empty($badgeName)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter badgeName"));
        }
        $badgeLevelType = isset($this->_arrayUpdatedData['badgeLevelType']) ? trim($this->_arrayUpdatedData['badgeLevelType']) : '';
        if (empty($badgeLevelType)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter badgeLevelType"));
        }
        $badgeDesc = isset($this->_arrayUpdatedData['badgeDesc']) ? trim($this->_arrayUpdatedData['badgeDesc']) : '';
        if (empty($badgeDesc)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter badgeDesc"));
        }
        $activityCommonObject = isset($this->_arrayUpdatedData['activityCommonObject']) ? trim($this->_arrayUpdatedData['activityCommonObject']) : '';
        if (empty($activityCommonObject)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter activityCommonObject"));
        }
        $activityCommonVerb = isset($this->_arrayUpdatedData['activityCommonVerb']) ? trim($this->_arrayUpdatedData['activityCommonVerb']) : '';
        if (empty($activityCommonVerb)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter activityCommonVerb"));
        }
        $activityOwnership = isset($this->_arrayUpdatedData['activityOwnership']) ? trim($this->_arrayUpdatedData['activityOwnership']) : '';
        if (empty($activityOwnership)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter activityOwnership"));
        }
        $activityObject = isset($this->_arrayUpdatedData['activityObject']) ? trim($this->_arrayUpdatedData['activityObject']) : '';
        if (empty($activityObject)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter activityObject"));
        }
        $activityVerb = isset($this->_arrayUpdatedData['activityVerb']) ? trim($this->_arrayUpdatedData['activityVerb']) : '';
        if (empty($activityVerb)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter activityVerb"));
        }
        $multipleBadges = isset($this->_arrayUpdatedData['multipleBadges']) ? trim($this->_arrayUpdatedData['multipleBadges']) : 'NO';
        if (empty($multipleBadges)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter multipleBadges"));
        }
        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : '';
        if (empty($createdBy) || !is_numeric($createdBy)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter createdBy and it should be numeric"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter clientId and it should be numeric"));
        }
        $activityCount = isset($this->_arrayUpdatedData['activityCount']) ? trim($this->_arrayUpdatedData['activityCount']) : '';
        if (empty($activityCount) || !is_numeric($activityCount)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter activityCountand it should be numeric"));
        }
        $imageUrl = isset($this->_arrayUpdatedData['imageUrl']) ? trim($this->_arrayUpdatedData['imageUrl']) : '';
//        if (empty($imageUrl)) {
//            throw new Exception(gettext("gamification_badge") . gettext("must enter imageUrl"));
//        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter publicationId and it should be numeric"));
        }
        $query = array();
        $query['publicationId'] = $publicationId;
        $query['badgeLevelType'] = $badgeLevelType;
        $query['badgeName'] = $badgeName;
        $query['badgeDesc'] = $badgeDesc;
        $query['activityOwnership'] = $activityOwnership;
        $query['activityObject'] = $activityObject;
        $query['activityVerb'] = $activityVerb;
        $query['activityCommonObject'] = $activityCommonObject;
        $query['activityCommonVerb'] = $activityCommonVerb;
        $query['clientId'] = $clientId;
        $query['createdBy'] = $createdBy;
        $query['multipleBadges'] = $multipleBadges;
        $query['activityCount'] = $activityCount;
        $query['imageUrl'] = $imageUrl;


        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
      $badgeId = isset($this->_arrayUpdatedData['badgeId']) ? trim($this->_arrayUpdatedData['badgeId']) : "";
        if (empty($badgeId) || !is_numeric($badgeId)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter badgeId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " badgeId = '$badgeId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $badgeId = isset($this->_arrayUpdatedData['badgeId']) ? trim($this->_arrayUpdatedData['badgeId']) : "";
        if (empty($badgeId) || !is_numeric($badgeId)) {
            throw new Exception(gettext("gamification_badge") . gettext("must enter badgeId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " badgeId = '$badgeId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
