<?php

abstract class Gamification_Db_Abstract {

    private $_reservedParameters = array("order", "count", "sortOrder", "sortColumn", "pageNumber", "quantity", "sum", "column", "avg");
    //reserve request arguments
    private $_sortOrder = "DESC";
    private $_sortColumn = "createdTime";
    private $_pageNumber = 1;
    protected $_quantity = 10;
    private $_count = 'N';
    private $_order = 'Y';
    private $_sum = array();
    private $_column = array();
    private $_avg = array();
    protected $_enableForcedCondition = false;
    protected $_enableForcedLimit = false;
    protected $_betweenClause = '';
    protected $_limitClause1 = '';
    protected $_limitClause2 = '';
    //basic db and table info
    protected $_databaseConnection = null;
    protected $_mainTable = null;
    protected $_clauseColumnNames = null;
    protected $_sortColumnNames = null;
    protected $_foreignKey = null;
    protected $_expandableTables = null;
    //permissible values variables
    private $_mainTablePermissibleValues = '';
    private $_expandableTablesPermissibleValues = '';
    private $_arrayExpandablePermissibleValues = array();
    //array of all column conditions, where clauses
    private $_arrayClauses = array();
    //add or update variables
    protected $_lastInsertedId = 0;
    protected $_arrayUpdatedData = array();
    private $_task = 'show';
    //resultset information variables
    private $_dataResultArray = array();
    private $_dataResultCount = 0;
    private $_dataTotalCount = 0;
    private $_dataTotalPages = 0;

    public function setForcedConditon($betweenClause) {
        $this->_betweenClause = $betweenClause;
        if (!empty($betweenClause)) {
            $this->_enableForcedCondition = true;
        }
    }

    public function setForcedLimit($limitClause1, $limitClause2) {
        $this->_limitClause1 = $limitClause1;
        $this->_limitClause2 = $limitClause2;
        if (is_int($limitClause1) and is_int($limitClause2)) {
            $this->_enableForcedLimit = true;
        }
    }

    public function __construct($task = 'show') {
        $this->_task = strtolower($task);
        $this->_databaseConnection = Gamification_Connection::GetInstance();
    }

    public function specialQuery($query, $escape=true) {
        $queryResult = $this->_databaseConnection->RunMyQuery($query, $escape);
        return $queryResult;
    }

