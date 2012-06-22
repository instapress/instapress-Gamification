<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_user_moderation_rel
 */
class Gamification_Db_UserModerationRel extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_user_moderation_rel';
    protected $_clauseColumnNames = array('userModerationRelId', 'userId', 'moderatorNo', 'publicationId', 'createdTime', 'clientId');
    protected $_updateColumnNames = array('userModerationRelId', 'userId', 'publicationId', 'moderatorNo', 'createdTime', 'clientId');
    protected $_sortColumnNames = array('userModerationRelId', 'userId', 'moderatorNo', 'publicationId', 'createdTime', 'clientId');
    protected $_foreignKey = 'userModerationRelId';
    protected $_expandableTables = array();

    function add() {

        $userId = isset($this->_arrayUpdatedData['userId']) ? trim($this->_arrayUpdatedData['userId']) : '';
        if (empty($userId) || !is_numeric($userId)) {
            throw new Exception(gettext("gamification_user_moderation_rel") . gettext("must enter userId and it should be numeric"));
        }
        $moderatorNo = isset($this->_arrayUpdatedData['moderatorNo']) ? trim($this->_arrayUpdatedData['moderatorNo']) : '';
        if (empty($moderatorNo) || !is_numeric($moderatorNo)) {
            throw new Exception(gettext("gamification_user_moderation_rel") . gettext("must enter moderatorNo and it should be numeric"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_user_moderation_rel") . gettext("must enter publicationId and it should be numeric"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_user_moderation_rel") . gettext("must enter clientId and it should be numeric"));
        }

        $query = array();
        $query['userId'] = $userId;
        $query['moderatorNo'] = $moderatorNo;
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
        $userModerationRelId = isset($this->_arrayUpdatedData['userModerationRelId']) ? trim($this->_arrayUpdatedData['userModerationRelId']) : "";
        if (empty($userModerationRelId) || !is_numeric($userModerationRelId)) {
            throw new Exception(gettext("gamification_user_moderation_rel") . gettext("must enter userModerationRelId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " userModerationRelId = '$userModerationRelId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $userModerationRelId = isset($this->_arrayUpdatedData['userModerationRelId']) ? trim($this->_arrayUpdatedData['userModerationRelId']) : "";
        if (empty($userModerationRelId) || !is_numeric($userModerationRelId)) {
            throw new Exception(gettext("gamification_user_moderation_rel") . gettext("must enter userModerationRelId and it should be numeric"));
        }

        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " userModerationRelId = '$userModerationRelId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
