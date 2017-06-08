<?
require_once('adodb/adodb.inc.php');
require_once('Class.db.php');
//require_once('Class.cst.php');

class cmp extends db{
	

	function grab_phones_from_db(){

		$temp = array();
		$db = $this->conn();
		$q = "SELECT * FROM cmp WHERE ( (ip LIKE '10.0.0.%') OR  (ip LIKE '10.0.1.%')  OR  (ip LIKE '10.0.2.%')  OR  (ip LIKE '10.0.3.%') OR   (ip LIKE '79.173.54.23') OR   (ip LIKE '79.173.52.160') OR   (ip LIKE '79.173.53.188') OR   (ip LIKE '79.173.52.14') OR   (ip LIKE '79.173.53.64') OR   (ip LIKE '79.173.54.108')) AND (comment LIKE '%TEL%') ORDER BY ip";
		//echo $q."<br>";

		$rs = $db->Execute($q) or die(mysql_error());
		$r = $rs->GetArray();
		//print_r($r);

		foreach ($r as $k => $v) { 	
			//print_r($v);
			$p1 = strstr(strtolower($v['comment']), "tel");
			$p1 = strstr($p1, "61");
			 //$p1 = substr($p1, 0, 10);
			//echo "<pre>$k - ".$v['ip']." - ".$v['comment']." -> ".$p1."</pre><br>";
			echo "<pre>".$p1."</pre>";
		}
		
		$this->disconn($db);

	}

	function cmp_delete_all($cmp_id, $cli_id){

		$db = $this->conn();
		$q1 = "			SELECT 	cmp1.cmp_id AS cmp1,cmp1.mac AS mac1, cmp1.ip AS ip1,
								cmp2.cmp_id AS cmp2,cmp2.mac AS mac2, cmp2.ip AS ip2
						FROM cmp AS cmp1
						LEFT JOIN cmp_subcmp ON cmp1.cmp_id = cmp_subcmp.cmp_id
						LEFT JOIN cmp AS cmp2 ON cmp_subcmp.subcmp_id = cmp2.cmp_id
						WHERE cmp1.cmp_id = ".$cmp_id;
		//echo $q1."<br>";
		
		$rs = $db->Execute($q1) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q1<br>" );;
		$r = $rs->GetArray();
		
		foreach ($r as $k => $v) {
			
			if($v['cmp2'] != NULL ){

				$q2 = "DELETE FROM cmp WHERE cmp_id = ".$v['cmp2'];
				//echo $q2."<br>";
				$db->Execute($q2) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q2<br>" );

				$q3 = "DELETE FROM cmp_net WHERE cmp_id = ".$v['cmp2'];
				//echo $q3."<br>";
				$db->Execute($q3) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q3<br>" );
				
			}
			
		}
		
		$q4 = "DELETE FROM cmp WHERE cmp_id = ".$cmp_id;
		//echo $q4."<br>";
		$db->Execute($q4) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q4<br>" );
		
		$q5 = "DELETE FROM cmp_subcmp WHERE subcmp_id = ".$cmp_id;
		//echo $q5."<br>";
		$db->Execute($q5) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q5<br>" );
		
		$q6 = "DELETE FROM cmp_subcmp WHERE cmp_id = ".$cmp_id;
		//echo $q6."<br>";
		$db->Execute($q6) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q6<br>" );
		
		$q7 = "DELETE FROM cmp_net WHERE cmp_id = ".$cmp_id;
		//echo $q7."<br>";
		$db->Execute($q7) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q7<br>" );
		
		//$this->disconn($db);
		
		//$db = $this->conn();
		
		$q8 = "SELECT * FROM cst_cmp WHERE sys_id = '".$cli_id."'";
		$r = $db->Execute($q8) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q8<br>" );
		$rr = $r->GetArray();

		$n = count($rr);
		if( $n > 1 ) {
			
			$q9 = "DELETE FROM cst_cmp WHERE cmp_id = ".$cmp_id;
			//echo $q9."<br>";
			$db->Execute($q9) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q9<br>" );
			
		}
		else {
		
			$q10 = "DELETE FROM cst_cmp WHERE cmp_id = ".$cmp_id;
			//echo $q10."<br>";
			$db->Execute($q10) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q10<br>" );
			
			$q11 = "DELETE FROM cst WHERE sys_id = '".$cli_id."'";
			//echo $q11."<br>";
			$db->Execute($q11) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $q11<br>" );
		
		}
		
		$this->disconn($db);
	}
	
	function import_db1() {
	
		$db = $this->conn();
		$q = "	SELECT cmp_1.cmp_id as cmp_id1, db1.mac as mac1, db1.ip as ip1, cmp_2.cmp_id as cmp_id2, db2.mac as mac2, db2.ip as ip2 
				FROM import_db AS db1
				LEFT JOIN cmp AS cmp_1 ON db1.mac = cmp_1.mac
				LEFT JOIN import_db AS db2 ON db1.sys_id = db2.sys_id
				LEFT JOIN cmp AS cmp_2 ON db2.mac = cmp_2.mac
				WHERE 
				( db1.cmp_type = 'CPE' AND db1.ip LIKE  '79.173.39.%' )
				AND db2.cmp_type = 'ONT'";
			
		$rs = $db->Execute($q) or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ($r as $k => $v) {
			
			$q = "INSERT IGNORE INTO  `dhcp_server`.`cmp_subcmp` (
					`cmp_id` ,
					`subcmp_id`
					) VALUES 
					('".$v['cmp_id2']."', '".$v['cmp_id1']."');";
			echo $q."<br>";
					
			$db->Execute($q) or die(mysql_error());
		
		}
		
