<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_user_level_rel
 */
class Gamification_Db_UserLevelRel extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_user_level_rel';
    protected $_clauseColumnNames = array('userLevelRelId','userId', 'userLevelNo', 'publicationId', 'createdTime', 'clientId');
    protected $_updateColumnNames = array('userLevelRelId','userId', 'publicationId', 'userLevelNo', 'createdTime', 'clientId');
    protected $_sortColumnNames = array('userLevelRelId','userId', 'userLevelNo', 'publicationId', 'createdTime', 'clientId');
    protected $_foreignKey = 'userLevelRelId';
    protected $_expandableTables = array();

    function add() {

        $userId = isset($this->_arrayUpdatedData['userId']) ? trim($this->_arrayUpdatedData['userId']) : '';
        if (empty($userId) || !is_numeric($userId)) {
            throw new Exception(gettext("gamification_user_level_rel") . gettext("must enter userId and it should be numeric"));
        }
        $userLevelNo = isset($this->_arrayUpdatedData['userLevelNo']) ? trim($this->_arrayUpdatedData['userLevelNo']) : '';
        if (empty($userLevelNo) || !is_numeric($userLevelNo)) {
            throw new Exception(gettext("gamification_user_level_rel") . gettext("must enter userLevelNo and it should be numeric"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_user_level_rel") . gettext("must enter publicationId and it should be numeric"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_user_level_rel") . gettext("must enter clientId and it should be numeric"));
        }

        $query = array();
        $query['userId'] = $userId;
        $query['userLevelNo'] = $userLevelNo;
        $query['publicationId'] = $publicationId;
        $query['clientId'] = $clientId;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $userLevelRelId = isset($this->_arrayUpdatedData['userLevelRelId']) ? trim($this->_arrayUpdatedData['userLevelRelId']) : "";
        if (empty($userLevelRelId) || !is_numeric($userLevelRelId)) {
            throw new Exception(gettext("gamification_user_level_rel") . gettext("must enter userLevelRelId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " userLevelRelId = '$userLevelRelId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $userLevelRelId = isset($this->_arrayUpdatedData['userLevelRelId']) ? trim($this->_arrayUpdatedData['userLevelRelId']) : "";
        if (empty($userLevelRelId) || !is_numeric($userLevelRelId)) {
            throw new Exception(gettext("gamification_user_level_rel") . gettext("must enter userLevelRelId and it should be numeric"));
        }

        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " userLevelRelId = '$userLevelRelId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
