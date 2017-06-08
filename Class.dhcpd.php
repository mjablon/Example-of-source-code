<?
require_once('adodb/adodb.inc.php');
require_once('Class.db.php');

class dhcpd extends db{

	function view_leases(){
		$db = $this->conn();
		$rs = $db->Execute("SELECT cmp.cmp_id, mac, ip, cmp.comment  
							FROM cmp_subcmp 
							LEFT JOIN cmp ON cmp_subcmp.subcmp_id=cmp.cmp_id 
							LEFT JOIN cmp_type ON cmp.cmpt_id=cmp_type.cmpt_id 
							WHERE (cmp_type.name='CM' OR cmp_type.name='ONT' OR cmp_type.name='SW') ORDER BY INET_ATON(ip)")
							or die(mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function net_pckt(){
		$db = $this->conn();
		$rs = $db->Execute("SELECT * 
							FROM serv_add  
							WHERE `group`='NET' AND `state`=1")
							or die("MYSQL error - select from serv_add table: ".mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function dhcp_global_opt(){
		$db = $this->conn();
		$rs = $db->Execute("SELECT * 
							FROM dhcp_opt")
							or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function dhcp_global_opt_info($dhcp_id){
		$db = $this->conn();
		$rs = $db->Execute("SELECT * 
							FROM dhcp_opt
							WHERE dhcp_id=$dhcp_id")
							or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function dhcp_opt_del($dhcp_id){
		$db = $this->conn();
		$db->Execute("DELETE * 
					  FROM dhcp_opt
					  WHERE dhcp_id=$dhcp_id")
						or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
		return true;
	}
	function dhcp_edit_opt($dhcp_id, $comment, $opt_txt, $state){
		$db = $this->conn();
		$db->Execute("UPDATE dhcp_opt 
					  SET comment='$comment', opt_txt='$opt_txt', state='$state' 
					  WHERE dhcp_id=$dhcp_id")
						or die("MYSQL error: ".mysql_error());
		$this->disconn($db);
		return true;
	}
	function _ip($cmpt_id, $nett_id, $cmp_id) {
	
		$db = $this->conn();
		$q="SELECT INET_ATON(network) as network, 
					INET_ATON(netmask) as netmask, 
					INET_ATON(range_min) as min, 
					INET_ATON(range_max) as max 
			FROM cmp_net 
			LEFT JOIN dhcp_net ON cmp_net.net_id=dhcp_net.net_id
			WHERE (cmp_net.cmp_id=$cmp_id AND nett_id=$nett_id AND cmpt_id=$cmpt_id) AND dhcp_net.state=1";

		$rs = $db->Execute($q) or die(mysql_error());
		$a1 = $rs->GetArray();
		if(empty($a1)) return 0;
		//print_r($a1);
		$ip='';
		foreach ($a1 as $key => $val) {
			//echo $key.") ".long2ip($val['max'])." ".long2ip($val['min'])." ".long2ip($val['network'])." ".long2ip($val['netmask'])."<br>";

			$q = "SELECT INET_ATON(`ip`) as ip 
				  FROM `cmp` 
				  WHERE (INET_ATON(`ip`)&".$val['netmask']." ) 
				  LIKE ".$val['network']." AND 
				  ((INET_ATON(`ip`) <= ".$val['max']." ) AND
				  (INET_ATON(`ip`) >= ".$val['min']." )) 
				  ORDER BY INET_ATON(`ip`) ASC";

  		    $rs = $db->Execute($q) or die(mysql_error());
			$a2 = $rs->GetRows();
			for ($k=0,$i=$val['min'];$i<=$val['max'];$i++,$k++)
				//print_r(long2ip($i));
				if($i != $a2[$k]['ip']) { $ip=long2ip($i); break;}
				
		}
		$this->disconn($db);
		return $ip;
	}
	function _voip($cmpt_id, $nett_id){
		$db = $this->conn();
		$q="SELECT INET_ATON(network) as network, 
					INET_ATON(netmask) as netmask, 
					INET_ATON(range_min) as min, 
					INET_ATON(range_max) as max 
			FROM dhcp_net 
			WHERE nett_id=$nett_id AND cmpt_id=$cmpt_id";
		$rs = $db->Execute($q) or die(mysql_error());
		$a1 = $rs->GetArray();
		if(empty($a1)) return 0;
		$ip=0;
		foreach ($a1 as $val) {
			$q = "SELECT INET_ATON(`ip`) as ip 
				  FROM `cmp` 
				  WHERE (INET_ATON(`ip`)&".$val['netmask']." ) 
				  LIKE ".$val['network']." AND 
				  ((INET_ATON(`ip`) <= ".$val['max']." ) AND 
				  (INET_ATON(`ip`) >= ".$val['min']." )) 
				  ORDER BY INET_ATON(`ip`) ASC";
			$rs = $db->Execute($q) or die(mysql_error());
			$a2 = $rs->GetArray();
			
			for ($k=0,$i=$val['min'];$i<=$val['max'];$i++,$k++)
				if($i != $a2[$k]['ip'])  { $ip=long2ip($i); break;}
		}
		$this->disconn($db);
		return $ip;
	}
}
?>