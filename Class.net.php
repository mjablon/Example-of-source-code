<?
require_once('adodb/adodb.inc.php');
require_once('Class.db.php');

class net extends db{
	function nett(){
		$db = $this->conn();
		$q = "SELECT * FROM net_type";
		$rs = $db->Execute($q) or die(mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function chckip($ip, $cmpt_id, $nett_id, $cmp_id){
		$db = $this->conn();
		$q = "SELECT cmp_id FROM cmp WHERE INET_ATON(ip) = INET_ATON('$ip')";
		$rs = $db->Execute($q) or die("Invalid count of ip: ".mysql_error());
		if($rs->NumRows() != 0) return 0;
		
		$q="SELECT dhcp_net.net_id 
			FROM cmp_net 
			LEFT JOIN dhcp_net ON cmp_net.net_id=dhcp_net.net_id 
			WHERE (cmp_net.cmp_id=$cmp_id AND nett_id=$nett_id AND cmpt_id=$cmpt_id)";
		$rs = $db->Execute($q) or die("Incorrect net_id form dhcp_net: ".mysql_error());
		$a1 = $rs->GetArray();
		if(empty($a1)) return 0;
		
		$is_correct=false;
		foreach ($a1 as $val) {
			$q = "SELECT net_id 
				  FROM dhcp_net 
				  WHERE net_id=".$val['net_id']." AND (INET_ATON(range_min) <= INET_ATON('$ip') 
				  AND INET_ATON(range_max) >= INET_ATON('$ip'))";
			$rs = $db->Execute($q) or die("Invalid ip of range: ".mysql_error());
			if($rs->NumRows() != 0) {$is_correct=true; break;}
		}
		$this->disconn($db);
		if($is_correct == true)
			return 1;
		else
			return 0;
	}
	function chcknet($ip, $cmpt_id, $nett_id, $cmp_id){
		$db = $this->conn();
		$q="SELECT dhcp_net.net_id 
			FROM cmp_net 
			LEFT JOIN dhcp_net ON cmp_net.net_id=dhcp_net.net_id 
			WHERE (cmp_net.cmp_id=$cmp_id AND nett_id=$nett_id AND cmpt_id=$cmpt_id)";
		$rs = $db->Execute($q) or die("Invalid net_id form dhcp_net: ".mysql_error());
		$a1 = $rs->GetArray();
		$net_id=0;
		if(empty($a1)) return $net_id;
		foreach ($a1 as $val) {
			$q = "SELECT * 
				FROM dhcp_net 
				WHERE (INET_ATON(range_min) <= INET_ATON('$ip') 
				AND INET_ATON(range_max) >= INET_ATON('$ip')) AND 
				net_id=".$val['net_id'];
			$rs = $db->Execute($q) or die("Incorrect ip of range: ".mysql_error());
			if($rs->NumRows() != 0){ $net_id=$val['net_id']; break;	}
		}
		$this->disconn($db);
		if($net_id != 0)
			return $net_id;
		else
			return 0;
	}
	function checknet2($ip, $cmpt_id, $nett_id){
	
		$db = $this->conn();
		$q = "SELECT net_id FROM dhcp_net WHERE 
			 (INET_ATON(range_min) <= INET_ATON('$ip') AND 
			 INET_ATON(range_max) >= INET_ATON('$ip')) AND 
			 ( nett_id=$nett_id AND cmpt_id=$cmpt_id)";
		$rs = $db->Execute($q) or die("Incorrect ip of range: ".mysql_error());
		$this->disconn($db);
		if($rs->NumRows() == 1) { $a = $rs->FetchRow(); return $a['net_id']; }
		else return 0;
	
	}
	function net_info(){
		$db = $this->conn();
		$rs = $db->Execute("SELECT net_id, auth, dhcp_net.comment, network, netmask, dhcp_net.state, cmp_type.name as cmp_type, net_type.name as net_type 
							FROM dhcp_net
							LEFT JOIN net_type ON dhcp_net.nett_id = net_type.nett_id
							LEFT JOIN cmp_type ON dhcp_net.cmpt_id = cmp_type.cmpt_id")
							or die("<center>Incorrect select from cfg_opt table! MYSQL error: ".mysql_error()."</center>\n");
		$this->disconn($db);
		return $rs->GetArray();
	}
	function net_opt($net_id){
		$db = $this->conn();
		$rs = $db->Execute("SELECT opt_id, dhcp_net.net_id, auth, dhcp_net.comment as comm1, dhcp_net_opt.comment as comm2, dhcp_net.nett_id, dhcp_net.cmpt_id, 
							network, netmask, gateway, range_min, range_max, broadcast, dhcp_net.state, opt_txt, cmp_type.name as cmp_type, net_type.name as net_type 
							FROM dhcp_net 
							LEFT JOIN dhcp_net_opt ON dhcp_net.net_id = dhcp_net_opt.net_id 
							LEFT JOIN net_type ON dhcp_net.nett_id = net_type.nett_id 
							LEFT JOIN cmp_type ON dhcp_net.cmpt_id = cmp_type.cmpt_id 
							WHERE dhcp_net.net_id='$net_id'")
							or die("<center>Incorrect select from cfg_opt table! MYSQL error: ".mysql_error()."</center>\n");
		$this->disconn($db);
		return $rs->GetArray();
	}
	function net_type_info($nett_id){
		$db = $this->conn();
		$rs = $db->Execute("SELECT * 
							FROM net_type 
							WHERE nett_id=$nett_id")
							or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function net_edit_type($nett_id, $name, $comment, $state){
		$db = $this->conn();
		$db->Execute("UPDATE net_type 
					  SET name='$name', comment='$comment', state='$state' 
					  WHERE nett_id=$nett_id")
					  or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
	}
	function net_del_type($nett_id){
		$db = $this->conn();
		$db->Execute("DELETE 
					  FROM net_type 
					  WHERE nett_id=$nett_id")
					  or die("MYSQL error: ".mysql_error());
			$this->disconn($db);
	}
	function net_add_type($name, $comm, $state){
		$db = $this->conn();
		$db->Execute("INSERT INTO net_type 
					  VALUES('', '$name', '$comm', '$state')")
					  or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
	}
	function add_net_opt($opt_id, $net_id, $comment, $opt_txt){
		$db = $this->conn();
		if($opt_id<>"")
			$db->Execute("INSERT dhcp_net_opt 
						  VALUES ($opt_id, $net_id, '$comment', '$opt_txt')")
						 or die(mysql_error());
		else
			$db->Execute("INSERT dhcp_net_opt 
						  VALUES ('', $net_id, '$comment', '$opt_txt')")
						  or die(mysql_error());
		$this->disconn($db);
	}
	function add_net(	$net_id, $auth, $state, $cmpt_id, $nett_id, $network, 
						$netmask, $gateway, $range_min, $range_max, $broadcast, $comment ){

		$db = $this->conn();
		$db->Execute("INSERT dhcp_net VALUES (	'$net_id', '$auth', '$nett_id', '$cmpt_id',
												'$comment', '$network', '$netmask', '$gateway',
												'$range_min', '$range_max', '$broadcast', '$state')")
							or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
		return $db->Insert_ID();
	}
	function del_net_opt($opt_id){
		$db = $this->conn();
		$db->Execute("DELETE 
					  FROM dhcp_net_opt 
					  WHERE opt_id = $opt_id")
							or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
	}
	function del_net($net_id){
		$db = $this->conn();
		if($db->Execute("DELETE 
						 FROM dhcp_net 
						 WHERE net_id = $net_id")
							or die("Błąd przy usuwaniu danych z tabeli dhcp_net! MYSQL error: ".mysql_error()))

			$db->Execute("DELETE 
						  FROM dhcp_net_opt
						  WHERE net_id = $net_id")
								or die("Błąd przy usuwaniu danych z tabeli dhcp_net_opt! MYSQL error: ".mysql_error());
		else
			echo "Nieudane usunięcie sieci o ID: $net_id\n";
		$this->disconn($db);
	}
	function modify_net(	$net_id, $auth, $state, $cmpt_id, $nett_id, $network, $netmask, $gateway, 
							$range_min, $range_max, $broadcast, $comm1, $opt_id, $opt_txt, $comm2){
		$this->del_net($net_id);
		$this->add_net(	$net_id, $auth, $state, $cmpt_id, $nett_id, $network, $netmask, 
						$gateway, $range_min, $range_max, $broadcast, $comm1);
		if($opt_id<>"")			
			foreach($opt_id as $key =>$val)
				$this->add_net_opt($val, $net_id, $comm2[$key], $opt_txt[$key]);
	}
	function add_new_net(	$net_id, $auth, $state, $cmpt_id, $nett_id, $network, 
							$netmask, $gateway, $range_min, $range_max, $broadcast, 
							$comm1, $opt_id, $opt_txt, $comm2){
		$net_id=$this->add_net(	NULL, $auth, $state, $cmpt_id, $nett_id, 
								$network, $netmask, $gateway, $range_min, $range_max, 
								$broadcast, $comm1);
		if(isset($opt_id))
			foreach($opt_id as $key =>$val)
				$this->add_net_opt($val, $net_id, $comm2[$key], $opt_txt[$key]);
	}
}
?>