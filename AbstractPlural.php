<?php

abstract class Gamification_AbstractPlural {

    private $_dbClassPrefix = '';
    protected $_matchedRecords = array();
    protected $_resultCount = 0;
    protected $_totalCount = 0;
    protected $_totalPages = 0;
    protected $_dbClass = '';

    public function updateStats($totalCount, $totalPages) {
        $this->_totalCount = $totalCount;
        $this->_totalPages = $totalPages;
    }

    public function __construct($firstArgument = false) {
        $this->_dbClassPrefix = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        $this->_dbClassPrefix = $this->_dbClassPrefix[count($this->_dbClassPrefix) - 1] . '_Db_';
        if (func_num_args() > 0) {
            if (is_array($firstArgument)) {
                if ($this->areMyOwn($firstArgument)) {
                    $singularClass = str_replace('Db_', '', $this->_dbClassPrefix) . $this->_dbClass;
                    $this->_matchedRecords = array();
                    foreach ($firstArgument as $expectedObject) {
                        if (is_a($expectedObject, $singularClass)) {
                            $this->_matchedRecords[] = $expectedObject;
                        }
                    }
                    $this->_totalCount = $this->_resultCount = count($this->_matchedRecords);
                    $this->_totalPages = 1;
                } else {
                    $argumentString = implode('", "', $firstArgument);
                    $dbClass = $this->_dbClassPrefix . $this->_dbClass;
                    $selfDbObj = new $dbClass();
                    eval('$selfDbObj->set( "' . $argumentString . '" );');
                    $this->_matchedRecords = array();
                    $this->_resultCount = $selfDbObj->getResultCount();
                    for ($i = 0; $i < $this->_resultCount; $i++) {
                        $singularClass = str_replace('Db_', '', $this->_dbClassPrefix) . $this->_dbClass;
                        $this->_matchedRecords[] = new $singularClass($selfDbObj->getRecord($i));
                    }
                    $this->_totalCount = $selfDbObj->getTotalCount();
                    $this->_totalPages = $selfDbObj->getTotalPages();
                }
            } else {
                $functionArguments = func_get_args();
                $argumentString = implode('", "', $functionArguments);

                $dbClass = $this->_dbClassPrefix . $this->_dbClass;
                $selfDbObj = new $dbClass();
                eval('$selfDbObj->set( "' . $argumentString . '" );');
                $this->_matchedRecords = array();
                $this->_resultCount = $selfDbObj->getResultCount();
                for ($i = 0; $i < $this->_resultCount; $i++) {
                    $singularClass = str_replace('Db_', '', $this->_dbClassPrefix) . $this->_dbClass;
                    $this->_matchedRecords[] = new $singularClass($selfDbObj->getRecord($i));
                }
                $this->_totalCount = $selfDbObj->getTotalCount();
                $this->_totalPages = $selfDbObj->getTotalPages();
            }
        }
    }

    private function areMyOwn($arrayOfObjects) {
        $singularClass = str_replace('Db_', '', $this->_dbClassPrefix) . $this->_dbClass;
        foreach ($arrayOfObjects as $expectedObject) {
            if (!is_a($expectedObject, $singularClass)) {
                return false;
            }
        }
        return true;
    }

    public function get() {
        $argumentsArray = func_get_args();
        $argumentsCount = func_num_args();
        if ($argumentsCount == 1 && is_numeric($argumentsArray[0])) {
            if ($this->_resultCount > $argumentsArray[0]) {
                return $this->_matchedRecords[$argumentsArray[0]];
            } else {
                throw new Exception('No such record found!');
            }
        } else if ($argumentsCount == 1) {
            $singularObj = $this->get(0);
            $query = 'get' . ucfirst($argumentsArray[0]);
            return $singularObj->$query();
        } else if ($argumentsCount > 1) {
            $index = $argumentsArray[1];
            $query = 'get' . ucfirst($argumentsArray[0]);
            if ($this->_resultCount > $index) {
                $singularObj = $this->get($index);
                return $singularObj->$query();
            } else {
                throw new Exception('No such record found!');
            }
        }
    }

    public function __call($functionName, $argumentsArray) {
        $checkForName = "get{$this->_dbClass}s";

        if ($functionName == $checkForName) {
            return $this->_matchedRecords;
        } else {
            if (count($argumentsArray) == 1 && is_numeric($argumentsArray[0])) {
                if ($this->_resultCount > $argumentsArray[0]) {
                    $singularObj = $this->_matchedRecords[$argumentsArray[0]];
                    return $singularObj->$functionName();
                } else {
                    throw new Exception('No such record found!');
                }
            }
            return $this->$functionName(0);
        }
        throw new Exception("Call to undefined function '$functionName'!");
    }

    public function varDump() {
        $return = array();
        foreach ($this->_matchedRecords as $singularObj) {
            $return[] = $singularObj->varDump();
        }
        return $return;
    }

    public function __invoke() {
        return $this->_matchedRecords;
    }

    public function getResultCount() {
        return $this->_resultCount;
    }

    public function getTotalCount() {
        return $this->_totalCount;
    }

    public function getTotalPages() {
        return $this->_totalPages;
    }

}