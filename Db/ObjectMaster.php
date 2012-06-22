<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_object_master
 */
class Gamification_Db_ObjectMaster extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_object_master';
    protected $_clauseColumnNames = array('gamificationObjectMasterId', 'gamificationObjectName', 'commonObject', 'clientId', 'createdTime', 'createdBy');
    protected $_updateColumnNames = array('gamificationObjectName', 'commonObject', 'clientId', 'createdTime', 'createdBy');
    protected $_sortColumnNames = array('gamificationObjectMasterId', 'gamificationObjectName', 'commonObject', 'clientId', 'createdTime', 'createdBy');
    protected $_foreignKey = 'gamificationObjectMasterId';
    protected $_expandableTables = array();

    function add() {

        $gamificationObjectName = isset($this->_arrayUpdatedData['gamificationObjectName']) ? trim($this->_arrayUpdatedData['gamificationObjectName']) : '';
        if (empty($gamificationObjectName)) {
            throw new Exception(gettext("gamification_object_master") . gettext("must enter gamificationObjectName"));
        }
        $commonObject = isset($this->_arrayUpdatedData['commonObject']) ? trim($this->_arrayUpdatedData['commonObject']) : 'YES';
        if (empty($commonObject)) {
            throw new Exception(gettext("gamification_object_master") . gettext("must enter commonObject"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_object_master") . gettext("must enter clientId and it should be numeric"));
        }
        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : '';
        if (empty($createdBy) || !is_numeric($createdBy)) {
            throw new Exception(gettext("gamification_object_master") . gettext("must enter createdBy and it should be numeric"));
        }

        $query = array();

        $query['gamificationObjectName'] = $gamificationObjectName;
        $query['commonObject'] = $commonObject;
        $query['clientId'] = $clientId;
        $query['createdBy'] = $createdBy;


        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $gamificationObjectMasterId = isset($this->_arrayUpdatedData['gamificationObjectMasterId']) ? trim($this->_arrayUpdatedData['gamificationObjectMasterId']) : "";
        if (empty($gamificationObjectMasterId) || !is_numeric($gamificationObjectMasterId)) {
            throw new Exception(gettext("gamification_object_master") . gettext("must enter gamificationObjectMasterId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " gamificationObjectMasterId = '$gamificationObjectMasterId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $gamificationObjectMasterId = isset($this->_arrayUpdatedData['gamificationObjectMasterId']) ? trim($this->_arrayUpdatedData['gamificationObjectMasterId']) : "";
        if (empty($gamificationObjectMasterId) || !is_numeric($gamificationObjectMasterId)) {
            throw new Exception(gettext("gamification_object_master") . gettext("must enter gamificationObjectMasterId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " gamificationObjectMasterId = '$gamificationObjectMasterId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>