    public function set() {
        try {
            $argumentsList = func_get_args();



            $stringArgumentList = implode("~", $argumentsList);




            // $argumentsList[] = "clientId||1"; //remove on main usage just for moving data

            $totalArguments = count($argumentsList);



            switch ($this->_task) {
                case "add":
                    for ($i = 0; $i < $totalArguments; $i++) {
                        //process single argument and break into array
                        $tempArgumentArray = explode("||", $argumentsList[$i]);

                        //if someone is not following the keyname||keyvalue rule, throw error
                        if (count($tempArgumentArray) != 2)
                        //throw new Exception("Argument '$argumentsList[$i]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument ") . $argumentsList[$i] . gettext(" is invalid"));

                        //check for keyname present in allowed columns list
                        if (array_search($tempArgumentArray[0], $this->_updateColumnNames) === FALSE) {
                            //throw new Exception("Argument '$tempArgumentArray[0]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument ") . $tempArgumentArray[0] . gettext(" is invalid"));
                        }

                        if ($tempArgumentArray[0] != 'xElementId' && ( substr($tempArgumentArray[0], strlen($tempArgumentArray[0]) - 2) == 'Id' and !is_numeric($tempArgumentArray[1]) )) {
                            //throw new Exception("$tempArgumentArray[0] should be a natural number.");
                            throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("should be a natural number."));
                        }

                        $this->_arrayUpdatedData[$tempArgumentArray[0]] = $tempArgumentArray[1];
                    }

                    $this->add();
                    break;

                case "edit":
                    for ($i = 0; $i < $totalArguments; $i++) {
                        //process single argument and break into array
                        $tempArgumentArray = explode("||", $argumentsList[$i]);

                        //if someone is not following the keyname||keyvalue rule, throw error
                        if (count($tempArgumentArray) != 2)
                        //throw new Exception("Argument '$argumentsList[$i]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument") . $argumentsList[$i] . gettext("is invalid"));

                        //check for keyname present in allowed columns list
                        if (array_search($tempArgumentArray[0], $this->_updateColumnNames) === FALSE) {
                            if ($tempArgumentArray[0] != $this->_foreignKey)
                            //throw new Exception("Argument '$tempArgumentArray[0]' is invalid");
                                throw new Exception(gettext("Abstract : ") . gettext("Argument") . $tempArgumentArray[0] . gettext("is invalid"));
                        }

                        if (substr($tempArgumentArray[0], strlen($tempArgumentArray[0]) - 2) == 'Id' and !is_numeric($tempArgumentArray[1])) {
                            //throw new Exception("$tempArgumentArray[0] should be a natural number.");
                            throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("should be a natural number."));
                        }

                        $this->_arrayUpdatedData[$tempArgumentArray[0]] = $tempArgumentArray[1];
                    }

                    $this->edit();
                    break;

                case "delete":
                    for ($i = 0; $i < $totalArguments; $i++) {
                        //process single argument and break into array
                        $tempArgumentArray = explode("||", $argumentsList[$i]);

                        //if someone is not following the keyname||keyvalue rule, throw error
                        if (count($tempArgumentArray) != 2)
                        //throw new Exception("Argument '$argumentsList[$i]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument") . $argumentsList[$i] . gettext("is invalid"));

                        //check for keyname present in allowed columns list
                        if (array_search($tempArgumentArray[0], $this->_updateColumnNames) === FALSE) {
                            if ($tempArgumentArray[0] != $this->_foreignKey)
                            //throw new Exception("Argument '$tempArgumentArray[0]' is invalid");
                                throw new Exception(gettext("Abstract : ") . gettext("Argument") . $tempArgumentArray[0] . gettext("is invalid"));
                        }

                        if (substr($tempArgumentArray[0], strlen($tempArgumentArray[0]) - 2) == 'Id' and !is_numeric($tempArgumentArray[1])) {
                            //throw new Exception("$tempArgumentArray[0] should be a natural number.");
                            throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("should be a natural number."));
                        }

                        $this->_arrayUpdatedData[$tempArgumentArray[0]] = $this->escape($tempArgumentArray[1]);
                    }
                    $this->delete();
                    break;

