<?php

/**
 * @author Mayank Gupta 20111214$.
 * @desc DB class for table gamification_friend_activity_stream.
 */
class Gamification_Db_FriendActivityStream extends Gamification_Db_Abstract {

    protected $_mainTable = 'gamification_friend_activity_stream';
    protected $_clauseColumnNames = array('activityStreamId', 'userId', 'friendId', 'publicationId', 'activityId', 'activityObject', 'activityVerb', 'activityText', 'comman', 'createdTime');
    protected $_updateColumnNames = array('userId', 'friendId', 'publicationId', 'activityId', 'activityObject', 'activityVerb', 'comman', 'createdTime');
    protected $_sortColumnNames = array('activityStreamId', 'userId', 'friendId', 'publicationId', 'activityId', 'activityObject', 'activityVerb', 'activityText', 'comman', 'createdTime');
    protected $_foreignKey = 'activityStreamId';
    protected $_expandableTables = array();

    function add() {
        $userId = isset($this->_arrayUpdatedData['userId']) ? trim($this->_arrayUpdatedData['userId']) : 0;
        if (empty($userId) || !is_numeric($userId)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter userId and it should be numeric"));
        }
        $friendId = isset($this->_arrayUpdatedData['friendId']) ? trim($this->_arrayUpdatedData['friendId']) : 0;
        if (empty($friendId) || !is_numeric($friendId)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter friendId and it should be numeric"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : '';
        if (empty($publicationId) || !is_numeric($publicationId)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter publicationId and it should be numeric"));
        }
        $activityId = isset($this->_arrayUpdatedData['activityId']) ? trim($this->_arrayUpdatedData['activityId']) : 0;
        if (empty($activityId) || !is_numeric($activityId)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter activityId and it should be numeric"));
        }
        $activityObject = isset($this->_arrayUpdatedData['activityObject']) ? trim($this->_arrayUpdatedData['activityObject']) : '';
        if (empty($activityObject)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter activityObject"));
        }
        $activityVerb = isset($this->_arrayUpdatedData['activityVerb']) ? trim($this->_arrayUpdatedData['activityVerb']) : '';
        if (empty($activityVerb)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter activityVerb"));
        }
        $activityText = isset($this->_arrayUpdatedData['activityText']) ? trim($this->_arrayUpdatedData['activityText']) : '';
        if (empty($activityText)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter activityText"));
        }
        $comman = isset($this->_arrayUpdatedData['comman']) ? trim($this->_arrayUpdatedData['comman']) : 'NO';
        if (empty($comman)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter comman"));
        }

        $query = array();
        $query['userId'] = $userId;
        $query['friendId'] = $friendId;
        $query['publicationId'] = $publicationId;
        $query['activityId'] = $activityId;
        $query['activityObject'] = $activityObject;
        $query['activityVerb'] = $activityVerb;
        $query['activityText'] = $activityText;
        $query['comman'] = $comman;
        $query['clientId'] = $clientId;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $query);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {
        //Required and Numeric
        $activityStreamId = isset($this->_arrayUpdatedData['activityStreamId']) ? trim($this->_arrayUpdatedData['activityStreamId']) : 0;
        if (empty($activityStreamId) || !is_numeric($activityStreamId)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter activityStreamId and it should be numeric"));
        }
        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " activityStreamId = '$activityStreamId'");
        } catch (Exception $ex) {
            throw new Exception('Problem in record deletion' . $ex->getMessage());
        }
    }

    function edit() {
        //Required and Numeric
        $activityStreamId = isset($this->_arrayUpdatedData['activityStreamId']) ? trim($this->_arrayUpdatedData['activityStreamId']) : 0;
        if (empty($activityStreamId) || !is_numeric($activityStreamId)) {
            throw new Exception(gettext("gamification_friend_activity_stream") . gettext(": must enter activityStreamId and it should be numeric"));
        }
        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }
        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " activityStreamId = '$activityStreamId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>
