<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_moderator_type
 */
class Gamification_Db_ModeratorType extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_moderator_type';
    protected $_clauseColumnNames = array('moderatorTypeId','moderatorNo', 'publicationId', 'moderatorName', 'unlockingPoints', 'imageUrl', 'createdBy', 'createdTime', 'clientId');
    protected $_updateColumnNames = array('moderatorNo','unlockingPoints', 'publicationId', 'moderatorName', 'imageUrl', 'createdBy', 'createdTime', 'clientId');
    protected $_sortColumnNames = array('moderatorTypeId','moderatorNo', 'publicationId', 'moderatorName', 'unlockingPoints', 'createdBy', 'createdTime', 'clientId');
    protected $_foreignKey = 'moderatorTypeId';
    protected $_expandableTables = array();

    function add() {

        $moderatorName = isset($this->_arrayUpdatedData['moderatorName']) ? trim($this->_arrayUpdatedData['moderatorName']) : '';
        if (empty($moderatorName)) {
            throw new Exception(gettext("gamification_moderator_type") . gettext("must enter moderatorName"));
        }
        $imageUrl = isset($this->_arrayUpdatedData['imageUrl']) ? trim($this->_arrayUpdatedData['imageUrl']) : '';
        $unlockingPoints = isset($this->_arrayUpdatedData['unlockingPoints']) ? trim($this->_arrayUpdatedData['unlockingPoints']) : '';
        if (empty($unlockingPoints) || !is_numeric($unlockingPoints)) {
            throw new Exception(gettext("gamification_moderator_type") . gettext("must enter unlockingPoints and it should be numeric"));
        }
        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : '';
        if (empty($createdBy)) {
            throw new Exception(gettext("gamification_moderator_type") . gettext("must enter createdBy"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_moderator_type") . gettext("must enter clientId and it should be numeric"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_moderator_type") . gettext("must enter publicationId and it should be numeric"));
        }
        $moderatorNo = isset($this->_arrayUpdatedData['moderatorNo']) ? trim($this->_arrayUpdatedData['moderatorNo']) : '';
        if (empty($moderatorNo) || !is_numeric($moderatorNo)) {
            throw new Exception(gettext("gamification_moderator_type") . gettext("must enter moderatorNo and it should be numeric"));
        }
        $query = array();
        $query['imageUrl'] = $imageUrl;
        $query['moderatorName'] = $moderatorName;
        $query['unlockingPoints'] = $unlockingPoints;
        $query['createdBy'] = $createdBy;
        $query['clientId'] = $clientId;
        $query['moderatorNo'] = $moderatorNo;
        $query['publicationId'] = $publicationId;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $moderatorTypeId = isset($this->_arrayUpdatedData['moderatorTypeId']) ? trim($this->_arrayUpdatedData['moderatorTypeId']) : "";
        if (empty($moderatorTypeId) || !is_numeric($moderatorTypeId)) {
            throw new Exception(gettext("gamification_moderator_type") . gettext("must enter moderatorTypeId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " moderatorTypeId = '$moderatorTypeId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $moderatorTypeId = isset($this->_arrayUpdatedData['moderatorTypeId']) ? trim($this->_arrayUpdatedData['moderatorTypeId']) : "";
        if (empty($moderatorTypeId) || !is_numeric($moderatorTypeId)) {
            throw new Exception(gettext("gamification_moderator_type") . gettext("must enter moderatorTypeId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " moderatorTypeId = '$moderatorTypeId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
