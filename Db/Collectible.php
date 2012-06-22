<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_collectible
 */
class Gamification_Db_Collectible extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_collectible';
    protected $_clauseColumnNames = array('collectibleId', 'publicationId', 'activityObject', 'contentType', 'activityCount', 'collectibleName', 'collectibleDescription', 'collectibleImageUrl', 'clientId', 'createdTime', 'createdBy');
    protected $_updateColumnNames = array('activityObject', 'publicationId', 'contentType', 'activityCount', 'collectibleName', 'collectibleDescription', 'collectibleImageUrl', 'clientId', 'createdTime', 'createdBy');
    protected $_sortColumnNames = array('collectibleId', 'publicationId', 'activityObject', 'contentType', 'activityCount', 'collectibleName', 'collectibleDescription', 'collectibleImageUrl', 'clientId', 'createdTime', 'createdBy');
    protected $_foreignKey = 'collectibleId';
    protected $_expandableTables = array();

    function add() {

        $activityObject = isset($this->_arrayUpdatedData['activityObject']) ? trim($this->_arrayUpdatedData['activityObject']) : '';
        if (empty($activityObject)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter activityObject"));
        }
        $contentType = isset($this->_arrayUpdatedData['contentType']) ? trim($this->_arrayUpdatedData['contentType']) : '';
        if (empty($contentType)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter contentType"));
        }
        $activityCount = isset($this->_arrayUpdatedData['activityCount']) ? trim($this->_arrayUpdatedData['activityCount']) : '';
        if (empty($activityCount)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter activityCount"));
        }
        $collectibleName = isset($this->_arrayUpdatedData['collectibleName']) ? trim($this->_arrayUpdatedData['collectibleName']) : '';
        if (empty($collectibleName)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter collectibleName"));
        }
        $collectibleDescription = isset($this->_arrayUpdatedData['collectibleDescription']) ? trim($this->_arrayUpdatedData['collectibleDescription']) : '';
        if (empty($collectibleDescription)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter collectibleDescription"));
        }
        $collectibleImageUrl = isset($this->_arrayUpdatedData['collectibleImageUrl']) ? trim($this->_arrayUpdatedData['collectibleImageUrl']) : '';
//        if (empty($collectibleImageUrl)) {
//            throw new Exception(gettext("gamification_collectible") . gettext("must enter collectibleImageUrl"));
//        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter clientId and it should be numeric"));
        }
        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : '';
        if (empty($createdBy) || !is_numeric($createdBy)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter createdBy and it should be numeric"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter publicationId and it should be numeric"));
        }

        $query = array();

        $query['activityObject'] = $activityObject;
        $query['contentType'] = $contentType;
        $query['activityCount'] = $activityCount;
        $query['collectibleName'] = $collectibleName;
        $query['collectibleDescription'] = $collectibleDescription;
        $query['collectibleImageUrl'] = $collectibleImageUrl;
        $query['clientId'] = $clientId;
        $query['createdBy'] = $createdBy;
        $query['publicationId'] = $publicationId;


        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $collectibleId = isset($this->_arrayUpdatedData['collectibleId']) ? trim($this->_arrayUpdatedData['collectibleId']) : "";
        if (empty($collectibleId) || !is_numeric($collectibleId)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter collectibleId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " collectibleId = '$collectibleId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $collectibleId = isset($this->_arrayUpdatedData['collectibleId']) ? trim($this->_arrayUpdatedData['collectibleId']) : "";
        if (empty($collectibleId) || !is_numeric($collectibleId)) {
            throw new Exception(gettext("gamification_collectible") . gettext("must enter collectibleId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " collectibleId = '$collectibleId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>