<?php

/**
 * This Class is a singleton class which provides CRUD functions related to mysql database
 *
 */
class Gamification_Connection {

// holds single instance
    private static $hinst = null;
    private static $hinstm = null;
    private static $hinsts = null;

    /**
     * Constructor
     * @return Database Class
     */
    private function __construct() {
        $this->hinstm = Gamification_Connection_Master::GetInstance();
        $this->hinsts = Gamification_Connection_Slave::GetInstance();
    }

	function __destruct() {
		// ParentClass has a close() method
		if( self::$hinstm !== null ) {
			self::$hinstm->Close();
		}
		if( self::$hinsts !== null ) {
			self::$hinsts->Close();
		}
	}

    /**
     * Return instance
     * @return instance
     */
    public static function GetInstance() {

        if (!self::$hinst) {
            //echo "Network";
            //echo "<br/>";
            self::$hinst = new self();
        }

        return self::$hinst;
    }

    /**
     * 
     * @param $sql contains sql 'select' query
     * @return array
     */
    function FetchAllArray($sql) {
        //fetch all array from slave
        $sql = trim($sql);
        $temp_sql = $sql;
        $arr_temp_sql = explode(" ", $temp_sql);
        $check_sql_term = strtolower(trim($arr_temp_sql[0]));
        if ('select' == $check_sql_term) {
            return $this->hinsts->FetchAllArray($sql);
        }
        else
            throw new Exception("Hey, you are not using select statement.");
    }

    /**
     * Updates an table values based on $data and $where
     * @param $table, string, tablename
     * @param $data, array, [name][value] pair
     * @param $where, string, condition
     * @return string, just check for true or false
     */
    function QueryUpdate($table, $data, $where='1') {
        return $this->hinstm->QueryUpdate($table, $data, $where);
    }

    /**
     * Delete an table values based on $where
     * @param $table, string, tablename
     * @param $where, string, condition
     * @return string, just check for true or false
     */
    function QueryDelete($table, $where='1') {
        return $this->hinstm->QueryDelete($table, $where);
    }

    /**
     * Insert data into table 
     * @param $table, string, table name
     * @param $data, array, key => value pair
     * @return int, primary key of the inserted record
     */
    function QueryInsert($table, $data, $autoIncrement = true) {
        return $this->hinstm->QueryInsert($table, $data, $autoIncrement);
    }

    /**
     * 
     * @param $msg, string, error message
     * @return void
     */
    function oops($msg='') {
        if ($this->link_id > 0) {
            $this->error = mysql_error($this->link_id);
            $this->errno = mysql_errno($this->link_id);
        } else {
            $this->error = mysql_error();
            $this->errno = mysql_errno();
        }
        ?>
        <table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
            <tr><th colspan=2>Database Error</th></tr>
            <tr><td align="right" valign="top">Message:</td><td><?php echo $msg; ?></td></tr>
            <?php if (strlen($this->error) > 0)
                echo '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>' . $this->error . '</td></tr>'; ?>
            <tr><td align="right">Date:</td><td><?php echo date("l, F j, Y \a\\t g:i:s A"); ?></td></tr>
            <tr><td align="right">Script:</td><td><a href="<?php echo @$_SERVER['REQUEST_URI']; ?>"><?php echo @$_SERVER['REQUEST_URI']; ?></a></td></tr>
            <?php if (strlen(@$_SERVER['HTTP_REFERER']) > 0)
                echo '<tr><td align="right">Referer:</td><td><a href="' . @$_SERVER['HTTP_REFERER'] . '">' . @$_SERVER['HTTP_REFERER'] . '</a></td></tr>'; ?>
        </table>
        <?php
    }

    /**
     * Hack to run any query - specially put in for running bulk inserts & joins
     * @param type $query - sql query string
     * @param type $escape - true if query needs to be escaped
     */
    function RunMyQuery($query, $escape=true) {
        if ($escape)
            $query = $this->hinstm->Escape($query);

        return $this->hinstm->Query($query);
    }

}
?>