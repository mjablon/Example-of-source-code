<?
require_once('conf/conf.php');

class db{

	function conn(){
		$dsn = DB_TYPE."://".DB_USER.":".DB_PASSWD."@".DB_SERVER."/".DB_NAME;
		$db = NewADOConnection($dsn);
		$db->Execute("SET NAMES utf8;");
		$db->Execute("SET CHARACTER SET utf8;");
		$db->Execute("BEGIN") or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when try to open database!<br>" );
		return $db;
	}
	function disconn($db){
		$db->Execute("COMMIT") or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when try to close database!<br>");
		$db->Close();
	}
	
}
?>