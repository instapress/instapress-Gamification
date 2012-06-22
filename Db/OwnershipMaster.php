<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_ownership_master
 */
class Gamification_Db_OwnershipMaster extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_ownership_master';
    protected $_clauseColumnNames = array('gamificationOwnershipMasterId', 'gamificationOwnershipName', 'clientId', 'createdTime', 'createdBy');
    protected $_updateColumnNames = array('gamificationOwnershipName', 'clientId', 'createdTime', 'createdBy');
    protected $_sortColumnNames = array('gamificationOwnershipMasterId', 'gamificationOwnershipName', 'clientId', 'createdTime', 'createdBy');
    protected $_foreignKey = 'gamificationOwnershipMasterId';
    protected $_expandableTables = array();

    function add() {

        $gamificationOwnershipName = isset($this->_arrayUpdatedData['gamificationOwnershipName']) ? trim($this->_arrayUpdatedData['gamificationOwnershipName']) : '';
        if (empty($gamificationOwnershipName)) {
            throw new Exception(gettext("gamification_ownership_master") . gettext("must enter gamificationOwnershipName"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_ownership_master") . gettext("must enter clientId and it should be numeric"));
        }
        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : '';
        if (empty($createdBy) || !is_numeric($createdBy)) {
            throw new Exception(gettext("gamification_ownership_master") . gettext("must enter createdBy and it should be numeric"));
        }

        $query = array();

        $query['gamificationOwnershipName'] = $gamificationOwnershipName;
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
        $gamificationOwnershipMasterId = isset($this->_arrayUpdatedData['gamificationOwnershipMasterId']) ? trim($this->_arrayUpdatedData['gamificationOwnershipMasterId']) : "";
        if (empty($gamificationOwnershipMasterId) || !is_numeric($gamificationOwnershipMasterId)) {
            throw new Exception(gettext("gamification_ownership_master") . gettext("must enter gamificationOwnershipMasterId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " gamificationOwnershipMasterId = '$gamificationOwnershipMasterId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $gamificationOwnershipMasterId = isset($this->_arrayUpdatedData['gamificationOwnershipMasterId']) ? trim($this->_arrayUpdatedData['gamificationOwnershipMasterId']) : "";
        if (empty($gamificationOwnershipMasterId) || !is_numeric($gamificationOwnershipMasterId)) {
            throw new Exception(gettext("gamification_ownership_master") . gettext("must enter gamificationOwnershipMasterId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " gamificationOwnershipMasterId = '$gamificationOwnershipMasterId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>