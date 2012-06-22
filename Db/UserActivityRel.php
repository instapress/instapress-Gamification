<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_user_activity_rel
 */
class Gamification_Db_UserActivityRel extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_user_activity_rel';
    protected $_clauseColumnNames = array('userActivityId', 'userId', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'activityPoints', 'activityPointsType', 'clientId', 'publicationId', 'contentId', 'contentType', 'createdTime','activityText','comman');
    protected $_updateColumnNames = array('userActivityId','userId', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'activityPointsType', 'activityPoints', 'clientId', 'publicationId', 'contentId', 'createdTime', 'contentType','activityText','comman');
    protected $_sortColumnNames = array('userActivityId', 'userId', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'activityPoints', 'activityPointsType', 'clientId', 'publicationId', 'contentId', 'createdTime', 'contentType','activityText','comman');
    protected $_foreignKey = 'userActivityId';
    protected $_expandableTables = array();

    function add() {

        $userId = isset($this->_arrayUpdatedData['userId']) ? trim($this->_arrayUpdatedData['userId']) : '';
        if (empty($userId) || !is_numeric($userId)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter userId and it should be numeric"));
        }

        $activityPoints = isset($this->_arrayUpdatedData['activityPoints']) ? trim($this->_arrayUpdatedData['activityPoints']) : 0;
        if (!is_numeric($activityPoints)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter activityPoints and it should be numeric"));
        }
        $activityPointsType = isset($this->_arrayUpdatedData['activityPointsType']) ? trim($this->_arrayUpdatedData['activityPointsType']) : '';
        if (empty($activityPointsType)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter activityPointsType"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter clientId and it should be numeric"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter publicationId and it should be numeric"));
        }
        $contentId = isset($this->_arrayUpdatedData['contentId']) ? trim($this->_arrayUpdatedData['contentId']) : 0;
//        if (empty($contentId) || !is_numeric($contentId)) {
//            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter contentId and it should be numeric"));
//        }
        $contentType = isset($this->_arrayUpdatedData['contentType']) ? trim($this->_arrayUpdatedData['contentType']) : 'none';
        if (empty($contentType)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter contentType "));
        }
        $activityOwnership = isset($this->_arrayUpdatedData['activityOwnership']) ? trim($this->_arrayUpdatedData['activityOwnership']) : '';
        if (empty($activityOwnership)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter activityOwnership"));
        }
        $activityObject = isset($this->_arrayUpdatedData['activityObject']) ? trim($this->_arrayUpdatedData['activityObject']) : '';
        if (empty($activityObject)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter activityObject"));
        }
        $activityVerb = isset($this->_arrayUpdatedData['activityVerb']) ? trim($this->_arrayUpdatedData['activityVerb']) : '';
        if (empty($activityVerb)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter activityVerb"));
        }
        $activityCommonObject = isset($this->_arrayUpdatedData['activityCommonObject']) ? trim($this->_arrayUpdatedData['activityCommonObject']) : '';
        if (empty($activityCommonObject)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter activityCommonObject"));
        }
        $activityCommonVerb = isset($this->_arrayUpdatedData['activityCommonVerb']) ? trim($this->_arrayUpdatedData['activityCommonVerb']) : '';
        if (empty($activityCommonVerb)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter activityCommonVerb"));
        }
        $activityText = isset($this->_arrayUpdatedData['activityText']) ? trim($this->_arrayUpdatedData['activityText']) : '';
        if (empty($activityText)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityText "));
        }
        if ($activityCommonObject == 'YES' && $activityCommonVerb == 'YES') {
            $comman = 'YES';
        } else {
            $comman = 'NO';
        }

        $query = array();
        $query['userId'] = $userId;
        $query['activityOwnership'] = $activityOwnership;
        $query['activityObject'] = $activityObject;
        $query['activityVerb'] = $activityVerb;
        $query['activityCommonObject'] = $activityCommonObject;
        $query['activityCommonVerb'] = $activityCommonVerb;
        $query['activityPoints'] = $activityPoints;
        $query['activityPointsType'] = $activityPointsType;
        $query['activityText'] = $activityText;
        $query['comman'] = $comman;
        $query['clientId'] = $clientId;
        $query['publicationId'] = $publicationId;
        $query['contentId'] = $contentId;
        $query['contentType'] = $contentType;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $userActivityId = isset($this->_arrayUpdatedData['userActivityId']) ? trim($this->_arrayUpdatedData['userActivityId']) : "";
        if (empty($userActivityId) || !is_numeric($userActivityId)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter userActivityId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " userActivityId = '$userActivityId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $userActivityId = isset($this->_arrayUpdatedData['userActivityId']) ? trim($this->_arrayUpdatedData['userActivityId']) : "";
        if (empty($userActivityId) || !is_numeric($userActivityId)) {
            throw new Exception(gettext("gamification_user_activity_rel") . gettext("must enter userActivityId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " userActivityId = '$userActivityId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
