<?php

/**
 * Description of Gamification_Db_Url
 *
 * @author ashok
 */
class Gamification_Db_Url extends Gamification_Db_Abstract {

    //Basic Table Info
    protected $_mainTable = "gamification_url";
    protected $_clauseColumnNames = array("urlSlug", "phpFilePath", "phpFile", "createdUserId", "createdTime");
    protected $_sortColumnNames = array();
    protected $_foreignKey = "";
    protected $_expandableTables = array();
    protected $_updateColumnNames = array("phpFilePath", "phpFile");

    /*
     * Insert record
     */

    function add() {


        $urlSlug = isset($this->_arrayUpdatedData['urlSlug']) ? trim($this->_arrayUpdatedData['urlSlug']) : "";
        if (!$urlSlug) {
            throw new Exception(gettext("Url: ") . gettext("urlSlug is a required parameter and cannot be blank!"));
        }

        $phpFilePath = isset($this->_arrayUpdatedData['phpFilePath']) ? trim($this->_arrayUpdatedData['phpFilePath']) : "";
        if (!$phpFilePath) {
            throw new Exception(gettext("Url: ") . gettext(" phpFilePath is a required parameter and cannot be blank!"));
        }

        $phpFile = isset($this->_arrayUpdatedData['phpFile']) ? trim($this->_arrayUpdatedData['phpFile']) : "";
        if (!$phpFilePath) {
            throw new Exception(gettext("Url: ") . gettext(" phpFile is a required parameter and cannot be blank!"));
        }


        $objUrl = new Gamification_Db_Url();
        $objUrl->set("urlSlug||$urlSlug");
        $totalCount = $objClient->getTotalCount();

        if ($totalCount > 0)
            throw new Exception(gettext("Url : ") . gettext("This url slug already exists for client!"));

        $queryData = array();
        $queryData['urlSlug'] = $urlSlug;

        $queryData['phpFilePath'] = $phpFilePath;
        $queryData['phpFile'] = $phpFile;
        $queryData['createdUserId'] = $createdUserId;



        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $queryData);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete record
     */
    function delete() {
        $urlId = isset($this->_arrayUpdatedData['urlId']) ? trim($this->_arrayUpdatedData['urlId']) : "";
        if (!$urlId || !is_numeric($urlId)) {
            throw new Exception(gettext("Client : ") . gettext("urlId should be a natural number!"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " urlId = '$clientId'");
        } catch (Exception $e) {
            throw $e;
        }
    }

    /*
     * Edit record
     */

    function edit() {

        $urlId = isset($this->_arrayUpdatedData['urlId']) ? trim($this->_arrayUpdatedData['urlId']) : "";
        if (!$urlId || !is_numeric($urlId)) {
            throw new Exception(gettext("Client : ") . gettext("urlId should be a natural number!"));
        }

        $queryData = array();


        if (isset($this->_arrayUpdatedData[''])) {
            $queryData['phpFilePath'] = trim($this->_arrayUpdatedData['phpFilePath']);
        }

        if (isset($this->_arrayUpdatedData['phpFilePath'])) {
            $queryData['phpFilePath'] = trim($this->_arrayUpdatedData['phpFilePath']);
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " urlId = '$urlId'");
        } catch (Exception $e) {
            throw $e;
        }
    }

}

?>
