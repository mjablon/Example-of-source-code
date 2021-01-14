<?
#This is it!
require_once('adodb/adodb.inc.php');
require_once('Class.db.php');

class cfgf extends db{
	function cfg_opt(){
		$db = $this->conn_adhcp();
		$rs = $db->Execute("SELECT cfg_id, comment, cfg_txt FROM docsis_cfg_opt ORDER BY cfg_id")
							or die("Incorrect select from docsis_cfg_opt table! MYSQL error: ".mysql_error()."\n");
		$this->disconn($db);
		return $rs->GetArray();
	}
	function add_cfg_file($pckt_name, $comment, $opt_selected){
		$db = $this->conn_adhcp();
		$db->Execute("INSERT INTO docsis_tmp VALUES('', '$pckt_name', '$comment', 1)")
							or die("Incorrect insert to docsis_tmp table! MYSQL error: ".mysql_error()."\n");
		$tmp_id = $db->Insert_ID(); // Id of last insert transaction
		foreach($opt_selected as $key => $val)
			$db->Execute("INSERT INTO docsis_tmp_cfg VALUES('$tmp_id', '$val')")
							or die("Incorrect insert to docsis_tmp_cfg table! MYSQL error: ".mysql_error()."\n");
		$this->disconn($db);
	}
	function tmp_pckt(){
		$db = $this->conn_adhcp();
		$q = "SELECT tmp_id, tmp_name, docsis_tmp.state, docsis_tmp.comment 
			  FROM docsis_tmp
			  LEFT JOIN cmp_type ON docsis_tmp.cmpt_id=cmp_type.cmpt_id";
		$rs = $db->Execute($q)
							or die("Incorrect select from docsis_tmp table! MYSQL error: ".mysql_error()."\n");
		$this->disconn($db);
		return $rs->GetArray();
	}
	function tmp1_pckt($tmp_id){
		$db = $this->conn_adhcp();
		$rs = $db->Execute("SELECT tmp_name, docsis_tmp.comment as comm, docsis_cfg_opt.cfg_id, state FROM docsis_tmp
							LEFT JOIN docsis_tmp_cfg ON docsis_tmp.tmp_id = docsis_tmp_cfg.tmp_id
							LEFT JOIN docsis_cfg_opt ON docsis_tmp_cfg.cfg_id = docsis_cfg_opt.cfg_id
							WHERE docsis_tmp.tmp_id = '$tmp_id'")
							or die("Incorrect select from docsis_tmp table! MYSQL error: ".mysql_error()."\n");
		$this->disconn($db);
		return $rs->GetArray();
	}
	function modify_cfg_file($tmp_id, $pckt_name, $comment, $opt_selected, $state){
		$db = $this->conn_adhcp();
		$db->Execute("DELETE FROM docsis_tmp WHERE tmp_id='$tmp_id'")
					 or die("Incorrect select from docsis_tmp table! MYSQL error: ".mysql_error()."\n");
		$db->Execute("DELETE FROM docsis_tmp_cfg WHERE tmp_id='$tmp_id'")
					  or die("Incorrect select from docsis_tmp table! MYSQL error: ".mysql_error()."\n");
		$db->Execute("INSERT INTO docsis_tmp VALUES('$tmp_id', '$pckt_name', '$comment', '$state')")
					  or die("Incorrect insert to docsis_tmp table! MYSQL error: ".mysql_error()."\n");
		foreach($opt_selected as $key => $val)
			$db->Execute("INSERT INTO docsis_tmp_cfg VALUES('$tmp_id', '$val')")
						 or die("Incorrect insert to docsis_tmp_cfg table! MYSQL error: ".mysql_error()."\n");
		$this->disconn($db);
	}
	function add_opt_cfg($comment, $cfg_txt){
		$db = $this->conn_adhcp();
		$db->Execute("INSERT INTO docsis_cfg_opt VALUES('', '$comment', '$cfg_txt')")
					  or die("Incorrect insert to docsis_cfg_opt table! MYSQL error: ".mysql_error()."\n");
		$this->disconn($db);
	}
	function chg_cfg_file($comment, $cfg_txt, $cfg_id, $op){
		$db = $this->conn_adhcp();
		if($op == 0){ // Modify
			$db->Execute("UPDATE docsis_cfg_opt SET comment='$comment', cfg_txt='$cfg_txt' WHERE cfg_id=$cfg_id")
					  or die("Incorrect update to docsis_cfg_opt table! MYSQL error: ".mysql_error()."\n");
		}
		if($op == 1){ // Delete
			$db->Execute("DELETE FROM docsis_cfg_opt WHERE cfg_id=$cfg_id")
					  or die("Incorrect delete from docsis_cfg_opt table! MYSQL error: ".mysql_error()."\n");
		}
		$this->disconn($db);
	}
	function cmptr($dir){
		$db = $this->conn_adhcp();
		$rs = $db->Execute("SELECT * FROM cmp_transfer WHERE direction='$dir'")
					  or die("Incorrect select form cmp_transfer  table! MYSQL error: ".mysql_error()."\n");
		$this->disconn($db);
		return $rs->GetArray();
	}
}
?>