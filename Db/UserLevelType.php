<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_user_level_type
 */
class Gamification_Db_UserLevelType extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_user_level_type';
    protected $_clauseColumnNames = array('userLevelTypeId','userLevelNo', 'publicationId', 'userLevelName', 'unlockingPoints', 'imageUrl', 'createdBy', 'createdTime', 'clientId');
    protected $_updateColumnNames = array('userLevelTypeId','userLevelNo','unlockingPoints', 'publicationId', 'userLevelName', 'imageUrl', 'createdBy', 'createdTime', 'clientId');
    protected $_sortColumnNames = array('userLevelTypeId','userLevelNo', 'publicationId', 'userLevelName', 'unlockingPoints', 'createdBy', 'createdTime', 'clientId');
    protected $_foreignKey = 'userLevelTypeId';
    protected $_expandableTables = array();

    function add() {

        $userLevelName = isset($this->_arrayUpdatedData['userLevelName']) ? trim($this->_arrayUpdatedData['userLevelName']) : '';
        if (empty($userLevelName)) {
            throw new Exception(gettext("gamification_user_level_type") . gettext("must enter userLevelName"));
        }
        $imageUrl = isset($this->_arrayUpdatedData['imageUrl']) ? trim($this->_arrayUpdatedData['imageUrl']) : '';
        $unlockingPoints = isset($this->_arrayUpdatedData['unlockingPoints']) ? trim($this->_arrayUpdatedData['unlockingPoints']) : '';
//        if (empty($unlockingPoints) || !is_numeric($unlockingPoints)) {
//            throw new Exception(gettext("gamification_user_level_type") . gettext("must enter unlockingPoints and it should be numeric"));
//        }
        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : '';
        if (empty($createdBy)) {
            throw new Exception(gettext("gamification_user_level_type") . gettext("must enter createdBy"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '1';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_user_level_type") . gettext("must enter clientId and it should be numeric"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_user_level_type") . gettext("must enter publicationId and it should be numeric"));
        }
        $userLevelNo = isset($this->_arrayUpdatedData['userLevelNo']) ? trim($this->_arrayUpdatedData['userLevelNo']) : '';
        if (empty($userLevelNo) || !is_numeric($userLevelNo)) {
            throw new Exception(gettext("gamification_user_level_type") . gettext("must enter userLevelNo and it should be numeric"));
        }
        $query = array();
        $query['imageUrl'] = $imageUrl;
        $query['userLevelName'] = $userLevelName;
        $query['unlockingPoints'] = $unlockingPoints;
        $query['createdBy'] = $createdBy;
        $query['clientId'] = $clientId;
        $query['publicationId'] = $publicationId;
        $query['userLevelNo'] = $userLevelNo;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $userLevelTypeId = isset($this->_arrayUpdatedData['userLevelTypeId']) ? trim($this->_arrayUpdatedData['userLevelTypeId']) : "";
        if (empty($userLevelTypeId) || !is_numeric($userLevelTypeId)) {
            throw new Exception(gettext("gamification_user_level_type") . gettext("must enter userLevelTypeId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " userLevelTypeId = '$userLevelTypeId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $userLevelTypeId = isset($this->_arrayUpdatedData['userLevelTypeId']) ? trim($this->_arrayUpdatedData['userLevelTypeId']) : "";
        if (empty($userLevelTypeId) || !is_numeric($userLevelTypeId)) {
            throw new Exception(gettext("gamification_user_level_type") . gettext("must enter userLevelTypeId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " userLevelTypeId = '$userLevelTypeId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
