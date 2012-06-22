<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_activity_type
 */
class Gamification_Db_ActivityType extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_activity_type';
    protected $_clauseColumnNames = array('activityTypeId', 'publicationId', 'activityName', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'activityText', 'activityPoints', 'userEligibilityPoints', 'activityPointsType', 'pointIndicator', 'activityTypeFrom', 'createdBy', 'createdTime', 'clientId');
    protected $_updateColumnNames = array('activityName', 'publicationId', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'activityPoints', 'activityText', 'activityPointsType', 'pointIndicator','userEligibilityPoints', 'activityTypeFrom', 'createdBy', 'createdTime', 'clientId');
    protected $_sortColumnNames = array('activityTypeId', 'publicationId', 'activityName', 'activityOwnership', 'activityObject', 'activityVerb', 'activityCommonObject', 'activityCommonVerb', 'activityText', 'activityPoints', 'activityPointsType','userEligibilityPoints', 'pointIndicator', 'activityTypeFrom', 'createdBy', 'createdTime', 'clientId');
    protected $_foreignKey = 'activityTypeId';
    protected $_expandableTables = array();

    function add() {

        $activityName = isset($this->_arrayUpdatedData['activityName']) ? trim($this->_arrayUpdatedData['activityName']) : '';
        if (empty($activityName)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityName"));
        }
        $activityOwnership = isset($this->_arrayUpdatedData['activityOwnership']) ? trim($this->_arrayUpdatedData['activityOwnership']) : '';
        if (empty($activityOwnership)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityOwnership"));
        }
        $activityObject = isset($this->_arrayUpdatedData['activityObject']) ? trim($this->_arrayUpdatedData['activityObject']) : '';
        if (empty($activityObject)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityObject"));
        }
        $activityVerb = isset($this->_arrayUpdatedData['activityVerb']) ? trim($this->_arrayUpdatedData['activityVerb']) : '';
        if (empty($activityVerb)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityVerb"));
        }
        $activityCommonObject = isset($this->_arrayUpdatedData['activityCommonObject']) ? trim($this->_arrayUpdatedData['activityCommonObject']) : '';
        if (empty($activityCommonObject)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityCommonObject"));
        }
        $activityCommonVerb = isset($this->_arrayUpdatedData['activityCommonVerb']) ? trim($this->_arrayUpdatedData['activityCommonVerb']) : '';
        if (empty($activityCommonVerb)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityCommonVerb"));
        }
        $activityText = isset($this->_arrayUpdatedData['activityText']) ? trim($this->_arrayUpdatedData['activityText']) : '';
        if (empty($activityText)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityText "));
        }
        $activityPoints = isset($this->_arrayUpdatedData['activityPoints']) ? trim($this->_arrayUpdatedData['activityPoints']) : '';
        if (!is_numeric($activityPoints)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityPoints and it should be numeric"));
        }
        $activityPointsType = isset($this->_arrayUpdatedData['activityPointsType']) ? trim($this->_arrayUpdatedData['activityPointsType']) : '+ve';
        if (empty($activityPointsType)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityPointsType"));
        }
        $userEligibilityPoints = isset($this->_arrayUpdatedData['userEligibilityPoints']) ? trim($this->_arrayUpdatedData['userEligibilityPoints']) : '0';
        if (!is_numeric($userEligibilityPoints)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter userEligibilityPoints and it should be numeric"));
        }
        $pointIndicator = isset($this->_arrayUpdatedData['pointIndicator']) ? trim($this->_arrayUpdatedData['pointIndicator']) : 'self';
        if (empty($pointIndicator)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter pointIndicator"));
        }
        $activityTypeFrom = isset($this->_arrayUpdatedData['activityTypeFrom']) ? trim($this->_arrayUpdatedData['activityTypeFrom']) : 'user';
        if (empty($activityTypeFrom)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityTypeFrom"));
        }
        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : '';
        if (empty($createdBy) || !is_numeric($createdBy)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter createdBy and it should be numeric"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter clientId and it should be numeric"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter publicationId and it should be numeric"));
        }
        $query = array();
        $query['activityName'] = $activityName;
        $query['activityOwnership'] = $activityOwnership;
        $query['activityObject'] = $activityObject;
        $query['activityVerb'] = $activityVerb;
        $query['activityCommonObject'] = $activityCommonObject;
        $query['activityCommonVerb'] = $activityCommonVerb;
        $query['activityText'] = $activityText;
        $query['activityPoints'] = $activityPoints;
        $query['activityPointsType'] = $activityPointsType;
        $query['userEligibilityPoints']=$userEligibilityPoints;
        $query['pointIndicator'] = $pointIndicator;
        $query['activityTypeFrom'] = $activityTypeFrom;
        $query['createdBy'] = $createdBy;
        $query['clientId'] = $clientId;
        $query['publicationId'] = $publicationId;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $activityTypeId = isset($this->_arrayUpdatedData['activityTypeId']) ? trim($this->_arrayUpdatedData['activityTypeId']) : "";
        if (empty($activityTypeId) || !is_numeric($activityTypeId)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityTypeId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " activityTypeId = '$activityTypeId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $activityTypeId = isset($this->_arrayUpdatedData['activityTypeId']) ? trim($this->_arrayUpdatedData['activityTypeId']) : "";
        if (empty($activityTypeId) || !is_numeric($activityTypeId)) {
            throw new Exception(gettext("gamification_activity_type") . gettext(": must enter activityTypeId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " activityTypeId = '$activityTypeId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
