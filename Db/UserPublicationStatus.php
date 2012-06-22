<?php

/**
 * @author Ashish Kumar
 * @desc DB class for table gamification_user_publication_status
 */
class Gamification_Db_UserPublicationStatus extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_user_publication_status';
    protected $_clauseColumnNames = array('userPublicationStatusId','publicationId', 'userId', 'status', 'totalPositivePoints', 'totalNegativePoints', 'lastActivityTime', 'createdTime', 'clientId');
    protected $_updateColumnNames = array('userPublicationStatusId','publicationId', 'userId', 'status', 'totalPositivePoints', 'totalNegativePoints', 'lastActivityTime', 'createdTime', 'clientId');
    protected $_sortColumnNames = array('userPublicationStatusId','publicationId', 'userId', 'status', 'totalPositivePoints', 'totalNegativePoints', 'lastActivityTime', 'createdTime', 'clientId');
    protected $_foreignKey = 'userPublicationStatusId';
    protected $_expandableTables = array();

    function add() {

        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId)) {
            throw new Exception(gettext("gamification_user_publication_status") . gettext("must enter publicationId"));
        }
        $userId = isset($this->_arrayUpdatedData['userId']) ? trim($this->_arrayUpdatedData['userId']) : '';
        if (empty($userId) || !is_numeric($userId)) {
            throw new Exception(gettext("gamification_user_publication_status") . gettext("must enter userId and it should be numeric"));
        }
        $status = isset($this->_arrayUpdatedData['status']) ? trim($this->_arrayUpdatedData['status']) : 'active';
        if (empty($status)) {
            throw new Exception(gettext("gamification_user_publication_status") . gettext("must enter status"));
        }
        $totalPositivePoints = isset($this->_arrayUpdatedData['totalPositivePoints']) ? trim($this->_arrayUpdatedData['totalPositivePoints']) : '';
//        if (empty($totalPositivePoints) || !is_numeric($totalPositivePoints)) {
//            throw new Exception(gettext("gamification_user_publication_status") . gettext("must enter totalPositivePoints and it should be numeric"));
//        }
        $totalNegativePoints = isset($this->_arrayUpdatedData['totalNegativePoints']) ? trim($this->_arrayUpdatedData['totalNegativePoints']) : '';
//        if (empty($totalNegativePoints) || !is_numeric($totalNegativePoints)) {
//            throw new Exception(gettext("gamification_user_publication_status") . gettext("must enter totalNegativePoints and it should be numeric"));
//        }
        $lastActivityTime = isset($this->_arrayUpdatedData['lastActivityTime']) ? trim($this->_arrayUpdatedData['lastActivityTime']) : 'NOW()';
        if (empty($lastActivityTime)) {
            throw new Exception(gettext("gamification_user_publication_status") . gettext("must enter lastActivityTime"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_user_publication_status") . gettext("must enter clientId and it should be numeric"));
        }

        $query = array();
        $query['publicationId'] = $publicationId;
        $query['userId'] = $userId;
        $query['status'] = $status;
        $query['totalPositivePoints'] = $totalPositivePoints;
        $query['totalNegativePoints'] = $totalNegativePoints;
        $query['lastActivityTime'] = $lastActivityTime;
        $query['clientId'] = $clientId;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $userPublicationStatusId = isset($this->_arrayUpdatedData['userPublicationStatusId']) ? trim($this->_arrayUpdatedData['userPublicationStatusId']) : "";
        if (empty($userPublicationStatusId) || !is_numeric($userPublicationStatusId)) {
            throw new Exception(gettext("gamification_user_publication_status") . gettext("must enter userPublicationStatusId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, $queryData, " userPublicationStatusId = '$userPublicationStatusId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $userPublicationStatusId = isset($this->_arrayUpdatedData['userPublicationStatusId']) ? trim($this->_arrayUpdatedData['userPublicationStatusId']) : "";
        if (empty($userPublicationStatusId) || !is_numeric($userPublicationStatusId)) {
            throw new Exception(gettext("gamification_user_publication_status") . gettext("must enter userPublicationStatusId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " userPublicationStatusId = '$userPublicationStatusId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
