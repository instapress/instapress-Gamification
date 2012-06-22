<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_verb_master
 */
class Gamification_Db_VerbMaster extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_verb_master';
    protected $_clauseColumnNames = array('gamificationVerbMasterId', 'gamificationVerbName', 'commonVerb', 'clientId', 'createdBy', 'createdTime');
    protected $_updateColumnNames = array('gamificationVerbMasterId', 'gamificationVerbName', 'commonVerb', 'clientId', 'createdBy', 'createdTime');
    protected $_sortColumnNames = array('gamificationVerbMasterId', 'gamificationVerbName', 'commonVerb', 'clientId', 'createdBy', 'createdTime');
    protected $_foreignKey = 'gamificationVerbMasterId';
    protected $_expandableTables = array();

    function add() {

        $gamificationVerbName = isset($this->_arrayUpdatedData['gamificationVerbName']) ? trim($this->_arrayUpdatedData['gamificationVerbName']) : '';
        if (empty($gamificationVerbName)) {
            throw new Exception(gettext("gamification_verb_master") . gettext("must enter gamificationVerbName"));
        }
        $commonVerb = isset($this->_arrayUpdatedData['commonVerb']) ? trim($this->_arrayUpdatedData['commonVerb']) : 'YES';
        if (empty($commonVerb)) {
            throw new Exception(gettext("gamification_verb_master") . gettext("must enter commonVerb"));
        }

        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_verb_master") . gettext("must enter clientId and it should be numeric"));
        }
        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : '';
        if (empty($createdBy) || !is_numeric($createdBy)) {
            throw new Exception(gettext("gamification_verb_master") . gettext("must enter createdBy and it should be numeric"));
        }

        $query = array();
        $query['gamificationVerbName'] = $gamificationVerbName;
        $query['commonVerb'] = $commonVerb;
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
        $gamificationVerbMasterId = isset($this->_arrayUpdatedData['gamificationVerbMasterId']) ? trim($this->_arrayUpdatedData['gamificationVerbMasterId']) : "";
        if (empty($gamificationVerbMasterId) || !is_numeric($gamificationVerbMasterId)) {
            throw new Exception(gettext("gamification_verb_master") . gettext("must enter gamificationVerbMasterId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, $queryData, " gamificationVerbMasterId = '$gamificationVerbMasterId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $gamificationVerbMasterId = isset($this->_arrayUpdatedData['gamificationVerbMasterId']) ? trim($this->_arrayUpdatedData['gamificationVerbMasterId']) : "";
        if (empty($gamificationVerbMasterId) || !is_numeric($gamificationVerbMasterId)) {
            throw new Exception(gettext("gamification_verb_master") . gettext("must enter gamificationVerbMasterId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " gamificationVerbMasterId = '$gamificationVerbMasterId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
