<?php

/**
 * @desc DB class for table gamification_notification
 *
 * @author ashish
 */
class Gamification_Db_Notification extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_notification';
    protected $_clauseColumnNames = array('notificationId', 'notificationTypeId', 'notificationType', 'notificationStatus', 'notificationText', 'userId', 'clientId', 'createdTime', 'publicationId');
    protected $_updateColumnNames = array('notificationTypeId', 'notificationType', 'notificationStatus', 'notificationText', 'userId', 'clientId', 'createdTime', 'publicationId');
    protected $_sortColumnNames = array('notificationId', 'notificationTypeId', 'notificationType', 'notificationStatus', 'notificationText', 'userId', 'clientId', 'createdTime', 'publicationId');
    protected $_foreignKey = 'notificationId';
    protected $_expandableTables = array();

    function add() {

        $notificationTypeId = isset($this->_arrayUpdatedData['notificationTypeId']) ? trim($this->_arrayUpdatedData['notificationTypeId']) : '';
        if (empty($notificationTypeId) || !is_numeric($notificationTypeId)) {
            throw new Exception(gettext("gamification_notification") . gettext("must enter notificationTypeId"));
        }
        $notificationType = isset($this->_arrayUpdatedData['notificationType']) ? trim($this->_arrayUpdatedData['notificationType']) : '';
        if (empty($notificationType)) {
            throw new Exception(gettext("gamification_notification") . gettext("must enter notificationType"));
        }
        $notificationStatus = isset($this->_arrayUpdatedData['notificationStatus']) ? trim($this->_arrayUpdatedData['notificationStatus']) : 'unread';
        if (empty($notificationStatus)) {
            throw new Exception(gettext("gamification_notification") . gettext("must enter notificationStatus"));
        }
        $notificationText = isset($this->_arrayUpdatedData['notificationText']) ? trim($this->_arrayUpdatedData['notificationText']) : '';
        if (empty($notificationText)) {
            throw new Exception(gettext("gamification_notification") . gettext("must enter notificationText"));
        }
        $userId = isset($this->_arrayUpdatedData['userId']) ? trim($this->_arrayUpdatedData['userId']) : '';
        if (empty($userId) || !is_numeric($userId)) {
            throw new Exception(gettext("gamification_notification") . gettext("must enter userId and it should be numeric"));
        }
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : '';
        if (empty($clientId) || !is_numeric($clientId)) {
            throw new Exception(gettext("gamification_notification") . gettext("must enter clientId and it should be numeric"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_notification") . gettext("must enter publicationId and it should be numeric"));
        }
        $query = array();

        $query['notificationTypeId'] = $notificationTypeId;
        $query['notificationType'] = $notificationType;
        $query['notificationStatus'] = $notificationStatus;
        $query['notificationText'] = $notificationText;
        $query['userId'] = $userId;
        $query['clientId'] = $clientId;
        $query['publicationId'] = $publicationId;


        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {

        //Required and Numeric
        $notificationId = isset($this->_arrayUpdatedData['notificationId']) ? trim($this->_arrayUpdatedData['notificationId']) : "";
        if (empty($notificationId) || !is_numeric($notificationId)) {
            throw new Exception(gettext("gamification_notification") . gettext("must enter notificationId and it should be numeric"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " notificationId = '$notificationId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {


        //Required and Numeric
        $notificationId = isset($this->_arrayUpdatedData['notificationId']) ? trim($this->_arrayUpdatedData['notificationId']) : "";
        if (empty($notificationId) || !is_numeric($notificationId)) {
            throw new Exception(gettext("gamification_notification") . gettext("must enter notificationId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " notificationId = '$notificationId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
