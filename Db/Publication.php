<?php

class Gamification_Db_Publication extends Gamification_Db_Abstract {

    protected $_mainTable = "gamification_publication";
    protected $_clauseColumnNames = array("publicationId", "clientId", "publicationName",
        "publicationPublishedStory", "publicationStartTime", "publicationCurrentTime", "sectionId",
        "priority", "gaProfileName", "webPropertyIdentity", "webProfileId",
        "createdUserId", "publicationActiveStatus", 'buyerId');
    protected $_sortColumnNames = array("publicationId", "publicationName", "publicationPublishedStory", "createdTime");
    protected $_foreignKey = "publicationId";
    protected $_expandableTables = array();
    protected $_updateColumnNames = array('publicationId', 'statCounterCode', "clientId", "createdUserId", "publicationName", 'qualityAnalysis',
        "publicationDescription", "publicationPublishedStory", 'publicationUrl',
        "publicationStartTime", "publicationCurrentTime", "sectionId", "priority",
        "gaProfileName", "webPropertyIdentity", "webProfileId", "publicationActiveStatus", 'buyerId');

    function add() {
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : "";
        if (!$clientId || !is_numeric($clientId)) {
            throw new Exception(gettext("Publication : ") . gettext("clientId should be a natural number!"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : "";
        if (!$publicationId || !is_numeric($publicationId)) {
            throw new Exception(gettext("Publication : ") . gettext("publicationId should be a natural number!"));
        }

        $publicationName = isset($this->_arrayUpdatedData['publicationName']) ? trim($this->_arrayUpdatedData['publicationName']) : "";
        if (!$publicationName) {
            throw new Exception(gettext("Publication : ") . gettext("Publication name is a required parameter and cannot be blank!"));
        }

        $publicationUrl = isset($this->_arrayUpdatedData['publicationUrl']) ? trim($this->_arrayUpdatedData['publicationUrl']) : "";
        if (!$publicationUrl) {
            throw new Exception(gettext("Publication : ") . gettext("Publication url is a required parameter and cannot be blank!"));
        }

        $publicationDescription = isset($this->_arrayUpdatedData['publicationDescription']) ? trim($this->_arrayUpdatedData['publicationDescription']) : "";
        $gaProfileName = isset($this->_arrayUpdatedData['gaProfileName']) ? trim($this->_arrayUpdatedData['gaProfileName']) : "";
        $webPropertyIdentity = isset($this->_arrayUpdatedData['webPropertyIdentity']) ? trim($this->_arrayUpdatedData['webPropertyIdentity']) : "";
        $webProfileId = isset($this->_arrayUpdatedData['webProfileId']) ? trim($this->_arrayUpdatedData['webProfileId']) : 0;
        $publicationPublishedStory = 0;
        $publicationActiveStatus = 'Y';
        $sectionId = isset($this->_arrayUpdatedData['sectionId']) ? trim($this->_arrayUpdatedData['sectionId']) : 0;
        $priority = isset($this->_arrayUpdatedData['priority']) ? trim($this->_arrayUpdatedData['priority']) : 0;
        /* if( !$priority || !is_numeric($priority ))
          {
          throw new Exception("Publication : priority should be a natural number!");
          } */
        $qualityAnalysis = isset($this->_arrayUpdatedData['qualityAnalysis']) ? trim($this->_arrayUpdatedData['qualityAnalysis']) : "N";
        $buyerId = isset($this->_arrayUpdatedData['buyerId']) ? trim($this->_arrayUpdatedData['buyerId']) : 0;
        $createdUserId = isset($this->_arrayUpdatedData['createdUserId']) ? trim($this->_arrayUpdatedData['createdUserId']) : 0;
        $objTempPublication = new Gamification_Db_Publication();
        $objTempPublication->set("clientId||$clientId", "publicationName||$publicationName", "count||Y");
        if ($objTempPublication->getTotalCount() > 0) {
            throw new Exception("Publication : This publication already exists!");
        }

        $queryData = array();
        $queryData['clientId'] = $clientId;
        $queryData['publicationId'] = $publicationId;
        $queryData['publicationName'] = $publicationName;
        $queryData['publicationDescription'] = $publicationDescription;
        $queryData['publicationUrl'] = $publicationUrl;
        $queryData['publicationActiveStatus'] = $publicationActiveStatus;
        $queryData['publicationPublishedStory'] = $publicationPublishedStory;
        $queryData['publicationStartTime'] = 'now()';
        $queryData['sectionId'] = $sectionId;
        $queryData['priority'] = $priority;
        $queryData['gaProfileName'] = $gaProfileName;
        $queryData['webPropertyIdentity'] = $webPropertyIdentity;
        $queryData['webProfileId'] = $webProfileId;
        $queryData['qualityAnalysis'] = $qualityAnalysis;
        $queryData['buyerId'] = $buyerId;
        $queryData['createdUserId'] = $createdUserId;
        $queryData['createdTime'] = 'now()';

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $queryData);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : "";
        if (!$clientId || !is_numeric($clientId)) {
            throw new Exception(gettext("Publication : ") . gettext("clientId should be a natural number!"));
        }
        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : "";
        if (!$publicationId || !is_numeric($publicationId)) {
            throw new Exception(gettext("Publication : ") . gettext("publicationId should be a natural number!"));
        }
        if (self::checkDependencies($clientId, $publicationId) == 0) {
            try {
                $this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and publicationId = '$publicationId'");
            } catch (Exception $ex) {
                throw $ex;
            }
        }
    }

    function edit() {
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : "";
        if (!$clientId || !is_numeric($clientId)) {
            throw new Exception(gettext("Publication : ") . gettext("clientId should be a natural number!"));
        }

        $publicationId = isset($this->_arrayUpdatedData['publicationId']) ? trim($this->_arrayUpdatedData['publicationId']) : "";
        if (!$publicationId || !is_numeric($publicationId)) {
            throw new Exception(gettext("Publication : ") . gettext("publicationId should be a natural number!"));
        }

        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);

                /* if( $column = "publicationName" ){
                  if( !self::checkEditDuplicate( $tagId, $queryData['tagName'] ) ){
                  throw new Exception(  "Publication : This publication name already in use please try another!" );
                  }
                  } */
            }
        }
        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and publicationId = '$publicationId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function checkDependencies($clientId, $publicationId) {
        $objPublicationArchive = new Gamification_Db_PublicationArchive();
        $objPublicationArchive->set("clientId||$clientId", "publicationId||$publicationId");
        $totalCount = $objPublicationArchive->getTotalCount();
        if ($totalCount > 0) {
            throw new Exception(gettext("Publication : ") . gettext("This record can't deleted because entry exist in Publication archive!"));
        }

        $objDossier = new Editorial_Db_Dossier();
        $objDossier->set("clientId||$clientId", "publicationId||$publicationId");
        $totalCount2 = $objDossier->getTotalCount();
        if ($totalCount2 > 0) {
            throw new Exception(gettext("Publication : ") . gettext("This record can't deleted because entry exist in Entn Dossier!"));
        }

        return $totalCount + $totalCount2;
    }

    /*
     * Check weather publication name already in use except this publication id.
     */

    function checkEditDuplicate($publicationId, $publicationName) {
        try {
            $objPublication = new Gamification_Db_Publication();
            $objPublication->set("publicationName||$publicationName");
            if ($objPublication->getTotalCount() > 0) {
                if ($publicationId == $objPublication->get("publicationId")) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}