                default:
                    //process each argument, set reserve parameters and clauses
                    for ($i = 0; $i < $totalArguments; $i++) {
                        //process single argument and break into array
                        $tempArgumentArray = explode("||", $argumentsList[$i]);
                        //print_r($tempArgumentArray);
                        //if someone is not following the keyname||keyvalue rule, throw error
                        if (count($tempArgumentArray) != 2)
                        //throw new Exception("Argument '$argumentsList[$i]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument ") . $argumentsList[$i] . gettext(" is invalid"));

                        if ($tempArgumentArray[0] != 'xElementId' && ( strstr($tempArgumentArray[0], 'Id') !== FALSE and !is_numeric($tempArgumentArray[1]) )) {
                            //throw new Exception("$tempArgumentArray[0] should be a natural number.");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument ") . $tempArgumentArray[0] . gettext(" is invalid"));
                        }

                        //check for keyname present in reserve parameter list
                        if (array_search($tempArgumentArray[0], $this->_reservedParameters) === FALSE) {
                            //check for keyname present in allowed columns list
                            if (array_search($tempArgumentArray[0], $this->_clauseColumnNames) === FALSE) {
                                //throw new Exception("Argument '$tempArgumentArray[0]' is invalid");
                                throw new Exception(gettext("Abstract : ") . gettext("Argument") . $tempArgumentArray[0] . gettext("is invalid"));
                            } else {
                                //set column clauses
                                $firstCharacter = substr($tempArgumentArray[1], 0, 1);
                                if ('=' == $firstCharacter || '<' == $firstCharacter || '>' == $firstCharacter) {
                                    $tempArgumentArray[1] = substr($tempArgumentArray[1], 1);
                                    $clauseValue = $this->escape($tempArgumentArray[1]);
                                    $tempClause = "$tempArgumentArray[0] $firstCharacter $clauseValue";
                                } else {
                                    $clauseValue = $this->escape($tempArgumentArray[1]);
                                    $tempClause = "$tempArgumentArray[0] = '$clauseValue'";
                                }
                                array_push($this->_arrayClauses, $tempClause);
                            }
                        } else {
                            //process keyname to populate reserve parameter variables
                            $firstCharacter = substr($tempArgumentArray[1], 0, 1);
                            if ('=' == $firstCharacter)
                                $tempArgumentArray[1] = substr($tempArgumentArray[1], 1);

                            if ('<' == $firstCharacter || '>' == $firstCharacter)
                            //throw new Exception("'<','>' are not allowed with $tempArgumentArray[0]");
                                throw new Exception(gettext("Abstract : ") . gettext("'<','>' are not allowed with ") . $tempArgumentArray[0]);

                            switch ($tempArgumentArray[0]) {
                                case 'sortOrder':
                                    if ('asc' == strtolower($tempArgumentArray[1]) || 'desc' == strtolower($tempArgumentArray[1]))
                                        $this->_sortOrder = strtoupper($tempArgumentArray[1]);
                                    else
                                    //throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. Valid values : ASC, DESC");
                                        throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("'s value '") . $tempArgumentArray[1] . gettext("'s value  is invalid. Valid values : ASC, DESC"));
                                    break;

                                case 'quantity':
                                    if (is_numeric($tempArgumentArray[1])) {
                                        $this->_quantity = $tempArgumentArray[1];
                                        if ($this->_quantity < 1) {
                                            $this->_quantity = 1;
                                        }
                                    }
                                    else
                                    //throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. It should be Number.");
                                        throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("'s value '") . $tempArgumentArray[1] . gettext("'s value  is invalid. It should be Number."));
                                    break;

                                case 'pageNumber':
                                    if (is_numeric($tempArgumentArray[1]))
                                        $this->_pageNumber = $tempArgumentArray[1];
                                    else
                                    //throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. It should be Number.");
                                        throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("'s value '") . $tempArgumentArray[1] . gettext("'s value  is invalid. It should be Number."));
                                    break;

