<?php

/**
 * This Class is a singleton class which provides CRUD functions related to mysql database
 *
 */
class Gamification_Connection_Master {

    // Server Variables
    private $server = GAMIFICATION_MASTER_SERVER; //database server
    private $user = GAMIFICATION_MASTER_USER; //database login name
    private $pass = GAMIFICATION_MASTER_PSWD; //database login password
    private $database = GAMIFICATION_MASTER_DB; //database name
    private $pre = ""; //table prefix
    private $autoincrement_pre = GAMIFICATION_MASTER_PREFIX;
    //internal info
    private $error = "";
    private $errno = 0;
    //number of rows affected by SQL query
    private $affected_rows = 0;
    private $link_id = 0;
    private $query_id = 0;
    // holds single instance
    private static $hinst = null;

    /**
     * Constructor
     * @return Database Class
     */
    private function __construct() {
        $this->Connect(true);
    }

    /**
     * Return instance
     * @return instance
     */
    public static function GetInstance() {
        if (!self::$hinst) {
            self::$hinst = new self();
        }

        return self::$hinst;
    }

    /**
     * Connects to database
     * @param $new_link false, if u need a single connection thread
     * @return void
     */
    function Connect($new_link=false) {
        $this->link_id = @mysql_connect($this->server, $this->user, $this->pass, $new_link);

        if (!$this->link_id) {//open failed
            $this->oops("Could not connect to server: <b>$this->server</b>.");
        }

        if (!@mysql_select_db($this->database, $this->link_id)) {//no database
            $this->oops("Could not open database: <b>$this->database</b>.");
        }

        // unset the data so it can't be dumped
        $this->server = '';
        $this->user = '';
        $this->pass = '';
        $this->database = '';
    }

    /**
     * Close the mysql connection
     * @return void
     */
    function Close() {
        if (!@mysql_close($this->link_id)) {
            $this->oops("Connection close failed.");
        }
    }

    /**
     *
     * @param $string string value, to be cleaned from malicious characters
     * @return string
     */
    function Escape($string) {
        if (( get_magic_quotes_runtime() == 1 ) || ( get_magic_quotes_gpc() == 1 )) {
            $string = stripslashes($string);
        }
        return mysql_real_escape_string($string);
    }

    /**
     *
     * @param $sql string, real sql queries
     * @return true, false based on success or failure
     */
    function Query($sql) {
        // do query
//                echo $sql;
        $this->query_id = @mysql_query($sql, $this->link_id);

        if (!$this->query_id) {
            $this->oops("<b>MySQL Query fail:</b> $sql");
            return 0;
        }

        $this->affected_rows = @mysql_affected_rows($this->link_id);

        return $this->query_id;
    }

    /**
     *
     * @param $query_id
     * @return array
     */
    private function FetchArray($query_id=-1) {
        // retrieve row
        if ($query_id != -1) {
            $this->query_id = $query_id;
        }

        if (isset($this->query_id)) {
            $record = @mysql_fetch_assoc($this->query_id);
        } else {
            $this->oops("Invalid query_id: <b>$this->query_id</b>. Records could not be fetched.");
        }

        return $record;
    }

    /**
     *
     * @param $sql contains sql 'select' query
     * @return array
     */
    function FetchAllArray($sql) {
        $query_id = $this->Query($sql);
        $out = array();

        while ($row = $this->FetchArray($query_id, $sql)) {
            $out[] = $row;
        }

        $this->FreeResult($query_id);
        return $out;
    }

    /**
     *
     * @param $query_id
     * @return void
     */
    private function FreeResult($query_id=-1) {
        if ($query_id != -1) {
            $this->query_id = $query_id;
        }
        if ($this->query_id != 0 && !@mysql_free_result($this->query_id)) {
            $this->oops("Result ID: <b>$this->query_id</b> could not be freed.");
        }
    }

    /**
     *
     * @param $query_string, contains select query
     * @return array, return only the first row
     */
    function QueryFirst($query_string) {
        $query_id = $this->query($query_string);
        $out = $this->fetch_array($query_id);
        $this->free_result($query_id);
        return $out;
    }

    /**
     * Updates an table values based on $data and $where
     * @param $table, string, tablename
     * @param $data, array, [name][value] pair
     * @param $where, string, condition
     * @return string, just check for true or false
     */
    function QueryUpdate($table, $data, $where='1') {
        $q = "UPDATE `" . $this->pre . $table . "` SET ";

        foreach ($data as $key => $val) {
            if (strtolower($val) == 'null')
                $q.= "`$key` = NULL, ";
            elseif (strtolower($val) == 'now()')
                $q.= "`$key` = NOW(), ";
            elseif (strtolower($val) == 'unix_timestamp()')
                $q.= "`$key` = UNIX_TIMESTAMP(), ";
            else
                $q.= "`$key`='" . $this->escape($val) . "', ";
        }

        $q = rtrim($q, ', ') . ' WHERE ' . $where . ';';

        //echoLine( $q );

        return $this->query($q);
    }

    /**
     * Delete an table values based on $where
     * @param $table, string, tablename
     * @param $where, string, condition
     * @return string, just check for true or false
     */
    function QueryDelete($table, $where='1') {
        $q = 'DELETE FROM `' . $this->pre . $table . '` WHERE ' . $where . ';';
        return $this->query($q);
    }

    /**
     * Insert data into table
     * @param $table, string, table name
     * @param $data, array, key => value pair
     * @return int, primary key of the inserted record
     */
    function QueryInsert($table, $data) {
        $q = "INSERT INTO `" . $this->pre . $table . "` ";
        $v = '';
        $n = '';

        foreach ($data as $key => $val) {
            $n.="`$key`, ";
            if (strtolower($val) == 'null')
                $v.="NULL, ";
            elseif (strtolower($val) == 'now()')
                $v.="NOW(), ";
            elseif (strtolower($val) == 'unix_timestamp()')
                $v.="UNIX_TIMESTAMP(), ";
            else
                $v.= "'" . $this->escape($val) . "', ";
        }

        $q .= "(" . rtrim($n, ', ') . ") VALUES (" . rtrim($v, ', ') . ");";

        //echoLine( $q );

        if ($this->query($q)) {
            //$this->free_result();
            return mysql_insert_id($this->link_id); //this can't be done on non autoincremented tables;
        }
        else
            return false;
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
        <table align="center" border="1" cellspacing="0"
               style="background: white; color: black; width: 80%;">
            <tr>
                <th colspan=2>Database Error</th>
            </tr>
            <tr>
                <td align="right" valign="top">Message:</td>
                <td><?php echo $msg; ?></td>
            </tr>
        <?php if (strlen($this->error) > 0)
            echo '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>' . $this->error . '</td></tr>'; ?>
            <tr>
                <td align="right">Date:</td>
                <td><?php echo date("l, F j, Y \a\\t g:i:s A"); ?></td>
            </tr>
            <tr>
                <td align="right">Script:</td>
                <td><a href="<?php echo @$_SERVER['REQUEST_URI']; ?>"><?php echo @$_SERVER['REQUEST_URI']; ?></a></td>
            </tr>
        <?php if (strlen(@$_SERVER['HTTP_REFERER']) > 0)
            echo '<tr><td align="right">Referer:</td><td><a href="' . @$_SERVER['HTTP_REFERER'] . '">' . @$_SERVER['HTTP_REFERER'] . '</a></td></tr>'; ?>
        </table>
        <?php
    }

}
?>
