<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_content_type
 */
class Gamification_Db_ContentType extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_content_type';
    protected $_clauseColumnNames = array('gamificationContentTypeId', 'activityObject', 'contentType', 'createdBy', 'createdTime', 'clientId');
    protected $_updateColumnNames = array('activityObject', 'contentType', 'createdBy', 'createdTime', 'clientId');
    protected $_sortColumnNames = array('gamificationContentTypeId', 'activityObject', 'contentType', 'createdBy', 'createdTime', 'clientId');
    protected $_foreignKey = 'gamificationContentTypeId';
    protected $_expandableTables = array();

    function add() {

        $activityObject = isset($this->_arrayUpdatedData['activityObject']) ? trim($this->_arrayUpdatedData['activityObject']) : '';
        if (empty($activityObject)) {
            throw new Exception(gettext("gamification_content_type") . gettext("must enter activityObject"));
        }
        $contentType = isset($this->_arrayUpdatedData['contentType']) ? trim($this->_arrayUpdatedData['contentType']) : '';
        if (empty($contentType)) {
            throw new Exception(gettext("gamification_content_type") . gettext("must enter contentType"));
        }
        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : '';
        if (empty($createdBy)) {
            throw new Exception(gettext("gamification_content_type") . gettext("must enter createdBy"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_content_type") . gettext("must enter clientId and it should be numeric"));
        }
        $query = array();
        $query['contentType'] = $contentType;
        $query['activityObject'] = $activityObject;
        $query['createdBy'] = $createdBy;
        $query['clientId'] = $clientId;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $gamificationContentTypeId = isset($this->_arrayUpdatedData['gamificationContentTypeId']) ? trim($this->_arrayUpdatedData['gamificationContentTypeId']) : "";
        if (empty($gamificationContentTypeId) || !is_numeric($gamificationContentTypeId)) {
            throw new Exception(gettext("gamification_content_type") . gettext("must enter gamificationContentTypeId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " gamificationContentTypeId = '$gamificationContentTypeId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $gamificationContentTypeId = isset($this->_arrayUpdatedData['gamificationContentTypeId']) ? trim($this->_arrayUpdatedData['gamificationContentTypeId']) : "";
        if (empty($gamificationContentTypeId) || !is_numeric($gamificationContentTypeId)) {
            throw new Exception(gettext("gamification_content_type") . gettext("must enter gamificationContentTypeId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " gamificationContentTypeId = '$gamificationContentTypeId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