                                case 'sortColumn':
                                    if (array_search($tempArgumentArray[1], $this->_sortColumnNames) === FALSE) {
                                        $validSortColumns = implode(', ', $this->_sortColumnNames);
                                        //throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. Valid values : $validSortColumns");
                                        throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("'s value '") . $tempArgumentArray[1] . gettext("'s value  is invalid. Valid values : ") . $validSortColumns);
                                    }

                                    $this->_sortColumn = $tempArgumentArray[1];
                                    break;

                                case 'count':
                                    if ('y' == strtolower($tempArgumentArray[1]) || 'n' == strtolower($tempArgumentArray[1]))
                                        $this->_count = strtoupper($tempArgumentArray[1]);
                                    else
                                        throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. Valid values : Y, N");
                                    break;

                                case 'order':
                                    if ('y' == strtolower($tempArgumentArray[1]) || 'n' == strtolower($tempArgumentArray[1]))
                                        $this->_order = strtoupper($tempArgumentArray[1]);
                                    else
                                        throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. Valid values : Y, N");
                                    break;

                                case 'sum':
                                    $var = 'sum(' . $tempArgumentArray[1] . ') as ' . $tempArgumentArray[1] . ' ';
                                    //echo $var."<br/>";print_r($this->_sum);
                                    $this->_sum[] = $var;
                                    break;

                                case 'column':
                                    $this->_column[] = $tempArgumentArray[1];
                                    //array_push($this->_column, $tempArgumentArray[1]);
                                    break;

                                case 'avg':
                                    $var = 'avg(' . $tempArgumentArray[1] . ') as ' . $tempArgumentArray[1] . ' ';
                                    $this->_avg[] = $var;
                                //echo $var."<br/>";
                                //array_push( ($this->_avg), $var);
                            }
                        }
                    }

                    $this->show();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    function show() {
        $start = ($this->_pageNumber - 1) * $this->_quantity;
        $limitClause = " LIMIT $start, $this->_quantity";
        $orderClause = " ORDER BY $this->_sortColumn $this->_sortOrder";

        $clauseList = "";
        if (count($this->_arrayClauses) > 0) {
            $clauseList = implode(" AND ", $this->_arrayClauses);
            $clauseList = " WHERE " . $clauseList;
            if ($this->_enableForcedCondition && $this->_betweenClause) {
                $clauseList .= ' and ' . $this->_betweenClause;
            }
            if ($this->_enableForcedLimit) {
                $limitClause = " LIMIT $this->_limitClause1, $this->_limitClause2";
//                $clauseList .= ' LIMIT ' . $this->_limitClause1 ." , ".$this->_limitClause2;
            }
        } else {

            if ($this->_enableForcedCondition && $this->_betweenClause) {
                $clauseList .= ' where ' . $this->_betweenClause;
            }
            if ($this->_enableForcedLimit) {
                $limitClause = " LIMIT $this->_limitClause1, $this->_limitClause2";
            }
        }

        $sumCheck = (sizeof($this->_sum) > 0) ? true : false;
        $columnCheck = (sizeof($this->_column) > 0) ? true : false;
        $avgCheck = (sizeof($this->_avg) > 0) ? true : false;

        /* print_r($this->_sum);
          print_r($this->_column);
          print_r($this->_avg); */

        if ($sumCheck || $columnCheck || $avgCheck) {
            $dataQueryString = 'SELECT ';

            if ($columnCheck) {
                $dataQueryString .= implode(',', $this->_column);
            }
            if ($sumCheck) {
                if ($columnCheck)
                    $dataQueryString .= ',';

                $dataQueryString .= implode(',', $this->_sum);
            }
            if ($avgCheck) {
                if ($avgCheck)
                    $dataQueryString .= ',';

                $dataQueryString .= implode(',', $this->_avg);
            }

            $dataQueryString .= " FROM $this->_mainTable " . $clauseList;

            //echo $dataQueryString;
            //echo "<br>";

            $queryResult = $this->_databaseConnection->FetchAllArray($dataQueryString);
            $this->_dataResultCount = count($queryResult);
            $this->_dataResultArray = $queryResult;
            //print_r($queryResult);
            if ($this->_dataResultCount > 0)
                $this->_mainTablePermissibleValues = implode(", ", array_keys($this->_dataResultArray[0]));
        }
        else {
            $countQueryString = "SELECT COUNT(*) as dataTotalCount FROM $this->_mainTable " . $clauseList;
            // echo $countQueryString;
            // echo "<br>";

            $queryResult = $this->_databaseConnection->FetchAllArray($countQueryString);
            $this->_dataTotalCount = $queryResult[0]['dataTotalCount'];
            $this->_dataTotalPages = ceil($this->_dataTotalCount / $this->_quantity);

            if ($this->_count == 'N') {

                $dataQueryString = "SELECT * FROM $this->_mainTable " . $clauseList . $orderClause . $limitClause;

                if ($this->_order == 'N') {
                    $dataQueryString = "SELECT * FROM $this->_mainTable " . $clauseList . $limitClause;
                }
                
                //$time1 = Helper::microtime_float();

                $queryResult = $this->_databaseConnection->FetchAllArray($dataQueryString);
                $this->_dataResultCount = count($queryResult);
                $this->_dataResultArray = $queryResult;
                //print_r(array_keys($queryResult[0]));
                
                //$time2 = Helper::microtime_float();
                //printf( "%2.3f", round( ( $time2 - $time1 ),3 ) );
           		//echo " - $dataQueryString<br />";
           		
                if ($this->_dataResultCount > 0)
                    $this->_mainTablePermissibleValues = implode(", ", array_keys($this->_dataResultArray[0]));
            }
        }
    }

    function get($keyName, $index=0, $xindex=1) {
        $range = $this->_dataResultCount > 0 ? $this->_dataResultCount - 1 : $this->_dataResultCount;

        if ($index >= $this->_dataResultCount)
        //throw new Exception("Your requested index '$index' is invalid. Range for get should be 0 - $range");
            throw new Exception(gettext("Abstract : ") . gettext("Your requested index ") . $index . gettext(" is invalid. Range for get should be 0 - ") . $range);

        if (!array_key_exists($keyName, $this->_dataResultArray[$index])) {
            $countExpandableTables = count($this->_expandableTables);
            if ($countExpandableTables == 0) {
                $validArguments = $this->_mainTablePermissibleValues;
                //throw new Exception("Your requested argument '$keyName' is invalid. Valid arguments : $validArguments");
                throw new Exception(gettext("Abstract : ") . gettext("Your requested argument ") . $keyName . gettext(" is invalid. Valid arguments : ") . $validArguments);
            }

            if (isset($this->_dataResultArray[$index][$xindex])) {
                if (array_key_exists($keyName, $this->_dataResultArray[$index][$xindex])) {
                    return $this->_dataResultArray[$index][$xindex][$keyName];
                }
            }
        } else {
//			if('assetElementsOrder' == $keyName)
//			{
//				if(''==$this->_dataResultArray[$index][$keyName])
//					return array();
//				else
//					return explode(",",$this->_dataResultArray[$index][$keyName]);
//			}
            //echo 'mayank is here';
            return $this->_dataResultArray[$index][$keyName];
        }
    }

    function getResultCount() {
        return $this->_dataResultCount;
    }

    function getTotalCount() {
        return $this->_dataTotalCount;
    }

    function getLastInsertedId() {
        return $this->_lastInsertedId;
    }

    function add() {
        echo gettext("Redefine add function");
    }

    function delete() {
        echo gettext("Redefine delete function");
    }

    function edit() {
        echo gettext("Redefine edit function");
    }

    function ipmlIf($keyName, $index=0) {
        $range = $this->_dataResultCount > 0 ? $this->_dataResultCount - 1 : $this->_dataResultCount;

        if ($index >= $this->_dataResultCount)
        //throw new Exception("Your requested index '$index' is invalid. Range for get should be 0 - $range");
            throw new Exception(gettext("Abstract : ") . gettext("Your requested index ") . $index . gettext(" is invalid. Range for get should be 0 - ") . $range);

        if (!array_key_exists($keyName, $this->_dataResultArray[$index])) {
            $validArguments = $this->_mainTablePermissibleValues;
            //throw new Exception("Your requested argument '$keyName' is invalid. Valid arguments : $validArguments");
            throw new Exception(gettext("Abstract : ") . gettext("Your requested argument ") . $keyName . gettext(" is invalid. Valid arguments : ") . $validArguments);
        } else {
            if ('' == $this->_dataResultArray[$index][$keyName])
                return false;
        }

        return true;
    }

    public function getTotalPages() {
        return $this->_dataTotalPages;
    }

    function __call($functionName, $argumentsArray) {
        if (substr($functionName, 0, 3) == 'get') {
            $columnName = lcfirst(substr($functionName, 3));
            return $this->get($columnName, count($argumentsArray) > 0 ? $argumentsArray[0] : 0, count($argumentsArray) > 1 ? $argumentsArray[1] : 0);
        } else {
            throw new Exception("Call to undefinded function '$functionName'!");
        }
    }

    function getRecord($index = false) {
        if ($index === false) {
            return $this->_dataResultArray;
        } else {
            $range = $this->_dataResultCount > 0 ? $this->_dataResultCount - 1 : $this->_dataResultCount;
            if ($index >= $this->_dataResultCount) {
                throw new Exception(gettext("Abstract : ") . gettext("Your requested index ") . $index . gettext(" is invalid. Range for get should be 0 - ") . $range);
            }
            return $this->_dataResultArray[$index];
        }
    }

    public function escape($string) {
        if (( get_magic_quotes_runtime() == 1 ) || ( get_magic_quotes_gpc() == 1 )) {
            $string = stripslashes($string);
        }
        return mysql_real_escape_string($string);
    }

}