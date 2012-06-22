<?php

abstract class Gamification_AbstractSingular {

    private $_dbClassPrefix = '';
    protected $_foreignKey = '';
    protected $_recordArray = array();
    protected $_dbClass = '';
    protected $_relatedClasses = array();
    private $_relatedInfoLoaded = 0;
    protected $_errorMessage = '';

    /**
     * 
     * @param $primaryKeyValue
     * @access public
     * @return Object
     */
    public function __construct($primaryKeyValue = 0) {
        $this->_dbClassPrefix = explode('/', dirname(__FILE__));
        $this->_dbClassPrefix = $this->_dbClassPrefix[count($this->_dbClassPrefix) - 1] . '_Db_';
        if (is_array($primaryKeyValue)) {
            if (isset($primaryKeyValue[$this->_foreignKey])) {
                $this->_init($primaryKeyValue);
            } else {
                throw new Exception(gettext($this->_dbClass . ' : ') . gettext('Object initialization not possible with provided data!'));
            }
        } else if ($primaryKeyValue > 0) {
            $dbClass = $this->_dbClassPrefix . $this->_dbClass;
            $selfDbObj = new $dbClass();
            $selfDbObj->set("$this->_foreignKey||$primaryKeyValue");

            if ($selfDbObj->getResultCount() > 0) {
                $this->_init($selfDbObj->getRecord(0));
            } else {
                throw new Exception(gettext($this->_dbClass . ' : ') . gettext($this->_foreignKey . ' does not exist!'));
            }
        } else {
            throw new Exception(gettext($this->_dbClass . ' : ') . gettext($this->_foreignKey . ' should be natural number!'));
        }
    }

    public function getMessage() {
        return $this->_errorMessage;
    }

    private function _init($recordArray) {
        $this->_recordArray = $recordArray;
    }

    /**
     * 
     * @param string $functionName
     * @param array $arguments
     * @return mixed
     */
    public function __call($functionName, $arguments) {
        if (substr($functionName, 0, 3) == 'get') {
            $varName = lcfirst(substr($functionName, 3));
            if (isset($this->_recordArray[$varName])) {
                return $this->_recordArray[$varName];
            } else {
                $relatedClassesCount = count($this->_relatedClasses);
                if ($relatedClassesCount == $this->_relatedInfoLoaded) {
                    throw new Exception("Call to undefinded function '$functionName'!");
                } else {
                    while ($relatedClassesCount > $this->_relatedInfoLoaded && !isset($this->$varName)) {
                        $this->_loadRelatedInfo();
                    }
                    if (isset($this->_recordArray[$varName])) {
                        return $this->_recordArray[$varName];
                    } else {
                        throw new Exception("Call to undefinded function '$functionName'!");
                    }
                }
            }
        } else {
            throw new Exception("Call to undefinded function '$functionName'!");
        }
    }

    public function varDump() {
        while ($this->_relatedInfoLoaded != count($this->_relatedClasses)) {
            $this->_loadRelatedInfo();
        }
        return $this->_recordArray;
    }

    /**
     * Loads additional information from related classes.
     * @return void
     */
    private function _loadRelatedInfo() {
        $nextClass = $this->_relatedClasses[$this->_relatedInfoLoaded++];
        $runFunction = 'get' . ucfirst($this->_foreignKey);
        $nextClassObj = new $nextClass();
        $nextClassObj->set($this->_foreignKey . '||' . $this->$runFunction());

        if ($nextClassObj->getResultCount() > 0) {
            $this->_recordArray = array_merge($this->_recordArray, $nextClassObj->getRecord(0));
        }
    }

    /**
     * 
     * @return array
     */
    public function getColumnNames() {
        return array_keys($this->_recordArray);
    }

    /**
     * Updates information of a user to database.
     * @return boolean
     * $userObj->update( "userFirstName||Rashid", "userLastName||Mohamad" );
     */
    public function update($firstArgument) {
        if (func_num_args() > 0) {
            $functionArguments = is_array($firstArgument) ? $firstArgument : func_get_args();
            $dbClass = $this->_dbClassPrefix . $this->_dbClass;
            $runFunction = 'get' . ucfirst($this->_foreignKey);
            $argumentList = '"' . str_replace('<--- IB --->', '", "', str_replace('"', '\"', implode('<--- IB --->', $functionArguments))) . '", "' . $this->_foreignKey . '||' . $this->$runFunction() . '"';
            $selfDbObj = new $dbClass('edit');
            try {
                eval('$selfDbObj->set( ' . $argumentList . ' );');
                return true;
            } catch (Exception $ex) {
                $this->_errorMessage = $ex->getMessage();
                return false;
            }
        } else {
            throw new Exception($this->_dbClass . ' : No data found for updation!');
        }
    }

}