		$this->disconn($db);			
		
	}
	
	function import_db2() {

		$temp = array();
		$temp2 = array();
			
		$db = $this->conn();
		#$q = "SELECT * FROM import_db WHERE cmp_type = 'ONT' AND (ip LIKE '10.0.204.%') ORDER BY ip";
		#$q = "SELECT * FROM import_db WHERE cmp_type = 'ONT' AND (ip LIKE '10.0.205.%') ORDER BY ip";
		#$q = "SELECT * FROM import_db WHERE cmp_type = 'ONT' AND (ip LIKE '10.0.207.%') ORDER BY ip";
		#$q = "SELECT * FROM import_db WHERE cmp_type = 'ONT' AND (ip LIKE '10.100.%') ORDER BY ip";
		$q = "SELECT * FROM import_db WHERE cmp_type = 'ONT' AND (ip LIKE '10.0.216.%') ORDER BY ip";

		$rs = $db->Execute($q) or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ($r as $k => $v) { 	

			$sql = "SELECT * FROM cmp WHERE ip = '".$v['ip']."'";
			$rr = $db->Execute($sql) or die(mysql_error());
			$row = $rr->GetArray();

			$c = count($row);
			if ($c == 0) {

				$in1="INSERT INTO  `dhcp_server`.`cmp` (
				`cmp_id` ,
				`cmpt_id` ,
				`mac` ,
				`ip` ,
				`comment` ,
				`cmp_update` ,
				`state`
				)
				VALUES (
				NULL ,  '5',  '".$v['mac']."',  '".$v['ip']."',  '',  '',  '1'
				);";
							
			
				$db->Execute($in1) or die(mysql_error());
				$id = mysql_insert_id();
				//echo $in1."<br>";

				$in2 = "INSERT INTO cmp_subcmp VALUES(3, ".$id." );";
				$db->Execute($in2) or die(mysql_error());
				//echo $in2."<br>";
				
				$in3 = "INSERT INTO cmp_net VALUES(".$id.", 2 );";
				$db->Execute($in3) or die(mysql_error());
				//echo $in3."<br>";

				$in4 = "INSERT INTO cst_cmp VALUES ( '".$v['sys_id']."', ".$id." );";
				//echo $in4."<br>";
			
				$temp[] = $in4;
				$temp2[] = $v['sys_id'];
				
			}
			
		}
		
		$this->disconn($db);
		
		$db = $this->conn();
		//$cst = new cst;
	
		foreach ($temp as $key => $val) {

			$db->Execute($val) or die(mysql_error());
			//$cst->sys2cst($temp2[$key]);
		}
	
		$this->disconn($db);
		
	}
	
	function import_db3() {
	
		$db = $this->conn();
		$sql = "SELECT * FROM import_db WHERE cmp_type='ONT' AND (ip LIKE '10.100.%') ORDER BY ip";
		$rs = $db->Execute($sql) or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ($r as $k => $v) { 	
		
			$q = "UPDATE cmp SET ip = '".$v['ip']."' WHERE mac='".$v['mac']."'";
			$db->Execute($q) or die(mysql_error());

		}
	
		$this->disconn($db);
	}
	
	function cm_info(){
		$db = $this->conn();
		$q = "SELECT * FROM docsis_cm";
		$rs = $db->Execute($q) or die(mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function cmp_add($cmp_id, $mac, $ip, $cmpt_id, $net_id, $comm){
		$db = $this->conn();
		$q = "SELECT cmp_id FROM cmp WHERE mac='$mac'";
		//print_r($q);
		$rs = $db->Execute($q) or die(mysql_error());
		if($rs->NumRows() != 0){
			$this->disconn($db);
			echo "<br><b>Podany adres MAC jest juz dodany w bazie danych!</b><br>";
			return 0;
		}
		$q = "INSERT INTO cmp 
			  (cmp_id, mac, ip, cmpt_id, state, comment, cmp_update)
			  VALUES (NULL , '$mac', '$ip', $cmpt_id, 1, '$comm', NOW( ));";
		//print_r($q);
		$rs = $db->Execute($q) or die(mysql_error());
		$subcmp_id = $db->Insert_ID();
		if($subcmp_id){
		
			$q = "INSERT INTO cmp_subcmp 
					 (cmp_id, subcmp_id) VALUES ";
			if($cmp_id != NULL)
				$q = $q."($cmp_id, $subcmp_id)";
			else
				$q = $q."(0, $subcmp_id)";
			//print_r($q);
			$rs = $db->Execute($q) or die(mysql_error());
			
			$q = "INSERT INTO cmp_net
				(cmp_id, net_id) VALUES ($subcmp_id, $net_id)";
			//print_r($q);
			$rs = $db->Execute($q) or die(mysql_error());
			$this->disconn($db);
			return $subcmp_id;
		} else return 0;
	}
	function cmp_del($cmp_id){
	
		$db = $this->conn();
		$db->Execute("DELETE FROM cmp WHERE cmp_id = $cmp_id") or die(mysql_error());
		$db->Execute("DELETE cmp.* 
					  FROM cmp 
					  LEFT JOIN cmp_subcmp ON cmp_subcmp.subcmp_id = cmp.cmp_id
					  WHERE cmp_subcmp.cmp_id =$cmp_id") or die(mysql_error());
		$db->Execute("DELETE FROM cmp_net WHERE cmp_id=$cmp_id") or die(mysql_error());
		$db->Execute("DELETE cmp_net.* 
					  FROM cmp_net 
					  LEFT JOIN cmp_subcmp ON cmp_subcmp.subcmp_id = cmp_net.cmp_id
					  WHERE cmp_subcmp.cmp_id =$cmp_id") or die(mysql_error());
		$db->Execute("DELETE FROM cmp_subcmp WHERE subcmp_id=$cmp_id") or die(mysql_error());
		$db->Execute("DELETE FROM cmp_subcmp WHERE cmp_id=$cmp_id") or die(mysql_error());
		$this->disconn($db);
		
	}
	function cmpt($cmpt_id=NULL, $layer){
		$db = $this->conn();
		$q = "SELECT * FROM cmp_type WHERE layer='$layer'";
		if($cmpt_id!=NULL)
			$q += " AND cmpt_id=$cmpt_id";
		$rs = $db->Execute($q) or die(mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function f_cmpcmpt($cmpt_id){
		$db = $this->conn();
		if($cmpt_id != 8)
			$q = "SELECT cmp.cmp_id, cmp.comment FROM cmp 
				  LEFT JOIN cmp_type ON cmp.cmpt_id = cmp_type.cmpt_id 
				  WHERE cmp.cmpt_id=$cmpt_id";
		else
			$q = "SELECT cmp.cmp_id, cmp.comment FROM cmp 
				  LEFT JOIN cmp_type ON cmp.cmpt_id = cmp_type.cmpt_id 
				  WHERE cmp.cmpt_id=$cmpt_id OR cmp.cmpt_id=9";
		$rs = $db->Execute($q) or die(mysql_error());
		$r=$rs->GetArray();
		$this->disconn($db);
		$str="";
		foreach ($r as $k => $v)
			$str .= $v['cmp_id']."|".$v['comment']."|";
		return substr($str, 0, strlen($str)-1 );
	}
	function cmpt_info($cmpt_id){
		$db = $this->conn();
		$q = "SELECT * FROM cmp_type WHERE cmpt_id=$cmpt_id";
		$rs = $db->Execute($q) or die(mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function view_subcmp($cmp_id){
		$db = $this->conn();
		$rs = $db->Execute("SELECT cmp_subcmp.cmp_id, cmp_subcmp.subcmp_id, mac, ip, cmp_type.name AS cmp_type, net_type.name AS net_type, cmp.comment as comment 
							FROM cmp_subcmp 
							LEFT JOIN cmp ON cmp_subcmp.subcmp_id = cmp.cmp_id 
							LEFT JOIN cmp_net ON cmp_net.cmp_id = cmp.cmp_id 
							LEFT JOIN dhcp_net ON cmp_net.net_id = dhcp_net.net_id 
							LEFT JOIN cmp_type ON dhcp_net.cmpt_id = cmp_type.cmpt_id 
							LEFT JOIN net_type ON dhcp_net.nett_id = net_type.nett_id 
							WHERE cmp_subcmp.cmp_id=$cmp_id") or die("Incorrect query: " . mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function view_upcmp($cmp_id){
		$db = $this->conn();
		$rs = $db->Execute("SELECT * FROM `cmp_subcmp` 
							LEFT JOIN cmp ON cmp.cmp_id=cmp_subcmp.cmp_id 
							WHERE cmp_subcmp.subcmp_id = $cmp_id") or die("Incorrect query: " . mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function view_layer($layer){
		$cmpt_id=$this->cmpt(NULL, $layer);
		$q = "SELECT * FROM cmp WHERE cmpt_id=";
		$db = $this->conn();
		$r=array();
		$i=0;
		foreach($cmpt_id as $key => $val){
			$rs = $db->Execute($q.$val['cmpt_id']) or die("Incorrect cmpt query: " . mysql_error());
			$a = $rs->GetArray();
			foreach($a as $k => $v){
				$r[$i]['cmp_id']=$v['cmp_id'];
				$r[$i]['comment']=$v['comment'];
				$i++;
			}
		}
		return $r;
	}
	function cmp_info($cmp_id){
		$db = $this->conn();
		$rs = $db->Execute("SELECT mac, ip, cmp_type.name as cmp_type, cmp_type.cmpt_id, 
							net_type.name as net_type, cmp.comment, net_type.nett_id  
							FROM cmp 
							LEFT JOIN cmp_net ON cmp_net.cmp_id = cmp.cmp_id 
							LEFT JOIN dhcp_net ON cmp_net.net_id = dhcp_net.net_id 
							LEFT JOIN cmp_type ON dhcp_net.cmpt_id=cmp_type.cmpt_id 
							LEFT JOIN net_type ON dhcp_net.nett_id=net_type.nett_id
							WHERE cmp.cmp_id='$cmp_id'")
						  or die("Incorrect data from cmp: ".mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function cmp_updt($cmp_id, $mac, $ip, $cmpt_id){
		$db = $this->conn();
		$q = "UPDATE cmp 
			  SET mac='$mac', ip='$ip', cmpt_id=$cmpt_id, cmp_update=NOW( ) 
			  WHERE cmp_id=$cmp_id";
		print_r($q);
		$db->Execute($q)or die("<center><b>Wprowadzony adres MAC lub IP istnieje w bazie danych!<br><a href='cst.php'><font color=red>Cofnij</font></a></b></center>");
		$this->disconn($db);
		return 1;
	}
	function cmp_edit_type($cmpt_id, $name, $comment, $state){
		$db = $this->conn();
		$db->Execute("UPDATE cmp_type 
					  SET name='$name', comment='$comment', state='$state' 
					  WHERE cmpt_id=$cmpt_id")
					  or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
	}
	function cmp_del_type($cmpt_id){
		$db = $this->conn();
		$db->Execute("DELETE 
					  FROM cmp_type 
					  WHERE cmpt_id=$cmpt_id")
					  or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
	}
	function cmp_add_type($name, $comment, $state){
		$db = $this->conn();
		$db->Execute("INSERT INTO cmp_type 
					  VALUES('', '$name', '$comment', '$state')")
					  or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
	}
	function fw(){
		$db = $this->conn();
		$rs=$db->Execute("SELECT *
						FROM docsis_cm_fw
						JOIN docsis_fw ON docsis_cm_fw.fw_id = docsis_fw.fw_id
						JOIN docsis_cm ON docsis_cm_fw.cm_id = docsis_cm.cm_id") or die(mysql_error());
		$this->disconn($db);
		if(!empty($rs)) 
			return $rs->GetArray();
		else 
			return NULL;
	}
	function fw_info($fw_id){
		$db = $this->conn();
		$rs=$db->Execute("SELECT *
						  FROM docsis_cm_fw
						  JOIN docsis_fw ON docsis_cm_fw.fw_id = docsis_fw.fw_id
						  JOIN docsis_cm ON docsis_cm_fw.cm_id = docsis_cm.cm_id
						  WHERE fw.fw_id=$fw_id") or die(mysql_error());
		$this->disconn($db);
		if(!empty($rs)) 
			return $rs->GetArray();
		else 
			return NULL;
	}
	function fw_add($cm_id, $fw_name){
		$db = $this->conn();
		$db->Execute("INSERT INTO docsis_fw 
					  VALUES('', '$fw_name', $cm_id, 1)")or die(mysql_error());
		$this->disconn($db);
	}
	function fw_del($fw_id){
		$db = $this->conn();
		$db->Execute("DELETE FROM docsis_fw WHERE fw_id='$fw_id'")or die(mysql_error());
		$this->disconn($db);
	}
	function fw_modify($fw_id, $cm_id, $fw_name, $state){
		$db = $this->conn();
		$db->Execute("UPDATE docsis_fw 
					  SET fw_name='$fw_name', cm_id=$cm_id, state='$state'
					  WHERE fw_id=$fw_id")or die(mysql_error());
		$this->disconn($db);
	}
	function cm(){
		$db = $this->conn();
		$rs = $db->Execute("SELECT * FROM docsis_cm")or die(mysql_error());
		$this->disconn($db);
		if(!empty($rs)) 
			return $rs->GetArray();
		else 
			return NULL;
	}
	function cm_del($cm_id){
		$db = $this->conn();
		$rs = $db->Execute("DELETE FROM docsis_cm WHERE cm_id='$cm_id'")or die(mysql_error());
		$this->disconn($db);
	}
	function cm_add($cm_name){
		$db = $this->conn();
		$rs=$db->Execute("INSERT INTO docsis_cm 
						  VALUES('', '$cm_name')")or die(mysql_error());
		$this->disconn($db);
	}
	function cmp_tree($cmp_id){
		$db = $this->conn();
		$arr=array();
		$sql = "SELECT 	cmp1.cmp_id as cmp1_id, cmp1.mac as mac1, cmp1.ip as ip1, 
						cmp2.cmp_id as cmp2_id, cmp2.mac as mac2, cmp2.ip as ip2 
				FROM cmp as cmp1  
				LEFT JOIN cmp_subcmp ON cmp1.cmp_id = cmp_subcmp.subcmp_id 
				LEFT JOIN cmp as cmp2 ON cmp_subcmp.cmp_id = cmp2.cmp_id 
				WHERE cmp1.cmp_id = ";
		while(true){
			$rs=$db->Execute($sql.$cmp_id)or die(mysql_error());
			$t=$rs->GetArray();
			if($t[0]['cmp2_id'] == NULL)
				break;
			else{
				$arr[] = $t;
				$cmp_id = $t[0]['cmp2_id'];
			}
		}
		$this->disconn($db);
		return $arr;
	}
	function onu_ul(){
		$db = $this->conn();
		$sql = "SELECT val 
			FROM gepon_transfer 
			WHERE type='UL'";
		$rs = $db->Execute($sql)or die(mysql_error());
		return $rs->GetArray();
	}
	function onu_dl(){
		$db = $this->conn();
		$sql = "SELECT val 
			FROM gepon_transfer 
			WHERE type='DL'";
		$rs = $db->Execute($sql)or die(mysql_error());
		return $rs->GetArray();
	}
	function distr($cmpt_id){
		$db = $this->conn();
		if($cmpt_id != 8)
			$q = "SELECT cmp.cmp_id, cmp.comment FROM cmp 
				  LEFT JOIN cmp_type ON cmp.cmpt_id = cmp_type.cmpt_id 
				  WHERE cmp.cmpt_id=$cmpt_id";
		else
			$q = "SELECT cmp.cmp_id, cmp.comment FROM cmp 
				  LEFT JOIN cmp_type ON cmp.cmpt_id = cmp_type.cmpt_id 
				  WHERE cmp.cmpt_id=$cmpt_id OR cmp.cmpt_id=9";
		$rs = $db->Execute($q) or die(mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function register_onu($onu_id, 
						$descr, 
						$catv_on, 
						$pir_dl, 
						$pir_ul, 
						$ep1_cmpid,
						$ep1_login,
						$ep1_passwd,
						$ep2_cmpid,
						$ep2_login,
						$ep2_passwd,
						$registrar_ip,
						$registrar_port){
		
		$db = $this->conn();
		$q = "INSERT INTO `dhcp_server`.`gepon_onu` (
		`onu_id` ,
		`iface` ,
		`lport` ,
		`descr` ,
		`catv_on` ,
		`cir_dl` ,
		`pir_dl` ,
		`cir_ul` ,
		`pir_ul` ,
		`ext_oam` ,
		`eth1_vlan` ,
		`eth2_vlan` ,
		`eth3_vlan` ,
		`eth4_vlan` ,
		`eth1_adm` ,
		`eth2_adm` ,
		`eth3_adm` ,
		`eth4_adm` ,
		`ep1_cmpid` ,
		`ep1_login` ,
		`ep1_passwd` ,
		`ep2_cmpid` ,
		`ep2_login` ,
		`ep2_passwd` ,
		`registrar_ip` ,
		`registrar_port` ,
		`oper_s` )	VALUES (
		$onu_id, 
		'', 
		'', 
		'$descr', 
		$catv_on, 
		'', 
		'$pir_dl', 
		'', 
		'$pir_ul', 
		'en', 
		'tag 200 8100', 
		'transp', 
		'transp', 
		'transp', 
		'en', 
		'dis', 
		'dis', 
		'dis', 
		$ep1_cmpid, 
		'$ep1_login', 
		'$ep1_passwd', 
		$ep2_cmpid, 
		'$ep2_login', 
		'$ep2_passwd', 
		'$registrar_ip', 
		'$registrar_port', 
		'-1');";
		$rs = $db->Execute($q) or die(mysql_error());
		$this->disconn($db);
	}
	#create mac +1 in HEX
	function conv_mac( $m_onu ) {

		$m=explode(':', $m_onu);

		if($m[5] == 'ff'){
			$m[5]= '00';
			$f5= 1;
		} else $m[5]= str_pad(dechex(hexdec($m[5])+1), 2, '0', STR_PAD_LEFT);
			
		if($f5 == 1)
			if($m[4] == 'ff'){
				$m[4]= '00';
				$f4= 1;
			} else $m[4]= str_pad(dechex(hexdec($m[4])+1), 2, '0', STR_PAD_LEFT);
		
		if($f4 == 1)
			if($m[3] == 'ff'){
				$m[3]= '00';
				$f3= 1;
			} else $m[3]= str_pad(dechex(hexdec($m[3])+1), 2, '0', STR_PAD_LEFT);
		
		if($f3 == 1)
			if($m[2] == 'ff'){
				$m[2]= '00';
				$f2= 1;
			} else $m[2]= str_pad(dechex(hexdec($m[2])+1), 2, '0', STR_PAD_LEFT);	
		
		if($f2 == 1)
			if($m[1] == 'ff'){
				$m[1]= '00';
				$f1= 1;
			} else $m[1]= str_pad(dechex(hexdec($m[1])+1), 2, '0', STR_PAD_LEFT);
		
		if($f1 == 1)
			if($m[0] == 'ff')
				return 0;
			else $m[0]= str_pad(dechex(hexdec($m[0])+1), 2, '0', STR_PAD_LEFT);
			
		return implode(":", $m);
	}
	
	#create mac +2 in HEX - failed
	function conv_mac_2( $m_onu ) {

		$m=explode(':', $m_onu);

		if($m[5] == 'ff'){
			$m[5]= '00';
			$f5= 1;
		} else $m[5]= str_pad(dechex(hexdec($m[5])+2), 2, '0', STR_PAD_LEFT);
			
		if($f5 == 1)
			if($m[4] == 'ff'){
				$m[4]= '00';
				$f4= 1;
			} else $m[4]= str_pad(dechex(hexdec($m[4])+2), 2, '0', STR_PAD_LEFT);
		
		if($f4 == 1)
			if($m[3] == 'ff'){
				$m[3]= '00';
				$f3= 1;
			} else $m[3]= str_pad(dechex(hexdec($m[3])+2), 2, '0', STR_PAD_LEFT);
		
		if($f3 == 1)
			if($m[2] == 'ff'){
				$m[2]= '00';
				$f2= 1;
			} else $m[2]= str_pad(dechex(hexdec($m[2])+2), 2, '0', STR_PAD_LEFT);	
		
		if($f2 == 1)
			if($m[1] == 'ff'){
				$m[1]= '00';
				$f1= 1;
			} else $m[1]= str_pad(dechex(hexdec($m[1])+2), 2, '0', STR_PAD_LEFT);
		
		if($f1 == 1)
			if($m[0] == 'ff')
				return 0;
			else $m[0]= str_pad(dechex(hexdec($m[0])+2), 2, '0', STR_PAD_LEFT);
			
		return implode(":", $m);
	}
	
	function convers_mac ( ){
	
		$db = $this->conn();
		$sql = "SELECT ADDRESS_MAC FROM temp3";
		//$sql = "UPDATE temp1 SET ADDRESS_MAC = ".$this->conv_mac2('."ADDRESS_MAC"');
		$rs = $db->Execute($sql)or die(mysql_error());
		$a = $rs->GetArray();
		
		foreach($a as $k => $v){
			$mac = $this->conv_mac2($v['ADDRESS_MAC']);
			$sql = "UPDATE temp3 SET ADDRESS_MAC = '".$mac."' WHERE ADDRESS_MAC = '".$v['ADDRESS_MAC']."'";
			//echo $sql."<br>";
			$rr = $db->Execute($sql)or die(mysql_error());

		}
	}
	
	function get_vlanid( $cmp_id, $cmpt_id ) { //for VoD
	
		$db = $this->conn();
		if( $cmp_id != "")
			$sql = "	SELECT dhcp_net.vlan_id 
						FROM cmp_net 
						LEFT JOIN dhcp_net ON cmp_net.net_id=dhcp_net.net_id 
						WHERE ( dhcp_net.cmpt_id=".$cmpt_id." AND cmp_net.cmp_id=".$cmp_id.")";
		else return false;			
		//echo $sql;
		$rs = $db->Execute( $sql )or die("Incorrect vlan information: ".mysql_error());
		return $rs->FetchRow();
		
	}
	 
	function convers_ip ( ){
	
		$db = $this->conn();
		$sql = "SELECT ADDRESS_IP FROM temp3";
		$rs = $db->Execute($sql)or die(mysql_error());
		$a = $rs->GetArray();
		
		foreach($a as $k => $v){
			$ip = $this->conv_ip2( $v['ADDRESS_IP'] );
			$sql = "UPDATE temp3 SET ADDRESS_IP = '".$ip."' WHERE ADDRESS_IP = '".$v['ADDRESS_IP']."'";
			//echo $sql."<br>";
			$rr = $db->Execute($sql)or die(mysql_error());

		}
	}
	
	function conv_mac2( $m ) {
		
		$arr2 = str_split($m, 2);
		return implode(":", $arr2);
	}
	
	function conv_ip2 ( $ip ) {
	
		$arr = explode ( ".", $ip );
		
		//echo $arr[0]." ".$arr[1]." ".$arr[2]." ".$arr[3];
		
		settype( $arr[0], "integer" );
		settype( $arr[1], "integer" );
		settype( $arr[2], "integer" );
		settype( $arr[3], "integer" );
		
		return implode( ".", $arr );
		
	}
	
	function parse_conf ( ) {
	
		$filename = "dhcpd.conf"; //-----> usunac poczatek z pliku zrodlowego (sieci)!
		$handle = fopen($filename, "r");
		$data = array();
		if ($handle) {
			while (!feof($handle)) {
				$text = fgets($handle, 4096);
				if (stripos($text, "hardware ethernet") !== false) {
					$arr = explode(" ", $text);
					$data[] = substr( $arr[2], 0, strlen( $arr[2] )-2 );
				}
				else if (stripos($text, "fixed-address") !== false) {
					$arr = explode(" ", $text);
					$data[] = substr( $arr[1], 0, strlen( $arr[1] )-2 );
				}
			}
			fclose($handle);
		} 
		else {
			die("Error opening a file $filename");
		}
		//print_r($data);
		$sql_in = "INSERT INTO  `dhcp_server`.`temp4` (
			`mac` ,
			`ip`
			)
			VALUES ";
		for ( $i = 0; $i < count( $data ); $i = $i + 2 ) {
			
			$sql_in .= "( '".$data[$i]."',  '".$data[$i+1]."' ),";

		}
		$sql_in = substr( $sql_in, 0, strlen( $sql_in )-1 );
		$sql_in .= ";";

		//echo $sql_in;
	
		$db = $this->conn();
		$rs = $db->Execute( $sql_in )or die(mysql_error());

		$this->disconn($db);
	}
	
	
	function compare_34 ( ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM temp3";
		$rs = $db->Execute( $sql )or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ( $r as $k => $v ) {
			
			//echo $v['ADDRESS_MAC']."<br>";
			$sql = "SELECT * FROM temp4 WHERE mac = '".$this->conv_mac_2( $v['ADDRESS_MAC'] )."'";
			//echo $sql."<br>";
			$rez = $db->Execute( $sql )or die( mysql_error() );
			$row = $rez->GetArray();
			foreach ( $row as $key => $val ) { 
			
				$q = "INSERT INTO temp34 (
						MODEM_ID ,
						MODEL_ID ,
						SERIAL_NUMBER ,
						ADDRESS_IP ,
						ADDRESS_MAC ,
						CUSTOMER_ID
						)
						VALUES ('".$v['MODEM_ID']."',  '".$v['MODEL_ID']."',  '".$v['SERIAL_NUMBER']."',  '".$val['ip']."',  '".$val['mac']."',  '".$v['CUSTOMER_ID']."');";
						
				$db->Execute( $q )or die( mysql_error() );

			}
			
		}
	
	}
	
	function compare_24 ( ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM temp2";
		$rs = $db->Execute( $sql )or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ( $r as $k => $v ) {
			
			//echo $v['ADDRESS_MAC']."<br>";
			$sql = "SELECT * FROM temp4 WHERE mac = '".$this->conv_mac( $v['ADDRESS_MAC'] )."'";
			//echo $sql."<br>";
			$rez = $db->Execute( $sql )or die( mysql_error() );
			$row = $rez->GetArray();
			foreach ( $row as $key => $val ) { 
			
				$q = "INSERT INTO temp24 (
						MODEM_ID ,
						MODEL_ID ,
						SERIAL_NUMBER ,
						ADDRESS_IP ,
						ADDRESS_MAC ,
						CUSTOMER_ID
						)
						VALUES ('".$v['MODEM_ID']."',  '".$v['MODEL_ID']."', '".$v['SERIAL_NUMBER']."', '".$val['ip']."', '".$val['mac']."', '".$v['CUSTOMER_ID']."');";
						
				$db->Execute( $q )or die( mysql_error() );

			}
			
		}
	
	}
	
	function compare_14 ( ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM temp1";
		$rs = $db->Execute( $sql )or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ( $r as $k => $v ) {
			
			//echo $v['ADDRESS_MAC']."<br>";
			$sql = "SELECT * FROM temp4 WHERE mac = '".$this->conv_mac( $v['ADDRESS_MAC'] )."'";
			//echo $sql."<br>";
			$rez = $db->Execute( $sql )or die( mysql_error() );
			$row = $rez->GetArray();
			
			foreach ( $row as $key => $val ) { 
			
				$q = "INSERT INTO temp14 (
						MODEM_ID ,
						MODEL_ID ,
						SERIAL_NUMBER ,
						ADDRESS_IP ,
						ADDRESS_MAC ,
						CUSTOMER_ID
						)
						VALUES ('".$v['MODEM_ID']."',  '', '', '".$val['ip']."', '".$val['mac']."', '');";
						
				$db->Execute( $q )or die( mysql_error() );

			}
			
		}
	
	}
	
	function upload_24 ( ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM temp24";
		$rs = $db->Execute( $sql )or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ( $r as $k => $v ) {
		
			#cmp
			// $sql1 = "INSERT INTO cmp (
					// cmp_id ,
					// cmpt_id ,
					// mac ,
					// ip ,
					// comment ,
					// cmp_update ,
					// state
					// )
					// VALUES 
					// ('".$v['MODEM_ID']."' , '5', '".$v['ADDRESS_MAC']."', '".$v['ADDRESS_IP']."', '".$v['SERIAL_NUMBER']."', '', '1'	);";
			//echo $sql1."<br>";		
			//$db->Execute( $sql1 )or die(mysql_error());
			
			#cmp_net
			// $sql2 = "INSERT INTO  cmp_net (
					// cmp_id ,
					// net_id
					// )
					// VALUES 
					// ('".$v['MODEM_ID']."',  '3');";
			// echo $sql2."<br>";
			// $db->Execute( $sql2 )or die(mysql_error());
			
			#cmp_subcmp
			// $sql3 = "INSERT INTO  cmp_subcmp (
					// cmp_id ,
					// subcmp_id
					// )
					// VALUES ('1',  '".$v['MODEM_ID']."'	);";
			// echo $sql3."<br>";	
			// $db->Execute( $sql3 )or die(mysql_error());
			
			
		}
	
	}
	
	function upload_34 ( ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM temp34";
		$rs = $db->Execute( $sql )or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ( $r as $k => $v ) {
		
			#cmp
			$sql1 = "INSERT INTO cmp (
					cmp_id ,
					cmpt_id ,
					mac ,
					ip ,
					comment ,
					cmp_update ,
					state
					)
					VALUES 
					('".$v['MODEM_ID']."' , '5', '".$v['ADDRESS_MAC']."', '".$v['ADDRESS_IP']."', '".$v['SERIAL_NUMBER']."', '', '1'	);";
			//echo $sql1."<br>";		
			//$db->Execute( $sql1 )or die(mysql_error());
			
			#cmp_net
			$sql2 = "INSERT INTO  cmp_net (
					cmp_id ,
					net_id
					)
					VALUES 
					('".$v['MODEM_ID']."',  '2');";
			//echo $sql2."<br>";
			// $db->Execute( $sql2 )or die(mysql_error());
			
			#cmp_subcmp
			$sql3 = "INSERT INTO  cmp_subcmp (
					cmp_id ,
					subcmp_id
					)
					VALUES ('3',  '".$v['MODEM_ID']."'	);";
			//echo $sql3."<br>";	
			// $db->Execute( $sql3 )or die(mysql_error());
			
			
		}
	
	}
	
	function upload_14 ( ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM temp1_krosno";
		$rs = $db->Execute( $sql )or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ( $r as $k => $v ) {
		
			#cmp
			$sql1 = "INSERT INTO cmp (
					cmp_id ,
					cmpt_id ,
					mac ,
					ip ,
					comment ,
					cmp_update ,
					state
					)
					VALUES 
					('".$v['MODEM_ID']."' , '5', '".$v['ADDRESS_MAC']."', '".$v['ADDRESS_IP']."', '".$v['SERIAL_NUMBER']."', '', '-1'	);";
			//echo $sql1."<br>";		
			//$db->Execute( $sql1 )or die(mysql_error());
			
			#cmp_net
			$sql2 = "INSERT INTO cmp_net (
					cmp_id ,
					net_id
					)
					VALUES 
					('".$v['MODEM_ID']."',  '13');";
			echo $sql2."<br>";
			// $db->Execute( $sql2 )or die(mysql_error());
			
			#cmp_subcmp
			$sql3 = "INSERT INTO cmp_subcmp (
					cmp_id ,
					subcmp_id
					)
					VALUES ('5',  '".$v['MODEM_ID']."'	);";
			echo $sql3."<br><br>";	
			// $db->Execute( $sql3 )or die(mysql_error());
			
			
		}
	
	}
	
	
	function temp14_cmp ( ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM temp14";
		$rs = $db->Execute( $sql )or die(mysql_error());
		$r = $rs->GetArray();
	
		foreach ( $r as $k => $v ) {
			
			$sql1 = "INSERT INTO cmp (
					cmp_id ,
					cmpt_id ,
					mac ,
					ip ,
					comment ,
					cmp_update ,
					state
					)
					VALUES 
					(NULL , '3', '".$v['ADDRESS_MAC']."', '".$v['ADDRESS_IP']."', '', '', '1'	);";

			echo $sql1."<br>";
			
			//$db->Execute( $sql1 )or die(mysql_error());
		
			//$id = mysql_insert_id();
			
			
			$sql2 = "INSERT INTO cmp_net (
					cmp_id ,
					net_id
					)
					VALUES 
					('".$id."',  '8');";
			//echo $sql2."<br>";
			//$db->Execute( $sql2 )or die(mysql_error());
					
			#cmp_subcmp
			$sql3 = "INSERT INTO cmp_subcmp (
					cmp_id ,
					subcmp_id
					)
					VALUES ('".$v['MODEM_ID']."',  '".$id."'	);";
			
			//echo $sql3."<br><br>";	
			//$db->Execute( $sql3 )or die(mysql_error());		
					
		}			
					
	}
	
	function diff ( ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM temp4";
		$rs = $db->Execute( $sql )or die(mysql_error());
		$r = $rs->GetArray();
		
		foreach ( $r as $k => $v ) {
	
			$q = "SELECT ip FROM cmp WHERE ip = '".$v['ip']."'";
			$rr = $db->Execute($q) or die(mysql_error());
			if($rr->NumRows() == 0) 
				echo "INSERT INTO diff VALUES ( '".$v['mac']."' ,'".$v['ip']."');<br>";
			
		}
	}
	
	function diff_find_all ( ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM cmp WHERE cmp.cmpt_id=1 AND NOT EXISTS ( SELECT * FROM cmp_subcmp WHERE cmp_subcmp.subcmp_id = cmp.cmp_id )";
		$rs = $db->Execute( $sql )or die(mysql_error());
		return $rs->GetArray();

	}
	
	function diff_find_spec ( $mac, $ip ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM cmp WHERE (mac = '".$mac."' OR ip = '".$ip."') AND 
				cmp.cmpt_id=1 AND NOT EXISTS ( SELECT * FROM cmp_subcmp WHERE cmp_subcmp.subcmp_id = cmp.cmp_id )";
		$rs = $db->Execute( $sql )or die(mysql_error());
		return $rs->GetArray();

	}
	
	function diff_c( $sys_id, $mac, $ip, $subcmp_id ) {
	
		$db = $this->conn();
		$db->Execute("INSERT INTO cst_cmp ( sys_id , cmp_id )
		  			  VALUES ('".$sys_id."', $subcmp_id);")or die("Invalid insert to cst_cmp: ".mysql_error());
		$this->disconn($db);
		
		$db = $this->conn();
		
		$sql = "INSERT INTO cmp_subcmp VALUES ( '0', '".$subcmp_id."' );";
				
		$db->Execute( $sql )or die(mysql_error());	
		$this->disconn($db);
	}
	
	function diff_updt ( $sys_id, $mac, $ip, $subcmp_id ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM cst_cmp WHERE sys_id='".$sys_id."'";
		$rs = $db->Execute( $sql )or die(mysql_error());
		$rr = $rs->GetArray();
		
		$cmp = array();
		foreach ( $rr as $k => $v ) {  $cmp[] = $v['cmp_id']; }
		$this->disconn($db);
		//print_r( $cmp );
		
		$db = $this->conn();
		
		$ont = array();
		$cpe = array();
		
		foreach ( $cmp as $key => $val ) { 
			
			//echo $val."<br>";
			$sql = "SELECT * FROM cmp WHERE cmp_id = ".$val;
			//echo $sql."<br>";
			$rx = $db->Execute( $sql )or die(mysql_error());
			$r = $rx->FetchRow();
			//echo $r['cmpt_id'];
			if ( $r['cmpt_id'] == 5 ) $ont[] = $r['cmp_id'];
			
			if ($r['cmpt_id'] == 1 ) $cpe[] = $r['cmp_id'];
			
		}
		//print_r($ont);
		//print_r($cpe);
		
		if (count($ont) > 1) { echo "<b>Większa liczba ONT przypisana do jednego użytkownika! Dodaj MAC ręcznie w panelu do określonego ONT!</b>"; return; }
		
		else if (count($ont) == 0) { 
		
			if (count($cpe) != 0 ) {
			
				#dodanie do cmp_subcmp
				$sql = "INSERT INTO cmp_subcmp VALUES ( 0, ".$subcmp_id." );";
				
				//echo $sql."<br>";	
				$db->Execute( $sql )or die(mysql_error());	
				
			}
			else {echo "<b>Brak urządzeń dostępowych!</b><br><b>Podany SYS_ID może nie istnieć w bazie! Zgłoś ten przypadek!</b>"; return; }
		
		}
		else {

			$sql = "INSERT INTO cmp_subcmp VALUES ( ".$ont[0].", ".$subcmp_id." )";
			//echo $sql."<br>";	
			$db->Execute($sql)or die(mysql_error());	
				
		}
		$this->disconn($db);
		
	}
	
	
	function diff2cmp( ) {
	
		$db = $this->conn();
		//$sql = "SELECT * FROM diff WHERE ip LIKE '79.173.36.%'";
		//$sql = "SELECT * FROM diff WHERE ip LIKE '79.173.38.%'";
		//$sql = "SELECT * FROM diff WHERE ip LIKE '79.173.39.%'";
		$sql = "SELECT * FROM diff WHERE ip LIKE '79.173.53.%'";
		$rs = $db->Execute( $sql )or die(mysql_error());
		
		foreach ( $rs as $k => $v ) {
			
			$sql1 =  "INSERT INTO cmp 
					VALUES( '', '1', '".$v['mac']."', '".$v['ip']."', '', '', '1');";

			$db->Execute( $sql1 );
			
			$sql2 = "INSERT INTO cmp_net (
					cmp_id ,
					net_id
					)
					VALUES 
					('".mysql_insert_id()."',  '9');";

			$db->Execute( $sql2 );
			
		}
		
		$this->disconn($db);
	
	}
	
	
	//remove from contract in sysberg db
	function rmv_db($mac, $sys_id){
		$m=strtoupper(trim(preg_replace('/(:)*/','',$mac)));
		$db= $this->conn_sysb();
		$t=ibase_trans($db);
	
		$s="SELECT * FROM ivt_modems WHERE address_mac='$m' AND customer_id='$sys_id';";
		$q=ibase_query($db, $s)or die(ibase_errmsg());
		while($r=ibase_fetch_assoc($q))
			$res[]=$r;
		
		if(count($res)!=1){
			ibase_rollback($t);
			return false;
		}
		else{
			$q=ibase_query($db,"INSERT INTO ARC_IVT_MODEMS 
								(arc_id,modem_id,model_id,serial_number,ivt_number,purchase_doc,net_price,date1,date2,date3,date4,date5,employee_id1,employee_id2,
								dispose_doc,location_id,state,notes,blocked,address_ip,address_mac,address_ip_mta,address_mac_mta,customer_id,user_mod,datetime_mod,
								owner,status,gps_lat,gps_long,update_kind,internet_access,send_mail,ivt_product_id,address_ip_pub,tel_number_id)
								VALUES(
								(SELECT NEXT VALUE FOR GEN_ARC_IVT_MODEMS_ID from RDB\$DATABASE),
								(case when '".$res[0]['MODEM_ID']."'='' then NULL else '".$res[0]['MODEM_ID']."' end),
								(case when '".$res[0]['MODEL_ID']."'='' then NULL else '".$res[0]['MODEL_ID']."' end),
								(case when '".$res[0]['SERIAL_NUMBER']."'='' then NULL else '".$res[0]['SERIAL_NUMBER']."' end), 
								(case when '".$res[0]['IVT_NUMBER']."'='' then NULL else '".$res[0]['IVT_NUMBER']."' end),
								(case when '".$res[0]['PURCHASE_DOC']."'='' then NULL else '".$res[0]['PURCHASE_DOC']."' end),
								(case when '".$res[0]['NET_PRICE']."'='' then NULL else '".$res[0]['NET_PRICE']."' end),
								(case when '".$res[0]['DATE1']."'='' then NULL else '".$res[0]['DATE1']."' end), 
								(case when '".$res[0]['DATE2']."'='' then NULL else '".$res[0]['DATE2']."' end), 
								current_date, 
								(case when '".$res[0]['DATE4']."'='' then NULL else '".$res[0]['DATE4']."' end), 
								(case when '".$res[0]['DATE5']."'='' then NULL else '".$res[0]['DATE5']."' end),
								(case when '".$res[0]['EMPLOYEE_ID1']."'='' then NULL else '".$res[0]['EMPLOYEE_ID1']."' end), 
								(case when '".$res[0]['EMPLOYEE_ID2']."'='' then NULL else '".$res[0]['EMPLOYEE_ID2']."' end), 
								(case when '".$res[0]['DISPOSE_DOC']."'='' then NULL else '".$res[0]['DISPOSE_DOC']."' end), 
								(case when '".$res[0]['LOCATION_ID']."'='' then NULL else '".$res[0]['LOCATION_ID']."' end), 
								(case when '".$res[0]['STATE']."'='' then NULL else '".$res[0]['STATE']."' end), 
								'REZYGNACJA', 
								(case when '".$res[0]['BLOCKED']."'='' then NULL else '".$res[0]['BLOCKED']."' end),
								(case when '".$res[0]['ADDRES_IP']."'='' then NULL else '".$res[0]['ADDRES_IP']."' end), 
								(case when '".$res[0]['ADDRESS_MAC']."'='' then NULL else '".$res[0]['ADDRESS_MAC']."' end), 
								(case when '".$res[0]['ADDRESS_IP_MTA']."'='' then NULL else '".$res[0]['ADDRESS_IP_MTA']."' end),
								(case when '".$res[0]['ADDRESS_MAC_MTA']."'='' then NULL else '".$res[0]['ADDRESS_MAC_MTA']."' end), 
								(case when '".$res[0]['CUSTOMER_ID']."'='' then NULL else '".$res[0]['CUSTOMER_ID']."' end), 
								(select EMPLOYEE_ID from hr_employee where first_name='ZENON' and last_name ='BERNADOWSKI'), 
								current_timestamp, 
								(case when '".$res[0]['OWNER']."'='' then NULL else '".$res[0]['OWNER']."' end), 
								(case when '".$res[0]['STATUS']."'='' then NULL else '".$res[0]['STATUS']."' end), 
								(case when '".$res[0]['GPS_LAT']."'='' then NULL else '".$res[0]['GPS_LAT']."' end),
								(case when '".$res[0]['GPS_LONG']."'='' then NULL else '".$res[0]['GPS_LONG']."' end), 
								'M', 
								(case when '".$res[0]['INTERNET_ACCESS']."'='' then NULL else '".$res[0]['INTERNET_ACCESS']."' end),
								(case when '".$res[0]['SEND_MAIL']."'='' then NULL else '".$res[0]['SEND_MAIL']."' end), 
								(case when '".$res[0]['IVT_PRODUCT_ID']."'='' then NULL else '".$res[0]['IVT_PRODUCT_ID']."' end), 
								(case when '".$res[0]['ADDRESS_IP_PUB']."'='' then NULL else '".$res[0]['ADDRESS_IP_PUB']."' end),
								(case when '".$res[0]['TEL_NUMBER_ID']."'='' then NULL else '".$res[0]['TEL_NUMBER_ID']."' end));")or die(ibase_errmsg());
			}
			if(ibase_affected_rows($db)!=1){
				ibase_rollback($t);
				return false;
			}
			else{							
				$q=ibase_query($db,"UPDATE ivt_modems SET 
									CUSTOMER_ID=NULL, DATETIME_MOD=current_timestamp, state='72', DATE2=NULL,  
									user_mod=(select EMPLOYEE_ID from hr_employee where first_name='ZENON' and last_name ='BERNADOWSKI')
									WHERE address_mac='$m' AND customer_id='$sys_id';")or die(ibase_errmsg());
					
				if(ibase_affected_rows($db)!=1){
					ibase_rollback($t);
					return false;
				}
				else ibase_commit($t);
			}
			ibase_close($db);
			return true;
	}
	
	
	function ont_ifindex( $olt ) {
	
		$iface = shell_exec( "snmpwalk -v 2c -c nfh3iuqowhk4 ".$olt. 
				" IF-MIB::ifName | grep GPON | sed -e 's/IF-MIB::ifName.//' | sed -e 's/ = STRING: GPON / /'");
		$iface = str_replace( array ( "\n", " " ), "|", $iface );
		
		return substr( $iface, 0, strlen( $iface )-1 );
		
	}
	function ont_ifalias( $olt, $oid_iface ) {
	
		$alias = shell_exec( "snmpget -v 2c -Ir -Ovq -c nfh3iuqowhk4 ".$olt. 
				" IF-MIB::ifAlias.".$oid_iface );
		return $alias;
		
	}
	function ont_id( $olt, $oid_iface ) {
	
		$id = shell_exec( "snmpwalk -v 2c -Oq -c nfh3iuqowhk4 ".$olt. 
			" .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.".$oid_iface.
			" | sed -e 's/SNMPv2-SMI::enterprises.2011.6.128.1.1.2.43.1.10."
			.$oid_iface.".//;s/ .*//'" );
			
		$id = explode( "\n",rtrim($id) );
		if ( $id[0] == "No" ) return 0;
		else {
			foreach ( range(0,127) as $n)
				if($n != $id[$n]) break;
			return $n++;
		}
	}
	function srv_p( $olt ) {
	
		$srv = shell_exec("snmpwalk -v 2c -Ovq -c nfh3iuqowhk4 ". $olt .
					" SNMPv2-SMI::enterprises.2011.5.14.3.1.1.1");
		$s = array();
		$srv = explode( "\n", rtrim( $srv ) );

		foreach ( range(1,32767) as $n ){
		
			if(! in_array( $n, $srv ) ) $s[] = $n;
			if( count( $s ) == 3) break;
			
		}
	
		return $s;
	}
	function l_port( $olt, $port ) {
	
		$p = shell_exec( "snmpwalk -v 2c -Ir -Oqv -c nfh3iuqowhk4 ".$olt.
					" IF-MIB::ifName.".$port." | sed -e 's/GPON //'" );
		$p = explode( "/", rtrim($p) );
		return $p;
		
	}
	function ont_hg824X_add($olt, $iface, $port, $llid, $descr, $sn, $catv, $vod, $vlan_vod, $inet, $ont_bwth){

		//echo $olt." ".$iface." ".$port." ".$llid." ".$descr." ".$sn." ".$catv." ".$vod." ".$vlan_vod." ".$inet." ".$ont_bwth;
	
		if($vod == 1)
			//---ont add 0 122 sn-auth "48575443011D3810" omci ont-lineprofile-id 13 ont-srvprofile-id 11 desc "VoD"
			$q="snmpset -v 2c -c nfh3iuqowhk4 ".$olt." .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.2.".$iface.".".$llid." i 1 .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.".
				$iface.".".$llid." x 0x".$sn." .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.6.".$iface.".".$llid." i 1 .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.7.".
				$iface.".".$llid." s LINE_PROFILE_4 .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.8.".$iface.".".
				$llid." s HG8240_VOD .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$iface.".".$llid." s ".$descr.
				" .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.".$iface.".".$llid.
				" i 4 .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.11.".$iface.".".$llid." i 2";
		else
			//---ont add 0 122 sn-auth "48575443011D3810" omci ont-lineprofile-id 12 ont-srvprofile-id 10 desc "STANDARD"
			$q="snmpset -v 2c -c nfh3iuqowhk4 ".$olt." .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.2.".$iface.".".$llid." i 1 .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.".
				$iface.".".$llid." x 0x".$sn." .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.6.".$iface.".".$llid." i 1 .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.7.".
				$iface.".".$llid." s LINE_PROFILE_3 .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.8.".$iface.".".
				$llid." s HG8240 .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$iface.".".$llid." s ".$descr.
				" .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.".$iface.".".$llid.
				" i 4 .1.3.6.1.4.1.2011.6.128.1.1.2.43.1.11.".$iface.".".$llid." i 2";
		
		//echo $q . "\n\n<br>";

		$r = shell_exec( $q );
		if( $r == "" ) { 
			echo "<b>ONT not added! Check your 
				parameters!</b><br>"; echo "aaa"; return; }
		else { echo "<b>ONT correctly added!</b><br>"; }

		//---ont port attribute 0 122 catv 1 operational-state on
		if($catv == 1){
			
			$q="snmpset -v 2c -c nfh3iuqowhk4 ".$olt." .1.3.6.1.4.1.2011.6.128.1.1.2.63.1.2.".$iface.".".$llid.".1 i 1";
			//echo $q . "\n\n<br>";
			
			$r = shell_exec( $q );
			if( $r == "" ) { 
				echo "<b>CATV attribute not added! 
				Check your parameters!</b><br>"; return; }
			else { echo "<b>CATV attribute correctly added!</b><br>"; }
		}
				
		//---service-port 254 vlan 204 gpon 0/1/0 ont 122 gemport 1 multi-service user-vlan 204 tag-transform translate inbound traffic-table index 10 outbound traffic-table index 10
		$s = $this->srv_p( $olt );
		//print_r($s);
		
		$p = $this->l_port( $olt, $iface );
		
		$q="snmpset -v 2c -c nfh3iuqowhk4 ".$olt." 		.1.3.6.1.4.1.2011.5.14.5.2.1.2.".$s[0]." i ".$p[0]." \
															.1.3.6.1.4.1.2011.5.14.5.2.1.3.".$s[0]." i ".$p[1]." \
															.1.3.6.1.4.1.2011.5.14.5.2.1.4.".$s[0]." i ".$p[2]." \
															.1.3.6.1.4.1.2011.5.14.5.2.1.5.".$s[0]." i ".$llid." \
															.1.3.6.1.4.1.2011.5.14.5.2.1.6.".$s[0]." i 1 \
															.1.3.6.1.4.1.2011.5.14.5.2.1.7.".$s[0]." i 4 \
															.1.3.6.1.4.1.2011.5.14.5.2.1.8.".$s[0]." i 204 \
															.1.3.6.1.4.1.2011.5.14.5.2.1.11.".$s[0]." i 1 \
															.1.3.6.1.4.1.2011.5.14.5.2.1.12.".$s[0]." i 204 \
															.1.3.6.1.4.1.2011.5.14.5.2.1.15.".$s[0]." i 4 \
															.1.3.6.1.4.1.2011.5.14.5.2.1.21.".$s[0]." s 1Mbps__VOIP \
															.1.3.6.1.4.1.2011.5.14.5.2.1.22.".$s[0]." s 1Mbps__VOIP";
															
															//.1.3.6.1.4.1.2011.5.14.5.2.1.21.".$s[0]." s 1Mbps__INET \
															//.1.3.6.1.4.1.2011.5.14.5.2.1.22.".$s[0]." s 1Mbps__INET";

		//echo $q . "\n\n<br>";
															
		$r = shell_exec( $q );
		if( $r == "" ) { 
			echo "<b>Service port (MGMT&VOIP interface) not added! 
			Check your parameters!</b><br>"; return; }
		else { echo "<b>Service port (MGMT&VOIP interface) added!</b><br>"; }

		//---ont port native-vlan 0 122 eth 1 vlan 205 priority 0
		if( $inet == 1 ) {
			
			$b = explode ( "/", $ont_bwth );
			
			$q="snmpset -v 2c -c nfh3iuqowhk4 ".$olt." .1.3.6.1.4.1.2011.6.128.1.1.2.62.1.7.".$iface.".".$llid.".1 i 205 .1.3.6.1.4.1.2011.6.128.1.1.2.62.1.8.".$iface.".".$llid.".1 i 0 ";
			//echo $q."<br>";
			$r = shell_exec( $q );
			if( $r == "" ) { 
				echo "<b>Mapping port ETH1 on ONT not added! 
				Check your parameters!</b><br>"; return; }
			else { echo "<b>Mapping port ETH1 on ONT added!</b><br>"; }

			//---service-port 255 vlan 205 gpon 0/1/0 ont 122 gemport 2 multi-service user-vlan 205 tag-transform translate inbound traffic-table index 150 outbound traffic-table index 18
			$q="snmpset -v 2c -c nfh3iuqowhk4 ".$olt." 		.1.3.6.1.4.1.2011.5.14.5.2.1.2.".$s[1]." i ".$p[0]." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.3.".$s[1]." i ".$p[1]." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.4.".$s[1]." i ".$p[2]." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.5.".$s[1]." i ".$llid." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.6.".$s[1]." i 2 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.7.".$s[1]." i 4 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.8.".$s[1]." i 205 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.11.".$s[1]." i 1 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.12.".$s[1]." i 205 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.15.".$s[1]." i 4 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.21.".$s[1]." s ".$b[0]."Mbps__INET \
																.1.3.6.1.4.1.2011.5.14.5.2.1.22.".$s[1]." s ".$b[1]."Mbps__INET";
			
			//echo $q . "\n\n<br>";
			
			$r = shell_exec( $q );
			if( $r == "" ) { 
				echo "<b>Service port (INET interface) not added! 
				Check your parameters!</b><br>"; return; }
			else { echo "<b>Service port (INET interface) added!</b><br>"; }
			
		}
		
		//---ont port native-vlan 0 122 eth 4 vlan 207 priority 3
		if( $vod == 1 ) {
			
			$q="snmpset -v 2c -c nfh3iuqowhk4 ".$olt." .1.3.6.1.4.1.2011.6.128.1.1.2.62.1.7.".$iface.".".$llid.".4 i 207 .1.3.6.1.4.1.2011.6.128.1.1.2.62.1.8.".$iface.".".$llid.".4 i 3 ";
			//echo $q."<br>";
			$r = shell_exec( $q );
			if( $r == "" ) { 
				echo "<b>Mapping port ETH4 on ONT not added! 
				Check your parameters!</b><br>"; return; }
			else { echo "<b>Mapping port ETH4 on ONT added!</b><br>"; }

			//---service-port 800 vlan 207 gpon 0/2/7 ont 2 gemport 3 multi-service user-vlan 207 tag-transform translate inbound traffic-table index 9 outbound traffic-table index 9
			$q="snmpset -v 2c -c nfh3iuqowhk4 ".$olt." 		.1.3.6.1.4.1.2011.5.14.5.2.1.2.".$s[2]." i ".$p[0]." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.3.".$s[2]." i ".$p[1]." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.4.".$s[2]." i ".$p[2]." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.5.".$s[2]." i ".$llid." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.6.".$s[2]." i 3 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.7.".$s[2]." i 4 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.8.".$s[2]." i ".$vlan_vod." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.11.".$s[2]." i 1 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.12.".$s[2]." i ".$vlan_vod." \
																.1.3.6.1.4.1.2011.5.14.5.2.1.15.".$s[2]." i 4 \
																.1.3.6.1.4.1.2011.5.14.5.2.1.21.".$s[2]." s 2Mbps__VOD \
																.1.3.6.1.4.1.2011.5.14.5.2.1.22.".$s[2]." s 2Mbps__VOD";
			
			//echo $q . "\n\n<br>";
			
			$r = shell_exec( $q );
			if( $r == "" ) { 
				echo "<b>Service port (VOD interface) not added! 
				Check your parameters!</b><br>"; return; }
			else { echo "<b>Service port (VOD interface) added!</b><br>"; }
			
		}
		
	}
	
	function comment( $cmp_id, $comment ) { 
	
		$db = $this->conn( );
		$sql = "UPDATE cmp SET comment='".$comment."' WHERE cmp_id=".$cmp_id;

		$rs = $db->Execute( $sql ) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $sql<br>" );
		#print_r($sql);
		
		$this->disconn($db);
	}
	
	function get_comment( $cmp_id ) { 
	
		$db = $this->conn( );
		$sql = "SELECT * FROM cmp WHERE cmp_id=".$cmp_id;

		$rs = $db->Execute( $sql ) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $sql<br>" );
		$r = $rs->GetRows();
		$this->disconn($db);
		//print_r($r);
		return $r[0]['comment'];
	}
	
	function dev( $devt_id ) { 
	
		$db = $this->conn( );
		$sql = "SELECT dev.dev_id, dev.name 
				FROM dev 
				LEFT JOIN devt_dev ON devt_dev.dev_id = dev.dev_id 
				LEFT JOIN devt ON devt_dev.devt_id = devt.devt_id ";

		if ( $devt_id <> "" ) $sql .= "WHERE devt.devt_id=$devt_id";

		$rs = $db->Execute( $sql ) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $sql<br>" );
		$this->disconn( $db );
		$r=$rs->GetArray( );
		
		foreach ( $r as $k => $v )
			$str .= $v['dev_id']."|".$v['name']."|";
			
		return substr( $str, 0, strlen( $str )-1 );
		
	}
	
	function cmp_bwth( $dev_id, $viface_id ) {
		
		$db = $this->conn( );
		$sql = "SELECT * FROM cfg 
				LEFT JOIN bwth ON cfg.bwth_id = bwth.bwth_id 
				LEFT JOIN path ON cfg.path_id = path.path_id 
				WHERE 	
				dev_id = $dev_id AND 
				viface_id = $viface_id AND 
				cfg.state = 1 ORDER BY bwth.dl";
		
		$rs = $db->Execute( $sql ) or die( "Error in ".__FILE__." in line ".__LINE__." : ".mysql_errno().": "
				.mysql_error()." when executing: $sql<br>" );
		$r = $rs->GetArray( );
		
		foreach ( $r as $k => $v )
			$str .= $v['cfg_id']."|".$v['dl']."/".$v['ul']."|".$v['max_cpe']."|".$v['path']."|";
			
		$this->disconn($db);

		return substr( $str, 0, strlen( $str )-1 );
		
	}
	
	function cfg_xml( $sn, $callerid, $auth ) {

		$xml='<InternetGatewayDevice>
<Time DaylightSavingsEnd="2009-8-30T00:00:00" DaylightSavingsStart="2009-8-1T00:00:00" DaylightSavingsUsed="0" Enable="0" LocalTimeZone="+00:00" LocalTimeZoneName="Greenwich Mean Time: Dublin, Edinburgh, Lisbon, London" NTPServer1="clock.fmt.he.net" NTPServer2="clock.nyc.he.net" NTPServer3="" NTPServer4="" NTPServer5="" X_HW_DaylightSavingsEndDate="9/4/1/0/0/0" X_HW_DaylightSavingsStartDate="7/4/1/0/0/0" X_HW_SynInterval="360" X_HW_WanName=""/>
<UserInterface>
<X_HW_CLITelnetAccess Access="1"/>
<X_HW_WebUserInfo NumberOfInstances="1">
<X_HW_WebUserInfoInstance Enable="1" InstanceID="2" Password="admintelecom" UserLevel="0" UserName="telecomadmin"/>
</X_HW_WebUserInfo>
</UserInterface>
<ManagementServer ConnectionRequestPassword="acs" ConnectionRequestURL="" ConnectionRequestUsername="acs" DefaultActiveNotificationThrottle="0" DownloadProgressURL="" EnableCWMP="0" KickURL="" ManageableDeviceNotificationLimit="0" ManageableDeviceNumberOfEntries="0" NATDetected="0" ParameterKey="0" Password="hgw" PeriodicInformEnable="1" PeriodicInformInterval="43200" PeriodicInformTime="" STUNEnable="0" STUNMaximumKeepAlivePeriod="0" STUNMinimumKeepAlivePeriod="0" STUNPassword="" STUNServerAddress="" STUNServerPort="0" STUNUsername="" UDPConnectionRequestAddress="" UDPConnectionRequestAddressNotificationLimit="0" URL="http://acs" UpgradesManaged="0" Username="hgw" X_HW_CertPassword="test" X_HW_EnableCertificate="0"/>
<X_HW_PSIXmlReset ResetFlag="1"/>
<Optical>
<X_HW_Interface X_HW_OpmEnable="1"/>
</Optical>
<Services>
<VoiceService MaxNumber="1" NumberOfInstances="1">
<VoiceServiceInstance InstanceID="1" VoiceProfileNumberOfEntries="1">
<VoiceProfile MaxNumber="4" NumberOfInstance="1" NumberOfInstances="1">
<VoiceProfileInstance DigitMap="" DigitMapEnable="1" DTMFMethod="InBand" InstanceID="1" Name="SIP" Region="CN" SignalingProtocol="SIP" X_HW_DigitMapMatchMode="Min" X_HW_HowlerSendFlag="1" X_HW_OverseaVer="0" X_HW_PortName="wan1">
<SIP DSCPMark="0" InboundAuthPassword="" InboundAuthUsername="" Organization="" OutboundProxy="" OutboundProxyPort="" ProxyServer="46.238.98.207" ProxyServerPort="5060" ProxyServerTransport="UDP" RegisterRetryInterval="30" RegistrarServer="" RegistrarServerPort="5060" RegistrarServerTransport="UDP" RegistrationPeriod="600" SIPResponseMapNumberOfElements="0" TimerT1="500" TimerT2="4000" TimerT4="5000" UseCodecPriorityInSDPResponse="0" UserAgentDomain="" UserAgentPort="" UserAgentTransport="" VLANIDMark="" X_HW_802-1pMark="" X_HW_SecondaryOutboundProxy="" X_HW_SecondaryOutboundProxyPort="" X_HW_SecondaryProxyServer="" X_HW_SecondaryProxyServerPort="5060" X_HW_SecondaryProxyServerTransport="" X_HW_SecondaryRegistrarServer="" X_HW_SecondaryRegistrarServerPort="5060" X_HW_SecondaryRegistrarServerTransport="UDP">
<ResponseMap MaxNumber="600" NumberOfInstances="2">
<ResponseMapInstance InstanceID="1" SIPResponseNumber="100" Tone="0" X_HW_Duration="60"/>
<ResponseMapInstance InstanceID="2" SIPResponseNumber="493" Tone="1" X_HW_Duration="60"/>
</ResponseMap>
<X_HW_SIPProfile ProfileBody="1=4294967295;2=1;3=1;4=1;5=0;6=0;7=1;8=600;9=1;10=0;11=0;12=0;13=1;14=1;15=0;16=0;17=0;18=0;19=0;20=1;21=1;22=0;23=64;24=15;25=180;26=32;27=120;28=120;29=30;30=60;31=40;32=60;33=500;34=45;35=0;36=239;37=24575;38=532615;39=1039;40=33007;41=1025;42=1;43=2;44=4294967295;45=1;46=4294967295;47=0;48=4294967295;49=0;50=1;51=1;52=2;53=0;54=4294967295;55=0;56=0;57=1;58=0;59=0;60=0;61=2;62=500;63=10;64=20;65=3;66=3;67=6;68=4294967295;69=0;70=0;71=4294967295;72=2;73=4294967295;74=4294967295;75=4294967295;76=4294967295;77=4294967295;78=4294967295;79=4294967295;80=0;81=2;82=0;83=4294967295;84=1;85=1;86=3;87=1;88=180;89=10;90=4;91=20;92=30;93=30;94=180;95=4;96=90;97=30;98=6;99=4;100=120;101=100;102=30;103=30;104=4;105=10;106=10;107=16;108=8;109=2;110=0;111=1;112=2;113=2;114=1;115=2;116=1;117=0;118=0;119=1;120=0;121=1;122=2;123=0;124=0;125=0;126=0;127=0;128=0;129=0;130=0;131=0;132=0;133=1;134=0;135=0;136=0;137=1;138=0;139=0;140=0;141=1;142=1;143=0;144=0;145=1;146=1;147=2;148=1;149=0;150=36000;151=500;152=2;153=0;154=8194;155=0;156=0;157=50;158=0;159=0;160=0;161=1;162=1;163=0;164=0;165=0;166=1;167=1;168=0;169=0;170=0;171=0;172=180;173=90;174=0;175=0;176=0;177=30;178=0;179=0;180=0;181=1;182=0;183=0;184=1;185=1;186=2;187=60000;188=1;189=0;190=0;191=0;192=0;193=0;194=21600;195=0;196=0;197=0;198=0;199=0;200=0;201=0;202=0;203=4294967295;204=480;205=486;206=486;207=0;208=30;209=0;210=0;211=0;212=0;213=0;214=0;215=0;216=0;217=0;218=1;219=0;220=0;221=15;222=0;223=1;224=0;225=20;226=20;227=4;228=0;229=0;230=0;231=0;232=0;233=4294967295;234=0;235=0;236=0;237=0;238=1800;239=0;240=4294967295;241=0;242=1200;243=1;244=0;245=0;246=5;247=0;248=0" ProfileName=""/>
<X_HW_SIPDigitmap MaxNumber="3" NumberOfInstances="3">
<X_HW_SIPDigitmapInstance DigitMap="x.S|x.#" DigitMapLongTimer="10" DigitMapShortTimer="5" DigitMapStartTimer="20" DigitmapType="Normal" DMName="dmmNormal" InstanceID="1"/>
<X_HW_SIPDigitmapInstance DMName="dmmScc" DigitMap="(*xx#|#xx#|*#xx#|*xx*[x].#|#xx*[x].#|*#xx*[x].#|*xx*xxxx*[x].#|**xx)" DigitMapLongTimer="10" DigitMapShortTimer="5" DigitMapStartTimer="20" DigitmapType="SCC" InstanceID="2"/>
<X_HW_SIPDigitmapInstance DMName="dmmEmg" DigitMap="" DigitMapLongTimer="10" DigitMapShortTimer="5" DigitMapStartTimer="20" DigitmapType="Emergent" InstanceID="3"/>
</X_HW_SIPDigitmap>
<X_HW_SIPStringDefine MaxNumber="1024" NumberOfInstances="15">
<X_HW_SIPStringDefineInstance InstanceID="1" StringBody="connect.huawei.com"/>
<X_HW_SIPStringDefineInstance InstanceID="2" StringBody="visit.huawei.com"/>
<X_HW_SIPStringDefineInstance InstanceID="3" StringBody="0"/>
<X_HW_SIPStringDefineInstance InstanceID="4" StringBody="00"/>
<X_HW_SIPStringDefineInstance InstanceID="5" StringBody="86"/>
<X_HW_SIPStringDefineInstance InstanceID="6" StringBody="755"/>
<X_HW_SIPStringDefineInstance InstanceID="7" StringBody="default"/>
<X_HW_SIPStringDefineInstance InstanceID="8" StringBody="default"/>
<X_HW_SIPStringDefineInstance InstanceID="9" StringBody="default"/>
<X_HW_SIPStringDefineInstance InstanceID="10" StringBody="default"/>
<X_HW_SIPStringDefineInstance InstanceID="11" StringBody="default"/>
<X_HW_SIPStringDefineInstance InstanceID="12" StringBody="null"/>
<X_HW_SIPStringDefineInstance InstanceID="13" StringBody="HUAWEI-EchoLife %M%/%V%"/>
<X_HW_SIPStringDefineInstance InstanceID="14" StringBody="unsubscribe"/>
<X_HW_SIPStringDefineInstance InstanceID="15" StringBody="*39#"/>
</X_HW_SIPStringDefine>
<X_HW_SIPSrvPri MaxNumber="18" NumberOfInstances="17">
<X_HW_SIPSrvPriInstance InstanceID="1" ServiceID="4"/>
<X_HW_SIPSrvPriInstance InstanceID="2" ServiceID="5"/>
<X_HW_SIPSrvPriInstance InstanceID="3" ServiceID="3"/>
<X_HW_SIPSrvPriInstance InstanceID="4" ServiceID="2"/>
<X_HW_SIPSrvPriInstance InstanceID="5" ServiceID="1"/>
<X_HW_SIPSrvPriInstance InstanceID="6" ServiceID="0"/>
<X_HW_SIPSrvPriInstance InstanceID="7" ServiceID="6"/>
<X_HW_SIPSrvPriInstance InstanceID="8" ServiceID="7"/>
<X_HW_SIPSrvPriInstance InstanceID="9" ServiceID="8"/>
<X_HW_SIPSrvPriInstance InstanceID="10" ServiceID="9"/>
<X_HW_SIPSrvPriInstance InstanceID="11" ServiceID="10"/>
<X_HW_SIPSrvPriInstance InstanceID="12" ServiceID="11"/>
<X_HW_SIPSrvPriInstance InstanceID="13" ServiceID="12"/>
<X_HW_SIPSrvPriInstance InstanceID="14" ServiceID="13"/>
<X_HW_SIPSrvPriInstance InstanceID="15" ServiceID="14"/>
<X_HW_SIPSrvPriInstance InstanceID="16" ServiceID="15"/>
<X_HW_SIPSrvPriInstance InstanceID="17" ServiceID="16"/>
</X_HW_SIPSrvPri>
<X_HW_SIPSrvLogic MaxNumber="1024" NumberOfInstances="327">
<X_HW_SIPSrvLogicInstance InstanceID="1" SrvLogicBody="SN=0,SS=0,LS=1,EVT=16,OSS=8,OEVT=18:21:22:26,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="2" SrvLogicBody="SN=0,SS=1,LS=3,EVT=16,OSS=8,OEVT=18:21:22:26:25,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="3" SrvLogicBody="SN=0,SS=1,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="4" SrvLogicBody="SN=0,SS=1,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="5" SrvLogicBody="SN=0,SS=1,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="6" SrvLogicBody="SN=0,SS=1,LS=8,EVT=16,OSS=8,OEVT=18:21:22:24,ACT=57,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="7" SrvLogicBody="SN=0,SS=1,LS=8,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="8" SrvLogicBody="SN=0,SS=1,LS=8,EVT=24,OSS=0,OEVT=16:17,ACT=25:19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="9" SrvLogicBody="SN=0,SS=2,LS=2,EVT=15,OSS=10,OEVT=18:16:29:27:26,ACT=13:41:46:38,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="10" SrvLogicBody="SN=0,SS=2,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:28:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="11" SrvLogicBody="SN=0,SS=2,LS=2,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:28:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="12" SrvLogicBody="SN=0,SS=2,LS=2,EVT=20,OSS=9,OEVT=18:16:23:26,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="13" SrvLogicBody="SN=0,SS=2,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="14" SrvLogicBody="SN=0,SS=3,LS=3,EVT=0,OSS=0,OEVT=16:17,ACT=12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="15" SrvLogicBody="SN=0,SS=3,LS=3,EVT=1,OSS=0,OEVT=16:17,ACT=4:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="16" SrvLogicBody="SN=0,SS=3,LS=3,EVT=2,OSS=1,OEVT=18:16:25:26,ACT=40:37:45:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="17" SrvLogicBody="SN=0,SS=3,LS=3,EVT=3,OSS=13,OEVT=18:29:25:26:24,ACT=40:17:35,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="18" SrvLogicBody="SN=0,SS=3,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:28:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="19" SrvLogicBody="SN=0,SS=3,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:28:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="20" SrvLogicBody="SN=0,SS=3,LS=3,EVT=20,OSS=9,OEVT=18:16:23:26:25,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="21" SrvLogicBody="SN=0,SS=3,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=23:28:4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="22" SrvLogicBody="SN=0,SS=3,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="23" SrvLogicBody="SN=0,SS=4,LS=8,EVT=6,OSS=7,OEVT=18:16:15:20:24,ACT=3,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="24" SrvLogicBody="SN=0,SS=4,LS=8,EVT=14,OSS=1,OEVT=18:24,ACT=58:13,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="25" SrvLogicBody="SN=0,SS=4,LS=8,EVT=16,OSS=1,OEVT=18:16:24,ACT=23:28:58,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="26" SrvLogicBody="SN=0,SS=4,LS=8,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="27" SrvLogicBody="SN=0,SS=4,LS=8,EVT=20,OSS=1,OEVT=18:16:24,ACT=58,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="28" SrvLogicBody="SN=0,SS=4,LS=8,EVT=24,OSS=0,OEVT=16:17,ACT=25:19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="29" SrvLogicBody="SN=0,SS=7,LS=8,EVT=15,OSS=14,OEVT=18:24:30:31,ACT=58:18,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="30" SrvLogicBody="SN=0,SS=7,LS=8,EVT=16,OSS=1,OEVT=18:16:24,ACT=23:28:58,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="31" SrvLogicBody="SN=0,SS=7,LS=8,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="32" SrvLogicBody="SN=0,SS=7,LS=8,EVT=20,OSS=9,OEVT=18:16:24:23,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="33" SrvLogicBody="SN=0,SS=7,LS=8,EVT=24,OSS=0,OEVT=16:17,ACT=25:19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="34" SrvLogicBody="SN=0,SS=8,LS=2,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="35" SrvLogicBody="SN=0,SS=8,LS=2,EVT=21,OSS=2,OEVT=18:16:15:20:26,ACT=3,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="36" SrvLogicBody="SN=0,SS=8,LS=2,EVT=22,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="37" SrvLogicBody="SN=0,SS=8,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="38" SrvLogicBody="SN=0,SS=8,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="39" SrvLogicBody="SN=0,SS=8,LS=3,EVT=21,OSS=3,OEVT=18:16:0:1:2:3:4:20:25:26,ACT=0,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="40" SrvLogicBody="SN=0,SS=8,LS=3,EVT=22,OSS=1,OEVT=18:16:26:25,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="41" SrvLogicBody="SN=0,SS=8,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="42" SrvLogicBody="SN=0,SS=8,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="43" SrvLogicBody="SN=0,SS=8,LS=8,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="44" SrvLogicBody="SN=0,SS=8,LS=8,EVT=21,OSS=4,OEVT=18:14:6:16:20:24,ACT=2,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="45" SrvLogicBody="SN=0,SS=8,LS=8,EVT=22,OSS=1,OEVT=18:16:24,ACT=58,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="46" SrvLogicBody="SN=0,SS=8,LS=8,EVT=24,OSS=0,OEVT=16:17,ACT=25:19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="47" SrvLogicBody="SN=0,SS=9,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="48" SrvLogicBody="SN=0,SS=9,LS=2,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="49" SrvLogicBody="SN=0,SS=9,LS=2,EVT=23,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="50" SrvLogicBody="SN=0,SS=9,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="51" SrvLogicBody="SN=0,SS=9,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="52" SrvLogicBody="SN=0,SS=9,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="53" SrvLogicBody="SN=0,SS=9,LS=3,EVT=23,OSS=1,OEVT=18:16:25:26,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="54" SrvLogicBody="SN=0,SS=9,LS=3,EVT=25,OSS=0,OEVT=16:17,ACT=23:4:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="55" SrvLogicBody="SN=0,SS=9,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="56" SrvLogicBody="SN=0,SS=9,LS=8,EVT=16,OSS=1,OEVT=18:16:24,ACT=23:58,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="57" SrvLogicBody="SN=0,SS=9,LS=8,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="58" SrvLogicBody="SN=0,SS=9,LS=8,EVT=23,OSS=1,OEVT=18:16:24,ACT=58,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="59" SrvLogicBody="SN=0,SS=9,LS=8,EVT=24,OSS=0,OEVT=16:17,ACT=25:19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="60" SrvLogicBody="SN=0,SS=10,LS=3,EVT=29,OSS=1,OEVT=18:16:25:26,ACT=24,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="61" SrvLogicBody="SN=0,SS=10,LS=6,EVT=16,OSS=0,OEVT=16:17,ACT=6:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="62" SrvLogicBody="SN=0,SS=10,LS=6,EVT=18,OSS=11,OEVT=19:26:28,ACT=6:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="63" SrvLogicBody="SN=0,SS=10,LS=6,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="64" SrvLogicBody="SN=0,SS=10,LS=6,EVT=27,OSS=9,OEVT=18:16:23:26,ACT=6:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="65" SrvLogicBody="SN=0,SS=11,LS=2,EVT=19,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="66" SrvLogicBody="SN=0,SS=11,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=21:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="67" SrvLogicBody="SN=0,SS=11,LS=2,EVT=28,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="68" SrvLogicBody="SN=0,SS=13,LS=2,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="69" SrvLogicBody="SN=0,SS=13,LS=2,EVT=24,OSS=9,OEVT=18:16:26:23,ACT=25:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="70" SrvLogicBody="SN=0,SS=13,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=5:25,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="71" SrvLogicBody="SN=0,SS=13,LS=3,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="72" SrvLogicBody="SN=0,SS=13,LS=3,EVT=24,OSS=9,OEVT=18:16:25:26:23,ACT=25:36:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="73" SrvLogicBody="SN=0,SS=13,LS=3,EVT=25,OSS=13,OEVT=18:29:24:26,ACT=4:45,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="74" SrvLogicBody="SN=0,SS=13,LS=3,EVT=26,OSS=13,OEVT=18:29:24:26,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="75" SrvLogicBody="SN=0,SS=13,LS=8,EVT=29,OSS=1,OEVT=18:16:24,ACT=34:26:43,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="76" SrvLogicBody="SN=0,SS=14,LS=8,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="77" SrvLogicBody="SN=0,SS=14,LS=8,EVT=24,OSS=0,OEVT=16:17,ACT=25:19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="78" SrvLogicBody="SN=0,SS=14,LS=8,EVT=30,OSS=1,OEVT=18:16:24,ACT=254,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="79" SrvLogicBody="SN=0,SS=14,LS=8,EVT=31,OSS=1,OEVT=18:16:24,ACT=254,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="80" SrvLogicBody="SN=1,SS=0,LS=1,EVT=16,OSS=8,OEVT=18:21:22:26,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="81" SrvLogicBody="SN=1,SS=1,LS=3,EVT=16,OSS=8,OEVT=18:21:22:26:25,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="82" SrvLogicBody="SN=1,SS=1,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="83" SrvLogicBody="SN=1,SS=1,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="84" SrvLogicBody="SN=1,SS=1,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="85" SrvLogicBody="SN=1,SS=1,LS=7,EVT=16,OSS=3,OEVT=18:16:0:1:2:20:25:26,ACT=0,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="86" SrvLogicBody="SN=1,SS=1,LS=7,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="87" SrvLogicBody="SN=1,SS=1,LS=7,EVT=25,OSS=0,OEVT=16:17,ACT=4:44:36:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="88" SrvLogicBody="SN=1,SS=1,LS=7,EVT=26,OSS=0,OEVT=16:17,ACT=5:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="89" SrvLogicBody="SN=1,SS=2,LS=2,EVT=15,OSS=10,OEVT=18:16:29:27:26,ACT=13:41:46:38,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="90" SrvLogicBody="SN=1,SS=2,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:28:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="91" SrvLogicBody="SN=1,SS=2,LS=2,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:28:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="92" SrvLogicBody="SN=1,SS=2,LS=2,EVT=20,OSS=9,OEVT=18:16:23:26,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="93" SrvLogicBody="SN=1,SS=2,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="94" SrvLogicBody="SN=1,SS=3,LS=3,EVT=0,OSS=0,OEVT=16:17,ACT=12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="95" SrvLogicBody="SN=1,SS=3,LS=3,EVT=1,OSS=0,OEVT=16:17,ACT=4:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="96" SrvLogicBody="SN=1,SS=3,LS=3,EVT=2,OSS=1,OEVT=18:16:25:26,ACT=40:37:45:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="97" SrvLogicBody="SN=1,SS=3,LS=3,EVT=3,OSS=1,OEVT=18:16:25:26,ACT=16,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="98" SrvLogicBody="SN=1,SS=3,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:28:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="99" SrvLogicBody="SN=1,SS=3,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:28:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="100" SrvLogicBody="SN=1,SS=3,LS=3,EVT=20,OSS=9,OEVT=18:16:23:26:25,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="101" SrvLogicBody="SN=1,SS=3,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=23:28:4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="102" SrvLogicBody="SN=1,SS=3,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="103" SrvLogicBody="SN=1,SS=3,LS=7,EVT=0,OSS=0,OEVT=16:17,ACT=28:5:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="104" SrvLogicBody="SN=1,SS=3,LS=7,EVT=1,OSS=0,OEVT=16:17,ACT=28:4:44:36:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="105" SrvLogicBody="SN=1,SS=3,LS=7,EVT=2,OSS=1,OEVT=18:16:25:26,ACT=28:14:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="106" SrvLogicBody="SN=1,SS=3,LS=7,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:28,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="107" SrvLogicBody="SN=1,SS=3,LS=7,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="108" SrvLogicBody="SN=1,SS=3,LS=7,EVT=20,OSS=9,OEVT=18:16:26:25:23,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="109" SrvLogicBody="SN=1,SS=3,LS=7,EVT=25,OSS=0,OEVT=16:17,ACT=23:28:4:44:36:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="110" SrvLogicBody="SN=1,SS=3,LS=7,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:5:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="111" SrvLogicBody="SN=1,SS=8,LS=2,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="112" SrvLogicBody="SN=1,SS=8,LS=2,EVT=21,OSS=2,OEVT=18:16:15:20:26,ACT=3,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="113" SrvLogicBody="SN=1,SS=8,LS=2,EVT=22,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="114" SrvLogicBody="SN=1,SS=8,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="115" SrvLogicBody="SN=1,SS=8,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="116" SrvLogicBody="SN=1,SS=8,LS=3,EVT=21,OSS=3,OEVT=18:16:0:1:2:3:4:20:25:26,ACT=0,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="117" SrvLogicBody="SN=1,SS=8,LS=3,EVT=22,OSS=1,OEVT=18:16:26:25,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="118" SrvLogicBody="SN=1,SS=8,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="119" SrvLogicBody="SN=1,SS=8,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="120" SrvLogicBody="SN=1,SS=9,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="121" SrvLogicBody="SN=1,SS=9,LS=2,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="122" SrvLogicBody="SN=1,SS=9,LS=2,EVT=23,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="123" SrvLogicBody="SN=1,SS=9,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="124" SrvLogicBody="SN=1,SS=9,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="125" SrvLogicBody="SN=1,SS=9,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="126" SrvLogicBody="SN=1,SS=9,LS=3,EVT=23,OSS=1,OEVT=18:16:25:26,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="127" SrvLogicBody="SN=1,SS=9,LS=3,EVT=25,OSS=0,OEVT=16:17,ACT=23:4:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="128" SrvLogicBody="SN=1,SS=9,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="129" SrvLogicBody="SN=1,SS=9,LS=7,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="130" SrvLogicBody="SN=1,SS=9,LS=7,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="131" SrvLogicBody="SN=1,SS=9,LS=7,EVT=23,OSS=1,OEVT=18:16:25:26,ACT=254,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="132" SrvLogicBody="SN=1,SS=9,LS=7,EVT=25,OSS=0,OEVT=16:17,ACT=23:4:44:36:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="133" SrvLogicBody="SN=1,SS=9,LS=7,EVT=26,OSS=0,OEVT=16:17,ACT=23:5:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="134" SrvLogicBody="SN=1,SS=10,LS=3,EVT=29,OSS=1,OEVT=18:16:25:26,ACT=24,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="135" SrvLogicBody="SN=1,SS=10,LS=6,EVT=16,OSS=0,OEVT=16:17,ACT=6:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="136" SrvLogicBody="SN=1,SS=10,LS=6,EVT=18,OSS=11,OEVT=19:26:28,ACT=6:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="137" SrvLogicBody="SN=1,SS=10,LS=6,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="138" SrvLogicBody="SN=1,SS=10,LS=6,EVT=27,OSS=9,OEVT=18:16:23:26,ACT=6:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="139" SrvLogicBody="SN=1,SS=11,LS=2,EVT=19,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="140" SrvLogicBody="SN=1,SS=11,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=21:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="141" SrvLogicBody="SN=1,SS=11,LS=2,EVT=28,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="142" SrvLogicBody="SN=2,SS=0,LS=1,EVT=16,OSS=8,OEVT=18:21:22:26,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="143" SrvLogicBody="SN=2,SS=1,LS=3,EVT=16,OSS=8,OEVT=18:21:22:26:25,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="144" SrvLogicBody="SN=2,SS=1,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="145" SrvLogicBody="SN=2,SS=1,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="146" SrvLogicBody="SN=2,SS=1,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="147" SrvLogicBody="SN=2,SS=2,LS=2,EVT=15,OSS=10,OEVT=18:16:29:27:26,ACT=13:41:46:38,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="148" SrvLogicBody="SN=2,SS=2,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:28:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="149" SrvLogicBody="SN=2,SS=2,LS=2,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:28:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="150" SrvLogicBody="SN=2,SS=2,LS=2,EVT=20,OSS=9,OEVT=18:16:23:26,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="151" SrvLogicBody="SN=2,SS=2,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="152" SrvLogicBody="SN=2,SS=3,LS=3,EVT=4,OSS=12,OEVT=18:30:31:26:25,ACT=15,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="153" SrvLogicBody="SN=2,SS=3,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:28:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="154" SrvLogicBody="SN=2,SS=3,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:28:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="155" SrvLogicBody="SN=2,SS=3,LS=3,EVT=20,OSS=9,OEVT=18:16:23:26:25,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="156" SrvLogicBody="SN=2,SS=3,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=23:28:4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="157" SrvLogicBody="SN=2,SS=3,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="158" SrvLogicBody="SN=2,SS=8,LS=2,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="159" SrvLogicBody="SN=2,SS=8,LS=2,EVT=21,OSS=2,OEVT=18:16:15:20:26,ACT=3,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="160" SrvLogicBody="SN=2,SS=8,LS=2,EVT=22,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="161" SrvLogicBody="SN=2,SS=8,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="162" SrvLogicBody="SN=2,SS=8,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="163" SrvLogicBody="SN=2,SS=8,LS=3,EVT=21,OSS=3,OEVT=18:16:0:1:2:3:4:20:25:26,ACT=0,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="164" SrvLogicBody="SN=2,SS=8,LS=3,EVT=22,OSS=1,OEVT=18:16:26:25,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="165" SrvLogicBody="SN=2,SS=8,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="166" SrvLogicBody="SN=2,SS=8,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="167" SrvLogicBody="SN=2,SS=9,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="168" SrvLogicBody="SN=2,SS=9,LS=2,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="169" SrvLogicBody="SN=2,SS=9,LS=2,EVT=23,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="170" SrvLogicBody="SN=2,SS=9,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="171" SrvLogicBody="SN=2,SS=9,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="172" SrvLogicBody="SN=2,SS=9,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="173" SrvLogicBody="SN=2,SS=9,LS=3,EVT=23,OSS=1,OEVT=18:16:25:26,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="174" SrvLogicBody="SN=2,SS=9,LS=3,EVT=25,OSS=0,OEVT=16:17,ACT=23:4:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="175" SrvLogicBody="SN=2,SS=9,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="176" SrvLogicBody="SN=2,SS=10,LS=3,EVT=29,OSS=1,OEVT=18:16:25:26,ACT=24,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="177" SrvLogicBody="SN=2,SS=10,LS=6,EVT=16,OSS=0,OEVT=16:17,ACT=6:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="178" SrvLogicBody="SN=2,SS=10,LS=6,EVT=18,OSS=11,OEVT=19:26:28,ACT=6:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="179" SrvLogicBody="SN=2,SS=10,LS=6,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="180" SrvLogicBody="SN=2,SS=10,LS=6,EVT=27,OSS=9,OEVT=18:16:23:26,ACT=6:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="181" SrvLogicBody="SN=2,SS=11,LS=2,EVT=19,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="182" SrvLogicBody="SN=2,SS=11,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=21:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="183" SrvLogicBody="SN=2,SS=11,LS=2,EVT=28,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="184" SrvLogicBody="SN=2,SS=12,LS=0,EVT=18,OSS=0,OEVT=16:17,ACT=254,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="185" SrvLogicBody="SN=2,SS=12,LS=0,EVT=30,OSS=0,OEVT=16:17,ACT=254,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="186" SrvLogicBody="SN=2,SS=12,LS=0,EVT=31,OSS=0,OEVT=16:17,ACT=254,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="187" SrvLogicBody="SN=2,SS=12,LS=2,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="188" SrvLogicBody="SN=2,SS=12,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="189" SrvLogicBody="SN=2,SS=12,LS=2,EVT=30,OSS=0,OEVT=16:17,ACT=5:29,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="190" SrvLogicBody="SN=2,SS=12,LS=2,EVT=31,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="191" SrvLogicBody="SN=2,SS=12,LS=3,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="192" SrvLogicBody="SN=2,SS=12,LS=3,EVT=25,OSS=12,OEVT=26:30:31:18,ACT=4:45:37,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="193" SrvLogicBody="SN=2,SS=12,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="194" SrvLogicBody="SN=2,SS=12,LS=3,EVT=30,OSS=0,OEVT=16:17,ACT=4:5:29,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="195" SrvLogicBody="SN=2,SS=12,LS=3,EVT=31,OSS=9,OEVT=18:16:23:26:25,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="196" SrvLogicBody="SN=3,SS=0,LS=1,EVT=16,OSS=8,OEVT=18:21:22:26,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="197" SrvLogicBody="SN=3,SS=1,LS=3,EVT=16,OSS=8,OEVT=18:21:22:26:25,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="198" SrvLogicBody="SN=3,SS=1,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="199" SrvLogicBody="SN=3,SS=1,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="200" SrvLogicBody="SN=3,SS=1,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="201" SrvLogicBody="SN=3,SS=2,LS=2,EVT=15,OSS=10,OEVT=18:16:29:27:26,ACT=13:41:46:38,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="202" SrvLogicBody="SN=3,SS=2,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:28:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="203" SrvLogicBody="SN=3,SS=2,LS=2,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:28:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="204" SrvLogicBody="SN=3,SS=2,LS=2,EVT=20,OSS=9,OEVT=18:16:23:26,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="205" SrvLogicBody="SN=3,SS=2,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="206" SrvLogicBody="SN=3,SS=3,LS=3,EVT=0,OSS=0,OEVT=16:17,ACT=12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="207" SrvLogicBody="SN=3,SS=3,LS=3,EVT=1,OSS=0,OEVT=16:17,ACT=4:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="208" SrvLogicBody="SN=3,SS=3,LS=3,EVT=2,OSS=1,OEVT=18:16:25:26,ACT=40:37:45:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="209" SrvLogicBody="SN=3,SS=3,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:28:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="210" SrvLogicBody="SN=3,SS=3,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:28:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="211" SrvLogicBody="SN=3,SS=3,LS=3,EVT=20,OSS=9,OEVT=18:16:23:26:25,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="212" SrvLogicBody="SN=3,SS=3,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=23:28:4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="213" SrvLogicBody="SN=3,SS=3,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="214" SrvLogicBody="SN=3,SS=8,LS=2,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="215" SrvLogicBody="SN=3,SS=8,LS=2,EVT=21,OSS=2,OEVT=18:16:15:20:26,ACT=3,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="216" SrvLogicBody="SN=3,SS=8,LS=2,EVT=22,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="217" SrvLogicBody="SN=3,SS=8,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="218" SrvLogicBody="SN=3,SS=8,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="219" SrvLogicBody="SN=3,SS=8,LS=3,EVT=21,OSS=3,OEVT=18:16:0:1:2:3:4:20:25:26,ACT=0,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="220" SrvLogicBody="SN=3,SS=8,LS=3,EVT=22,OSS=1,OEVT=18:16:26:25,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="221" SrvLogicBody="SN=3,SS=8,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="222" SrvLogicBody="SN=3,SS=8,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="223" SrvLogicBody="SN=3,SS=9,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="224" SrvLogicBody="SN=3,SS=9,LS=2,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="225" SrvLogicBody="SN=3,SS=9,LS=2,EVT=23,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="226" SrvLogicBody="SN=3,SS=9,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="227" SrvLogicBody="SN=3,SS=9,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="228" SrvLogicBody="SN=3,SS=9,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="229" SrvLogicBody="SN=3,SS=9,LS=3,EVT=23,OSS=1,OEVT=18:16:25:26,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="230" SrvLogicBody="SN=3,SS=9,LS=3,EVT=25,OSS=0,OEVT=16:17,ACT=23:4:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="231" SrvLogicBody="SN=3,SS=9,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="232" SrvLogicBody="SN=3,SS=10,LS=3,EVT=29,OSS=1,OEVT=18:16:25:26,ACT=24,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="233" SrvLogicBody="SN=3,SS=10,LS=6,EVT=16,OSS=0,OEVT=16:17,ACT=6:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="234" SrvLogicBody="SN=3,SS=10,LS=6,EVT=18,OSS=11,OEVT=19:26:28,ACT=6:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="235" SrvLogicBody="SN=3,SS=10,LS=6,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="236" SrvLogicBody="SN=3,SS=10,LS=6,EVT=27,OSS=9,OEVT=18:16:23:26,ACT=6:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="237" SrvLogicBody="SN=3,SS=11,LS=2,EVT=19,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="238" SrvLogicBody="SN=3,SS=11,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=21:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="239" SrvLogicBody="SN=3,SS=11,LS=2,EVT=28,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="240" SrvLogicBody="SN=4,SS=0,LS=1,EVT=16,OSS=8,OEVT=18:21:22:26,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="241" SrvLogicBody="SN=4,SS=1,LS=3,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="242" SrvLogicBody="SN=4,SS=1,LS=3,EVT=25,OSS=0,OEVT=16:17,ACT=5:4,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="243" SrvLogicBody="SN=4,SS=1,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="244" SrvLogicBody="SN=4,SS=2,LS=2,EVT=14,OSS=10,OEVT=18:27:26:29,ACT=13:41:46:38,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="245" SrvLogicBody="SN=4,SS=2,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:28:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="246" SrvLogicBody="SN=4,SS=2,LS=2,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="247" SrvLogicBody="SN=4,SS=2,LS=2,EVT=20,OSS=9,OEVT=18:23:26:16,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="248" SrvLogicBody="SN=4,SS=2,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="249" SrvLogicBody="SN=4,SS=8,LS=2,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="250" SrvLogicBody="SN=4,SS=8,LS=2,EVT=21,OSS=2,OEVT=18:16:14:20:26,ACT=1,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="251" SrvLogicBody="SN=4,SS=8,LS=2,EVT=22,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="252" SrvLogicBody="SN=4,SS=8,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="253" SrvLogicBody="SN=4,SS=9,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="254" SrvLogicBody="SN=4,SS=9,LS=2,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="255" SrvLogicBody="SN=4,SS=9,LS=2,EVT=23,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="256" SrvLogicBody="SN=4,SS=9,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="257" SrvLogicBody="SN=4,SS=10,LS=3,EVT=29,OSS=1,OEVT=18:25:26,ACT=24,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="258" SrvLogicBody="SN=4,SS=10,LS=6,EVT=18,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="259" SrvLogicBody="SN=4,SS=10,LS=6,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="260" SrvLogicBody="SN=4,SS=10,LS=6,EVT=27,OSS=0,OEVT=16:17,ACT=5:6,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="261" SrvLogicBody="SN=5,SS=0,LS=5,EVT=17,OSS=1,OEVT=18:16:25:28:27,ACT=7,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="262" SrvLogicBody="SN=5,SS=1,LS=3,EVT=16,OSS=8,OEVT=18:21:22:26:25,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="263" SrvLogicBody="SN=5,SS=1,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="264" SrvLogicBody="SN=5,SS=1,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="265" SrvLogicBody="SN=5,SS=1,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="266" SrvLogicBody="SN=5,SS=1,LS=5,EVT=16,OSS=8,OEVT=18:21:22:27:26:28,ACT=9,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="267" SrvLogicBody="SN=5,SS=1,LS=5,EVT=18,OSS=11,OEVT=19:27:28,ACT=4:46:38:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="268" SrvLogicBody="SN=5,SS=1,LS=5,EVT=25,OSS=9,OEVT=18:16:23:27:28,ACT=4:46:38:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="269" SrvLogicBody="SN=5,SS=1,LS=5,EVT=27,OSS=0,OEVT=16:17,ACT=23:6,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="270" SrvLogicBody="SN=5,SS=1,LS=5,EVT=28,OSS=0,OEVT=16:17,ACT=6,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="271" SrvLogicBody="SN=5,SS=3,LS=3,EVT=0,OSS=0,OEVT=16:17,ACT=12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="272" SrvLogicBody="SN=5,SS=3,LS=3,EVT=1,OSS=0,OEVT=16:17,ACT=4:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="273" SrvLogicBody="SN=5,SS=3,LS=3,EVT=2,OSS=1,OEVT=18:16:25:26,ACT=40:37:45:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="274" SrvLogicBody="SN=5,SS=3,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:28:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="275" SrvLogicBody="SN=5,SS=3,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:28:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="276" SrvLogicBody="SN=5,SS=3,LS=3,EVT=20,OSS=9,OEVT=18:16:23:26:25,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="277" SrvLogicBody="SN=5,SS=3,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=23:28:4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="278" SrvLogicBody="SN=5,SS=3,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:28:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="279" SrvLogicBody="SN=5,SS=3,LS=6,EVT=0,OSS=0,OEVT=16:17,ACT=6:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="280" SrvLogicBody="SN=5,SS=3,LS=6,EVT=1,OSS=0,OEVT=16:17,ACT=5:38:46:8,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="281" SrvLogicBody="SN=5,SS=3,LS=6,EVT=2,OSS=1,OEVT=18:16:25:26,ACT=46:41:38:8,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="282" SrvLogicBody="SN=5,SS=3,LS=6,EVT=16,OSS=1,OEVT=18:16:25:27:28,ACT=23:28:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="283" SrvLogicBody="SN=5,SS=3,LS=6,EVT=18,OSS=11,OEVT=19:27:28,ACT=23:28:5:46:38:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="284" SrvLogicBody="SN=5,SS=3,LS=6,EVT=20,OSS=9,OEVT=18:16:23:27:26:28,ACT=22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="285" SrvLogicBody="SN=5,SS=3,LS=6,EVT=26,OSS=9,OEVT=18:16:23:27:28,ACT=23:28:5:46:38:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="286" SrvLogicBody="SN=5,SS=3,LS=6,EVT=27,OSS=0,OEVT=16:17,ACT=23:28:6:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="287" SrvLogicBody="SN=5,SS=3,LS=6,EVT=28,OSS=0,OEVT=16:17,ACT=23:28:6:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="288" SrvLogicBody="SN=5,SS=8,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="289" SrvLogicBody="SN=5,SS=8,LS=3,EVT=21,OSS=3,OEVT=18:16:0:1:2:3:4:20:25:26,ACT=0,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="290" SrvLogicBody="SN=5,SS=8,LS=3,EVT=22,OSS=1,OEVT=18:16:26:25,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="291" SrvLogicBody="SN=5,SS=8,LS=3,EVT=25,OSS=9,OEVT=18:16:23:26,ACT=4:45:37:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="292" SrvLogicBody="SN=5,SS=8,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="293" SrvLogicBody="SN=5,SS=8,LS=6,EVT=18,OSS=11,OEVT=19:27:28,ACT=5:46:38:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="294" SrvLogicBody="SN=5,SS=8,LS=6,EVT=21,OSS=3,OEVT=18:16:0:1:2:20:26:27:28,ACT=0,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="295" SrvLogicBody="SN=5,SS=8,LS=6,EVT=22,OSS=1,OEVT=18:16:27:25:28,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="296" SrvLogicBody="SN=5,SS=8,LS=6,EVT=26,OSS=9,OEVT=18:16:23:27:28,ACT=5:46:38:22,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="297" SrvLogicBody="SN=5,SS=8,LS=6,EVT=27,OSS=0,OEVT=16:17,ACT=6:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="298" SrvLogicBody="SN=5,SS=8,LS=6,EVT=28,OSS=0,OEVT=16:17,ACT=6:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="299" SrvLogicBody="SN=5,SS=9,LS=2,EVT=16,OSS=0,OEVT=16:17,ACT=23:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="300" SrvLogicBody="SN=5,SS=9,LS=2,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="301" SrvLogicBody="SN=5,SS=9,LS=2,EVT=23,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="302" SrvLogicBody="SN=5,SS=9,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=23:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="303" SrvLogicBody="SN=5,SS=9,LS=3,EVT=16,OSS=1,OEVT=18:16:25:26,ACT=23:12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="304" SrvLogicBody="SN=5,SS=9,LS=3,EVT=18,OSS=11,OEVT=19:26:28,ACT=23:4:45:37:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="305" SrvLogicBody="SN=5,SS=9,LS=3,EVT=23,OSS=1,OEVT=18:16:25:26,ACT=12,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="306" SrvLogicBody="SN=5,SS=9,LS=3,EVT=25,OSS=0,OEVT=16:17,ACT=23:4:45:37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="307" SrvLogicBody="SN=5,SS=9,LS=3,EVT=26,OSS=0,OEVT=16:17,ACT=23:12:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="308" SrvLogicBody="SN=5,SS=9,LS=4,EVT=16,OSS=0,OEVT=16:17,ACT=23:8,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="309" SrvLogicBody="SN=5,SS=9,LS=4,EVT=18,OSS=11,OEVT=19:27:28,ACT=23:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="310" SrvLogicBody="SN=5,SS=9,LS=4,EVT=23,OSS=0,OEVT=16:17,ACT=8,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="311" SrvLogicBody="SN=5,SS=9,LS=4,EVT=27,OSS=0,OEVT=16:17,ACT=23:6,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="312" SrvLogicBody="SN=5,SS=9,LS=4,EVT=28,OSS=0,OEVT=16:17,ACT=6,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="313" SrvLogicBody="SN=5,SS=9,LS=6,EVT=16,OSS=1,OEVT=18:16:25:28:27,ACT=23:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="314" SrvLogicBody="SN=5,SS=9,LS=6,EVT=18,OSS=11,OEVT=19:27:28,ACT=23:5:46:38:20,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="315" SrvLogicBody="SN=5,SS=9,LS=6,EVT=23,OSS=1,OEVT=18:16:25:28:27,ACT=37:11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="316" SrvLogicBody="SN=5,SS=9,LS=6,EVT=26,OSS=9,OEVT=18:16:23:27:28,ACT=5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="317" SrvLogicBody="SN=5,SS=9,LS=6,EVT=27,OSS=9,OEVT=18:23:26:16,ACT=6,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="318" SrvLogicBody="SN=5,SS=9,LS=6,EVT=28,OSS=9,OEVT=18:23:26:16,ACT=6,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="319" SrvLogicBody="SN=5,SS=11,LS=2,EVT=19,OSS=0,OEVT=16:17,ACT=11,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="320" SrvLogicBody="SN=5,SS=11,LS=2,EVT=26,OSS=0,OEVT=16:17,ACT=21:5,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="321" SrvLogicBody="SN=5,SS=11,LS=2,EVT=28,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="322" SrvLogicBody="SN=5,SS=11,LS=4,EVT=19,OSS=0,OEVT=16:17,ACT=8,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="323" SrvLogicBody="SN=5,SS=11,LS=4,EVT=27,OSS=0,OEVT=16:17,ACT=21:6,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="324" SrvLogicBody="SN=5,SS=11,LS=4,EVT=28,OSS=0,OEVT=16:17,ACT=19,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="325" SrvLogicBody="SN=12,SS=0,LS=4,EVT=19,OSS=40,OEVT=63:28,ACT=49:46:38,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="326" SrvLogicBody="SN=12,SS=40,LS=4,EVT=28,OSS=0,OEVT=16:17:19,ACT=23:28:51,TIMERLEN=0;"/>
<X_HW_SIPSrvLogicInstance InstanceID="327" SrvLogicBody="SN=12,SS=40,LS=4,EVT=63,OSS=0,OEVT=16:17:19,ACT=50,TIMERLEN=0;"/>
</X_HW_SIPSrvLogic>
<X_HW_SIPExtend AuthHeaderFoldingEnable="0" ConferenceFactoryUri="" EntityBasedSessionsTimerFlag="0" MaxForwards="70" PhoneContext="" PortFilterFlag="0" Server="" SoftwareParameters="" TimerMinSe="90" TimerSessionProgress="180" TimerSessionRelProgress="60" TimerTD="32000"/>
</SIP>
<X_HW_H248 CallAgent1="" CallAgent2="" CallAgentMID1="" CallAgentMID2="" CallAgentPort1="2944" CallAgentPort2="2944" DSCPMark="0" DeviceName="" Domain="" LocalPort="2944" MIDFormat="IP">
<auth HeaderSecurityType="md5" Rfc2833EncryptKey="" auth="0" authHeader="0" authInitKey="" authmgid=""/>
<StackInfo FixedRetransTime="2000" LongTimer="30000" MGProvisionalRespTime="8000" MTUValue="1500" MaxreTransDuration="30000" MaxreTransTime="4000" MaxreTransTimes="7" MinreTransTime="1000" MsgFormat="Short" MsgSegmentation="1" ResponseAckCtrl="0" RetransMode="Fixed" Retransfailoption="Times"/>
<Digitmap DMName="" DigitMap="" DigitMapLongTimer="10" DigitMapShortTimer="5" DigitMapStartTimer="20"/>
<Extend CallholdTimer="600" DisconnectToneType="BusyTone" HeartBeatRetransTimer="60" HeartBeatRetransTimes="3" HeartBeatTimer="600" MWD="60" MWDBaseTime="0" MgcSwitchMethod="SupportSwitch" PortFilterFlag="0" PortFilterTime="5" ProfileIndex="1" ProfileName="" ProfileNego="0" SoftwareParameters="" Topversion="2"/>
<Profile ProfileBody="0=0;1=2;2=1;3=0;4=0;5=0;6=0;7=0;8=0;9=0;10=0;11=1;12=0;13=0;14=0;15=0;16=0;17=0;18=0;19=0;20=0;21=0;22=0;23=0;24=0;25=0;26=0;27=0;28=0;29=0;30=0;31=0;32=0;33=0;34=0;35=0;36=0;37=0;38=0;39=0;40=1;41=16;42=0;43=0;44=0;45=0;46=0;47=2000;48=0;49=60;50=0;51=0;52=0;53=0;54=0;55=1;56=1;57=7;58=3;59=0;60=0;61=0;62=0;63=0;64=4;65=400;66=1;67=400;68=1;69=400;70=1;71=400;72=1;73=400;74=0;75=20;76=0;77=0;78=0;79=1;80=1;81=0;82=0;83=0;84=1;85=0;86=180;87=2;88=0;89=0;90=0;91=0;92=0;93=1;94=1;95=44;96=1;97=2;98=0;99=0;100=0;101=0;102=2000;103=0;104=60;105=60;106=0;107=0;108=0;109=0;110=500;111=0;112=0;113=65534;114=0;115=0;116=0;117=0;" ProfileName=""/>
</X_HW_H248>
<MGCP CallAgent1="" CallAgent2="" CallAgentPort1="2727" CallAgentPort2="2727" DSCPMark="0" Domain="" LocalPort="2427" MaxRetranCount="7" RegisterMode="Wildcard" RetranIntervalTimer="2">
<X_HW_MGCPExtend CallHoldFlag="0" DigitMapInterDigitTimer="4" HBWithMGNameFlag="0" HeartBeatClose="0" HeartBeatMaxreTransTimes="7" HeartBeatTimer="2" HowlToneTimeLength="60" LocalBlockToneType="BusyTone" MgcSwitchFlag="1" MgcType="0" RemoteBlockToneType="BusyTone"/>
<X_HW_MGCPStackInfo AtMostOnceTimer="30" IsSendProv="0" ProvTimer="5"/>
<X_HW_MGCPauth Rfc2833EncryptKey="" auth="Passive" authInitKey="" authKey="" authmgid=""/>
</MGCP>
<RTP DSCPMark="0" LocalPortMax="50032" LocalPortMin="50000" TelephoneEventPayloadType="97" VLANIDMark="" X_HW_2833FaxEventFlag="NoInitiativeStart" X_HW_2833FlashHook="0" X_HW_802-1pMark="" X_HW_PortName="">
<RTCP Enable="1" TxRepeatInterval="5000" X_HW_RtcpxrFlag="1" X_HW_VqmFlag="0"/>
<Redundancy Enable="0" PayloadType="96" X_HW_EnableAuto="0" X_HW_EnableFixedStart="0" X_HW_Rfc2198For2833="0"/>
<X_HW_JitBuffer IniAdaptJB="2" IniFixedJB="60" MaxAdaptJB="135" MaxFixedJB="135" MinAdaptJB="2" MinFixedJB="2"/>
<X_HW_Extend DefaultCodec="G.711ALaw" DefaultPktLen="20" EchoCancellationEnable="1" OffhookDtaLevel="-15.5dbm0" OffhookDtasAckFskInterval="50" OffhookDtasAckInterval="160" OffhookDtasDuration="80" OffhookFskLevel="-8.5dbm0" OffhookMarkSignalBit="80" OnhookChannelSeizeBit="300" OnhookDtaLevel="-8.5dbm0" OnhookDtasDuration="100" OnhookDtasFskInterval="250" OnhookFskLevel="-8.5dbm0" OnhookMarkSignalBit="180" PktLostThreshold="400" PlcFlag="0" RTPTermIDNumWidth="6" RTPTermIDPrefix="A100" RTPTermIDStartNum="0" SilenceSuppression="0"/>
</RTP>
<Tone>
<Event MaxNumber="60" NumberOfInstances="0"/>
<Description MaxNumber="200" NumberOfInstances="3">
<DescriptionInstance EntryID="101" InstanceID="1" ToneEnable="1" ToneFile="" ToneName="" TonePattern="1" ToneRepetitions="0"/>
<DescriptionInstance EntryID="102" InstanceID="2" ToneEnable="1" ToneFile="" ToneName="" TonePattern="3" ToneRepetitions="0"/>
<DescriptionInstance EntryID="1" InstanceID="3" ToneEnable="1" ToneFile="" ToneName="" TonePattern="5" ToneRepetitions="0"/>
</Description>
<TonePattern MaxNumber="200" NumberOfInstances="5">
<TonePatternInstance Duration="300" EntryID="1" Frequency1="300" Frequency2="0" Frequency3="0" Frequency4="0" InstanceID="1" NextEntryID="2" Power1="100" Power2="0" Power3="0" Power4="0" ToneOn="1"/>
<TonePatternInstance Duration="300" EntryID="2" Frequency1="300" Frequency2="0" Frequency3="0" Frequency4="0" InstanceID="2" NextEntryID="0" Power1="100" Power2="0" Power3="0" Power4="0" ToneOn="0"/>
<TonePatternInstance Duration="2000" EntryID="3" Frequency1="300" Frequency2="0" Frequency3="0" Frequency4="0" InstanceID="3" NextEntryID="4" Power1="100" Power2="0" Power3="0" Power4="0" ToneOn="1"/>
<TonePatternInstance Duration="2000" EntryID="4" Frequency1="300" Frequency2="0" Frequency3="0" Frequency4="0" InstanceID="4" NextEntryID="0" Power1="100" Power2="0" Power3="0" Power4="0" ToneOn="0"/>
<TonePatternInstance Duration="0" EntryID="5" Frequency1="400" Frequency2="0" Frequency3="0" Frequency4="0" InstanceID="5" NextEntryID="0" Power1="100" Power2="0" Power3="0" Power4="0" ToneOn="0"/>
</TonePattern>
</Tone>
<FaxT38 BitRate="0" Enable="0" HighSpeedRedundancy="3" LowSpeedRedundancy="3" TCFMethod="Network" X_HW_PortAdd2="0"/>
<X_HW_FaxModem FaxModemPktFix10ms="0" FaxModemVbdCodec="G.711ALaw" FaxModemVbdPTMode="Static" FaxModemVbdPayload="99" FaxNego="1" FaxNegoFlow="V3" ModemEventMode="Direct"/>
<X_HW_Ring>
<Mapping MaxNumber="16" NumberOfInstances="6">
<MappingInstance CadenceType="0" InitialRing="0" InstanceID="1" Pattern="0" RingName="alert-group"/>
<MappingInstance CadenceType="2" InitialRing="2" InstanceID="2" Pattern="1" RingName="alert-external"/>
<MappingInstance CadenceType="5" InitialRing="5" InstanceID="3" Pattern="2" RingName="alert-internal"/>
<MappingInstance CadenceType="3" InitialRing="3" InstanceID="4" Pattern="3" RingName=""/>
<MappingInstance CadenceType="0" InitialRing="0" InstanceID="5" Pattern="4" RingName=""/>
<MappingInstance CadenceType="6" InitialRing="6" InstanceID="6" Pattern="5" RingName=""/>
</Mapping>
<UserDefine MaxNumber="16" NumberOfInstances="1">
<UserDefineInstance InstanceID="1" RingPara1="0" RingPara2="0" RingPara3="0" RingPara4="0" RingPara5="0" RingPara6="0" RingType="32"/>
</UserDefine>
</X_HW_Ring>
<X_HW_Signal>
<List MaxNumber="128" NumberOfInstances="1">
<ListInstance InstanceID="1" SignalName="">
<Unit MaxNumber="4" NumberOfInstances="1">
<UnitInstance InstanceID="1" UnitDuration="4294967295" UnitEndCondition="-" UnitRepetitions="1" UnitStartCondition="-" UnitType=""/>
</Unit>
</ListInstance>
</List>
<Mapping MaxNumber="128" NumberOfInstances="1">
<MappingInstance InstanceID="1" Scene="" SignalName=""/>
</Mapping>
</X_HW_Signal>
<Line MaxNumber="10" NumberOfInstance="1" NumberOfInstances="1">
<LineInstance DirectoryNumber="';
	$xml.=$callerid;
$xml.='" Enable="Enabled" InstanceID="1" PhyReferenceList="1" X_HW_Priority="0">
<SIP AuthPassword="';
	$xml.=$auth;
$xml.='" AuthUserName="';
	$xml.=$callerid;
$xml.='" URI="">
<X_HW_Digitmap DMName="" DigitMap="" DigitMapLongTimer="10" DigitMapShortTimer="5" DigitMapStartTimer="20"/>
</SIP>
<X_HW_H248 LineName="A0"/>
<CallingFeatures CallForwardOnBusyEnable="0" CallForwardOnBusyNumber="" CallForwardOnNoAnswerEnable="0" CallForwardOnNoAnswerNumber="" CallForwardOnNoAnswerRingCount="10" CallForwardUnconditionalEnable="0" CallForwardUnconditionalNumber="" CallTransferEnable="1" CallWaitingEnable="1" CallerIDEnable="1" CallerIDName="" CallerIDNameEnable="0" MWIEnable="1" X_HW_3WayEnable="1" X_HW_CallHoldEnable="1" X_HW_CentrexDialSecondaryEnable="0" X_HW_CentrexPrefix="" X_HW_ConferenceEnable="1" X_HW_HotlineEnable="0" X_HW_HotlineNumber="" X_HW_HotlineTimer="5" X_HW_MCIDEnable="0" X_HW_MWIMode="Deferred"/>
<VoiceProcessing ReceiveGain="0" TransmitGain="0"/>
<Codec>
<List MaxNumber="4" NumberOfInstances="4">
<ListInstance Codecs="G.711MuLaw" Enable="1" InstanceID="1" PacketizationPeriod="20" Priority="1"/>
<ListInstance Codecs="G.711ALaw" Enable="1" InstanceID="2" PacketizationPeriod="20" Priority="2"/>
<ListInstance Codecs="G.729" Enable="1" InstanceID="3" PacketizationPeriod="20" Priority="3"/>
<ListInstance Codecs="G.722" Enable="1" InstanceID="4" PacketizationPeriod="20" Priority="4"/>
</List>
</Codec>
<MGCP LineName=""/>
</LineInstance>
</Line>
</VoiceProfileInstance>
</VoiceProfile>
<X_HW_DialSN FailSetToneId="101" InputToneId="" IsHaveAuthed="0" Prefix="**" SucessSetToneId="102" TimerCompleteInput="10" TimerWaitInput="10"/>
<X_HW_InnerCall Enable="0" Prefix="**123#"/>
<PhyInterface MaxNumber="4" NumberOfInstances="1">
<PhyInterfaceInstance InstanceID="1" InterfaceID="1">
<X_HW_IPSpc Codecs="G.711ALaw" EchoCancellationEnable="0" Enable="0" JbMode="Static" LocalTransport="" MediaMode="SendReceive" PacketizationPeriod="20" PortName="" RemoteIP="" RemoteTransport="" SilenceSuppression="0"/>
<X_HW_DspTemplate EchoCancellationEnable="0" Enable="0" JbMode="Static" NLP="Closed" SilenceSuppression="0" WorkMode="Voice"/>
<X_HW_Extend BellAnsEnable="0" CalledOffhookShakeTime="200" CallerOffhookShakeTime="80" ClipForceSendFsk="0" ClipFormat="Sdmf-fsk" ClipFskMode="BELL_202" ClipReversePole="0" ClipTasPattern="NO-TAS" ClipTransWhen="AfterRing" Current="25" CurrentOnPark="0" DCTime="100" DetectAnsbarBySingleToneEnable="0" DialMode="First" DialPulseBreakLowerLimit="30" DialPulseBreakUpperLimit="90" DialPulseInterval="240" DialPulseMakeLowerLimit="30" DialPulseMakeUpperLimit="90" DialPulsePeriodLowerLimit="50" DialPulsePeriodUpperLimit="200" FskTime="800" HookFlashDownTime="100" HookFlashUpTime="300" Impedance="2" KcHighLevel="100" KcLowLevel="300" KcType="16Kc" KcVoltage="0" MWIRingFlag="0" OnhookConfirmTime="0" ReceiveGain="-3.5db" ReversePoleOnAnswer="0" ReversePolePulse="1" ReversePolePulseLevel="300" RingFrequency="1" RingVoltage="0" SendGain="0db" ToneIdForNoLineConfig="1"/>
</PhyInterfaceInstance>
</PhyInterface>
<X_HW_LineTestThreshold Threshold0="55" Threshold1="40" Threshold10="700" Threshold11="200" Threshold12="350" Threshold13="50" Threshold2="20000" Threshold3="3000" Threshold4="100000" Threshold5="100" Threshold6="2000" Threshold7="2000" Threshold8="2000" Threshold9="20000"/>
<X_HW_RemoteCapServer LocalTransport="50100" PortName="" RemoteIP="" RemoteTransport="0"/>
<X_HW_SimulateTestParameters CalledNumberDialInterval="500" DialDTMFConfirmFailToneId="104" DialDTMFConfirmInputToneId="103" DialDTMFConfirmSucessToneId="105"/>
<X_HW_InnerParameters AutoResetInterfaceTimer="3000" DelayResetTimerOnExistCall="0" FskBindReceiveGainEnable="1" GracefulTimerOnCallClear="30" SendGainRelativeEnable="1" ToneBindReceiveGainEnable="0"/>
</VoiceServiceInstance>
</VoiceService>
<X_HW_AccessLimit Mode="Off" TotalTerminalNumber="0"/>
<X_HW_IPTV GenQueryInterval="125" GenResponseTime="100" IGMPEnable="0" ProxyEnable="0" Robustness="2" STBNumber="0" SnoopingEnable="1" SpQueryInterval="10" SpQueryNumber="2" SpResponseTime="10"/>
<X_HW_PortalManagement DefaultUrl="" Enable="0">
<TypePortal MaxNumber="3" NumberOfInstances="0"/>
</X_HW_PortalManagement>
</Services>
<WANDevice NumberOfInstances="1">
<WANDeviceInstance InstanceID="1" WANConnectionNumberOfEntries="1">
<WANConnectionDevice MaxNumber="4" NumberOfInstance="2" NumberOfInstances="2">
<WANConnectionDeviceInstance InstanceID="1" WANIPConnectionNumberOfEntries="1" WANPPPConnectionNumberOfEntries="0">
<WANIPConnection NumberOfInstances="1">
<WANIPConnectionInstance AddressingType="DHCP" ConnectionType="IP_Routed" DefaultGateway="" DNSEnabled="1" DNSServers="" Enable="1" ExternalIPAddress="" InstanceID="1" Name="VOIP" NATEnabled="0" SubnetMask="" X_HW_MultiCastVLAN="0xFFFFFFFF" X_HW_PRI="0" X_HW_SERVICELIST="VOIP" X_HW_VenderClassID="" X_HW_VLAN="204"/>
</WANIPConnection>
</WANConnectionDeviceInstance>
</WANConnectionDevice>
</WANDeviceInstance>
</WANDevice>
<LANDevice NumberOfInstances="1">
<LANDeviceInstance InstanceID="1">
<LANEthernetInterfaceConfig MaxNumber="4" NumberOfInstances="4">
<LANEthernetInterfaceConfigInstance InstanceID="1" X_HW_L3Enable="0"/>
<LANEthernetInterfaceConfigInstance InstanceID="2" X_HW_L3Enable="0"/>
<LANEthernetInterfaceConfigInstance InstanceID="3" X_HW_L3Enable="0"/>
<LANEthernetInterfaceConfigInstance InstanceID="4" X_HW_L3Enable="0"/>
</LANEthernetInterfaceConfig>
<LANHostConfigManagement X_HW_CameraEnd="" X_HW_CameraStart="" X_HW_ComputerEnd="" X_HW_ComputerStart="" X_HW_DHCPL2RelayEnable="1" X_HW_DHCPOption82Enable="0" X_HW_HGWEnd="" X_HW_HGWStart="" X_HW_PPPoEPITPEnable="0" X_HW_PhoneEnd="" X_HW_PhoneStart="" X_HW_STBEnd="" X_HW_STBStart=""/>
</LANDeviceInstance>
</LANDevice>
<X_HW_AmpInfo EthLoopbackTimeout="0">
<X_HW_Spec X_HW_EthTrapEnable="1" X_HW_HGDetectEnable="0" X_HW_HGVlan="3999">
<X_HW_EthSpec NumberOfInstances="4">
<X_HW_EthSpecInstance InstanceID="1" X_HW_HGDetectEnable="1"/>
<X_HW_EthSpecInstance InstanceID="2" X_HW_HGDetectEnable="1"/>
<X_HW_EthSpecInstance InstanceID="3" X_HW_HGDetectEnable="1"/>
<X_HW_EthSpecInstance InstanceID="4" X_HW_HGDetectEnable="1"/>
</X_HW_EthSpec>
</X_HW_Spec>
</X_HW_AmpInfo>
<X_HW_ALG FtpEnable="1" H323Enable="1" RTSPEnable="1" SipEnable="1" TftpEnable="1"/>
<X_HW_Security>
<AclServices FTPLanEnable="0" FTPWanEnable="0" HTTPLanEnable="1" HTTPWanEnable="1" TELNETLanEnable="1" TELNETWanEnable="0"/>
</X_HW_Security>
<Layer3Forwarding X_HW_AutoDefaultGatewayEnable="0" X_HW_WanDefaultWanName="">
<X_HW_policy_route MaxNumber="8" NumberOfInstances="0"/>
</Layer3Forwarding>
<X_HW_APMPolicy EnablePowerSavingMode="1">
<BatteryModePolicy NotUseCATVService="0" NotUseLanService="0" NotUseRemoteManagement="0" NotUseUsbService="0" NotUseVoiceService="0" NotUseWlanService="0"/>
<BatteryAlarmPolicy AlwaysEnable="1" VoiceServiceEnable=""/>
</X_HW_APMPolicy>
<X_HW_ARPPingDiagnostics MaxNumber="4" NumberOfInstances="0"/>
<DeviceInfo X_HW_UpPortMode="4">
<X_HW_Alarm/>
<X_HW_Monitor Enable="0" MonitorNumberOfEntries="0"/>
<X_HW_ReConnect Enable="1"/>
<X_HW_Syslog Enable="1" Level="3"/>
</DeviceInfo>
<QueueManagement Enable="0" X_HW_Mode="OTHER"/>
</InternetGatewayDevice>';
		
			$f="/tftpboot/HG824X/".$sn.".xml";
			$fp=fopen($f, "w"); 
			//flock($fp, 2); 
			fwrite($fp, $xml); 
			//flock($fp, 3); 
			fclose($fp); 
			$cmd='gzip /tftpboot/HG824X/'.$sn.'.xml';
			//echo system($cmd, $retval);
			echo exec ($cmd);
			//if($retval == false) echo "<b>Not create cfg file!</b><br>";
	}
	
}
?>