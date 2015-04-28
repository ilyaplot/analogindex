<?php
/*Enter database settings*/
define('LOCAL_DB_HOST', 'localhost');
define('LOCAL_DB_NAME', 'analogindex');
define('LOCAL_DB_USER', 'analogindex');
define('LOCAL_DB_PASSWORD', 'analogindex');
define('PREFIX', 'cackle');


class CackleAPI{

    function CackleAPI(){
        $this->last_error = null;
    }
    function db_connect($query){
        try {
            $hd="mysql:host=" . LOCAL_DB_HOST . ";dbname=" . LOCAL_DB_NAME;
            $DBH = new PDO($hd, LOCAL_DB_USER, LOCAL_DB_PASSWORD, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_PERSISTENT => true,
            ));
			
            $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $STH = $DBH->query($query);

            #  устанавливаем режим выборки
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            $x=0;
            $row=array();
            while($res = $STH->fetch()) {
                $row[$x]=$res;
                $x++;
            }
            $DBH = null;
            return $row;
        }
        catch(PDOException $e) {
           // echo "invalid sql - $query - ";
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
    }
    function conn(){
        try {
            $hd="mysql:host=" . LOCAL_DB_HOST . ";dbname=" . LOCAL_DB_NAME;
            $DBH = new PDO($hd, LOCAL_DB_USER, LOCAL_DB_PASSWORD);
            $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

            return $DBH;
        }
        catch(PDOException $e) {
            echo "invalid sql - $query - ";
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
    }
    function db_table_exist($table){
        $hd="mysql:host=" . LOCAL_DB_HOST . ";dbname=" . LOCAL_DB_NAME;
        $DBH = new PDO($hd, LOCAL_DB_USER, LOCAL_DB_PASSWORD);
        $tableExists = (gettype($DBH->exec("SELECT count(*) FROM $table")) == "integer")?true:false;
        return $tableExists;
    }
    function db_column_exist($table,$column){
        if ($this->db_table_exist($table)){
            $hd="mysql:host=" . LOCAL_DB_HOST . ";dbname=" . LOCAL_DB_NAME;
            $DBH = new PDO($hd, LOCAL_DB_USER, LOCAL_DB_PASSWORD);
            $quer= "SHOW COLUMNS FROM $table LIKE '$column'";
            $column_exist = $DBH->query($quer)->fetch();
            $column_exist = $column_exist['Field'];
            //$column_exist = (gettype($DBH->query("SHOW COLUMNS FROM $table LIKE '$column''")) == "integer")?true:false;
            return $column_exist;
            //return $quer;
        }
        else {
            return false;
        }
    }
    function cackle_set_param($param, $value){
        $hd="mysql:host=" . LOCAL_DB_HOST . ";dbname=" . LOCAL_DB_NAME;
        $DBH = new PDO($hd, LOCAL_DB_USER, LOCAL_DB_PASSWORD);

        if ($this->db_table_exist("".PREFIX."_cackle")){
            $DBH->query("delete from ".PREFIX."_cackle where param = '$param'");
            $DBH->query("insert into ".PREFIX."_cackle (param, value) values ('$param','$value')");
        }
        else{
            $this->db_connect("CREATE TABLE ".PREFIX."_cackle (param VARCHAR(100) NOT NULL DEFAULT '',value VARCHAR(100) NOT NULL DEFAULT '')");
        }
    }

    function cackle_get_param($param,$default=0){
        $hd="mysql:host=" . LOCAL_DB_HOST . ";dbname=" . LOCAL_DB_NAME;
        $DBH = new PDO($hd, LOCAL_DB_USER, LOCAL_DB_PASSWORD);

        if ($this->db_table_exist("".PREFIX."_cackle")){
            $ex = $DBH->query("select value from ".PREFIX."_cackle where param = '$param'")->fetch();
            $res = $ex['value'];
            if ($res == null){
                $res = 0;
            }
            return $res;
        }
        else{
            $this->db_connect("CREATE TABLE ".PREFIX."_cackle (param VARCHAR(100) NOT NULL DEFAULT '',value VARCHAR(100) NOT NULL DEFAULT '')");
        }
    }
    function cackle_db_prepare(){

        if ($this->db_table_exist("".PREFIX."_comments")){
            $this->db_connect("ALTER TABLE ".PREFIX."_comments ADD user_agent VARCHAR(64) NOT NULL default ''");
           // $this->db_connect("ALTER TABLE ".PREFIX."_comments MODIFY 'user_agent' varchar(64) NOT NULL default ''");
        }
        else {
            $create_comment_sql = "
        CREATE TABLE ".PREFIX."_comments (
	id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	channel TEXT NOT NULL,
	comment TEXT NOT NULL,
	date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	autor VARCHAR(40) NOT NULL DEFAULT '',
	email VARCHAR(40) NULL DEFAULT '',
	avatar VARCHAR(50) NULL DEFAULT NULL,
	ip VARCHAR(16) NOT NULL DEFAULT '',
	is_register TINYINT(1) NOT NULL DEFAULT '0',
	approve TINYINT(1) NOT NULL DEFAULT '1',
	user_agent VARCHAR(64) NOT NULL DEFAULT '',
	PRIMARY KEY (id)
)


        ";
            $this->db_connect($create_comment_sql);
        }

    }

    function get_last_error() {
        if (empty($this->last_error)) return;
        if (!is_string($this->last_error)) {
            return var_export($this->last_error);
        }
        return $this->last_error;
    }
    function curl($url) {
        $ch = curl_init();
        $php_version = phpversion();
        $useragent = "Drupal";
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("referer" =>  "localhost"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}