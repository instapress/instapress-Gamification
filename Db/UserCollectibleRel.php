<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_user_collectible_rel
 */
class Gamification_Db_UserCollectibleRel extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_user_collectible_rel';
    protected $_clauseColumnNames = array('gamificationUserCollectibleRelId','userId', 'activityObject', 'publicationId', 'createdTime', 'collectibleId', 'clientId', 'contentType');
    protected $_updateColumnNames = array('gamificationUserCollectibleRelId','userId', 'activityObject', 'publicationId', 'createdTime', 'collectibleId', 'clientId', 'contentType');
    protected $_sortColumnNames = array('gamificationUserCollectibleRelId','userId', 'activityObject', 'publicationId', 'createdTime', 'collectibleId', 'clientId', 'contentType');
    protected $_foreignKey = 'gamificationUserCollectibleRelId';
    protected $_expandableTables = array();

    function add() {

        $userId = isset($this->_arrayUpdatedData['userId']) ? trim($this->_arrayUpdatedData['userId']) : '';
        if (empty($userId) || !is_numeric($userId)) {
            throw new Exception(gettext("gamification_user_collectible_rel") . gettext("must enter userId and it should be numeric"));
        }
        $collectibleId = isset($this->_arrayUpdatedData['collectibleId']) ? trim($this->_arrayUpdatedData['collectibleId']) : '';
        if (empty($collectibleId) || !is_numeric($collectibleId)) {
            throw new Exception(gettext("gamification_user_collectible_rel") . gettext("must enter collectibleId and it should be numeric"));
        }
        $contentType = isset($this->_arrayUpdatedData['contentType']) ? trim($this->_arrayUpdatedData['contentType']) : '';
        if (empty($contentType)) {
            throw new Exception(gettext("gamification_user_collectible_rel") . gettext("must enter contentType"));
        }
        $activityObject = isset($this->_arrayUpdatedData['activityObject']) ? trim($this->_arrayUpdatedData['activityObject']) : '';
        if (empty($activityObject)) {
            throw new Exception(gettext("gamification_user_collectible_rel") . gettext("must enter activityObject"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_user_collectible_rel") . gettext("must enter publicationId and it should be numeric"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_user_collectible_rel") . gettext("must enter clientId and it should be numeric"));
        }

        $query = array();
        $query['userId'] = $userId;
        $query['collectibleId'] = $collectibleId;
        $query['contentType'] = $contentType;
        $query['activityObject'] = $activityObject;
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
        $gamificationUserCollectibleRelId = isset($this->_arrayUpdatedData['gamificationUserCollectibleRelId']) ? trim($this->_arrayUpdatedData['gamificationUserCollectibleRelId']) : "";
        if (empty($gamificationUserCollectibleRelId) || !is_numeric($gamificationUserCollectibleRelId)) {
            throw new Exception(gettext("gamification_user_collectible_rel") . gettext("must enter gamificationUserCollectibleRelId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " gamificationUserCollectibleRelId = '$gamificationUserCollectibleRelId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $gamificationUserCollectibleRelId = isset($this->_arrayUpdatedData['gamificationUserCollectibleRelId']) ? trim($this->_arrayUpdatedData['gamificationUserCollectibleRelId']) : "";
        if (empty($gamificationUserCollectibleRelId) || !is_numeric($gamificationUserCollectibleRelId)) {
            throw new Exception(gettext("gamification_user_collectible_rel") . gettext("must enter gamificationUserCollectibleRelId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " gamificationUserCollectibleRelId = '$gamificationUserCollectibleRelId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
