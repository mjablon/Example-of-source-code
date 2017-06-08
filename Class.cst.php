<?
require_once('adodb/adodb.inc.php');
require_once('Class.db.php');

class cst extends db{

	function login() {
		$login=$_POST['login'];
		$passwd=md5($_POST['passwd']);//hash password MD5 !!!
		$status=false;
		
		$db=$this->conn();
		$q="SELECT * FROM admin WHERE login='".$login."' AND passwd='".$passwd."'";
		$rs=$db->Execute($q) or die("Invalid operation login: ".mysql_error());
		$t=$rs->GetArray();
		
		if( $rs->NumRows() != 0 ) {
			setcookie("PHPSESSID",$_COOKIE['PHPSESSID'],time()+9000);
			foreach($t as $k => $v){
				$_SESSION['name']=$v['name'];
				$_SESSION['login']=$v['login'];
				$_SESSION['group']=$v['group'];
				$_SESSION['id_session']=session_id();
				$_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
				//print_r($_SESSION);
			}
			$this->disconn($db);
			return true;
		}
		else
			return false;
	}

	function cst2descr($sys_id) {
		$t=$this->cst_sys($sys_id);
		$str = $t['CITY']."_".$t['STREET']."_".$t['HOUSE_NUMBER'];
		if ($t['FLAT_NUMBER']!='')
			$str.="/".$t['FLAT_NUMBER'];
		return strtoupper(strtr(iconv('utf-8', 'iso-8859-2',$str), iconv('utf-8', 'iso-8859-2','ĘÓĄŚŁŻŹĆŃęóąśłżźćń'), 'EOASLZZCNeoaslzzcn'));
	}
	function cst_info($arr){
		$con1=array();
		if($arr['sys_id']<>"")
			$con1[]=" cst.sys_id LIKE '%".$arr['sys_id']."%' ";
		if($arr['street']<>"")
			$con1[]=" cst.street='".$arr['street']."' ";
		if($arr['city']<>"")
			$con1[]=" cst.city='".$arr['city']."' ";
		if($arr['b_num']<>"")
			$con1[]=" cst.b_num LIKE '".$arr['b_num']."%' ";
		if($arr['l_num']<>"")
			$con1[]=" cst.l_num LIKE '".$arr['l_num']."%' ";
		
		$con2=array();
		if($arr['mac']<>"")
			$con2[]=" cmp.mac LIKE '%".$arr['mac']."%' ";
		if($arr['ip']<>"")
			$con2[]=" cmp.ip LIKE '%".$arr['ip']."%' ";
		$t1=implode(" AND ", $con1);
		$t2=implode(" AND ", $con2);
		$r=array();

		if((!empty($con1)) AND (empty($con2))){
			$db = $this->conn();
			$q="SELECT cst.sys_id, cmp_id, street, city, b_num, l_num  
				FROM cst 
				LEFT JOIN cst_cmp ON cst.sys_id=cst_cmp.sys_id 
				WHERE ".$t1;
			//echo $q."<br>";
			$rs=$db->Execute($q) or die("Invalid select join tables3 cst, cst_cmp: ".mysql_error());
			$a1=$rs->GetArray();
			//$this->disconn($db);

			//$db = $this->conn();
			$q="SELECT mac, ip, cmp.comment, cmp_subcmp.cmp_id, cmp_subcmp.subcmp_id, cmp_type.name as cmpt, net_type.name as nett    
				FROM cmp 
				LEFT JOIN cmp_subcmp ON cmp.cmp_id=cmp_subcmp.subcmp_id 
				LEFT JOIN cmp_type ON cmp.cmpt_id=cmp_type.cmpt_id 
				LEFT JOIN cmp_net ON cmp.cmp_id=cmp_net.cmp_id 
				LEFT JOIN dhcp_net ON cmp_net.net_id=dhcp_net.net_id 
				LEFT JOIN net_type ON dhcp_net.nett_id=net_type.nett_id 
				WHERE 
				((cmp_type.name='ONT' AND net_type.name='PON') OR 
				(cmp_type.name='CPE' AND net_type.name='ETHERNET')) 
				AND cmp.cmp_id=";
			//echo "<pre>";
			//print_r($a1);
			//echo "</pre>";
			foreach ($a1 as $key => $val){
				//echo "<pre>".$val['cmp_id']."</pre>";
				//echo $q.$val['cmp_id']."<br>";
				$rs=$db->Execute($q.$val['cmp_id']) or die("Error in file: ".__FILE__.", function: ".__FUNCTION__.", line: ".__LINE__.", description: ".mysql_error());
				if($a2=$rs->GetArray()){
					$r[$key]=$a1[$key];
					$r[$key]['mac']=$a2[0]['mac'];
					$r[$key]['ip']=$a2[0]['ip'];
					$r[$key]['comment']=$a2[0]['comment'];
					$r[$key]['cmpt'] = $a2[0]['cmpt'];
					$r[$key]['nett'] = $a2[0]['nett'];
				}
			}
			$this->disconn($db);
			return $r;
		}
		else if((empty($con1)) AND (!empty($con2))){
			$db = $this->conn();
			$q="SELECT mac, ip, cmp.comment, cmp_subcmp.subcmp_id as cmp_id, cmp_type.name as cmpt, net_type.name as nett    
				FROM cmp 
				LEFT JOIN cmp_subcmp ON cmp.cmp_id=cmp_subcmp.subcmp_id 
				LEFT JOIN cmp_type ON cmp.cmpt_id=cmp_type.cmpt_id 
				LEFT JOIN cmp_net ON cmp.cmp_id=cmp_net.cmp_id 
				LEFT JOIN dhcp_net ON cmp_net.net_id=dhcp_net.net_id 
				LEFT JOIN net_type ON dhcp_net.nett_id=net_type.nett_id 
				WHERE 
				((cmp_type.name='ONT' AND net_type.name='PON') OR 
				(cmp_type.name='CPE' AND net_type.name='ETHERNET')) 
				AND ".$t2;
			$rs=$db->Execute($q) or die("Incorrect select from cmp table: ".mysql_error());
			$rs=$db->Execute($q) or die("Incorrect select from cmp table: ".mysql_error());
			$a1=$rs->GetArray();
			//$this->disconn($db);

			//$db = $this->conn();
			$q="SELECT cst.sys_id, street, city, b_num, l_num 
				FROM cst 
				LEFT JOIN cst_cmp ON cst.sys_id=cst_cmp.sys_id 
				WHERE cst_cmp.cmp_id=";
			$i=0; $r=array();
			foreach ($a1 as $key => $val){
				$rs=$db->Execute($q.$val['cmp_id']) or die("Incorrect select from join tables cst and cst_cmp: ".mysql_error());
				$a2=$rs->GetArray();
				foreach($a2 as $k => $v){
					$r[$i]=$a1[$key];
					$r[$i]['sys_id']=$a2[$k]['sys_id'];
					$r[$i]['street']=$a2[$k]['street'];
					$r[$i]['city']=$a2[$k]['city'];
					$r[$i]['b_num']=$a2[$k]['b_num'];
					$r[$i]['l_num']=$a2[$k]['l_num'];
					$i++;
				}
			}
			$this->disconn($db);
			return $r;
		}
		else if((!empty($con1)) AND (!empty($con2))){
			$db = $this->conn();
			$q="SELECT mac, ip, cmp.comment, cmp_subcmp.subcmp_id as cmp_id, cmp_type.name as cmpt, net_type.name as nett    
				FROM cmp 
				LEFT JOIN cmp_subcmp ON cmp.cmp_id=cmp_subcmp.subcmp_id 
				LEFT JOIN cmp_type ON cmp.cmpt_id=cmp_type.cmpt_id 
				LEFT JOIN cmp_net ON cmp.cmp_id=cmp_net.cmp_id 
				LEFT JOIN dhcp_net ON cmp_net.net_id=dhcp_net.net_id 
				LEFT JOIN net_type ON dhcp_net.nett_id=net_type.nett_id 
				WHERE 
				((cmp_type.name='CM' AND net_type.name='DOCSIS') OR 
				(cmp_type.name='ONT' AND net_type.name='PON') OR 
				(cmp_type.name='CPE' AND net_type.name='ETHERNET')) 
				AND ".$t2;
			$rs=$db->Execute($q) or die("Incorrect select from cmp table: ".mysql_error());
			$a1=$rs->GetArray();
			//$this->disconn($db);

			//$db = $this->conn();
			$q="SELECT cst.sys_id, street, city, b_num, l_num 
				FROM cst 
				LEFT JOIN cst_cmp ON cst.sys_id=cst_cmp.sys_id 
				WHERE ".$t1." AND cst_cmp.cmp_id=";
			$i=0;
			foreach ($a1 as $key => $val){
				$rs=$db->Execute($q.$val['cmp_id']) or die("Incorrect select from join tables cst and cst_cmp: ".mysql_error());
				$a2=$rs->GetArray();
				foreach($a2 as $k => $v){
					$r[$i]=$a1[$key];
					$r[$i]['sys_id']=$a2[$k]['sys_id'];
					$r[$i]['street']=$a2[$k]['street'];
					$r[$i]['city']=$a2[$k]['city'];
					$r[$i]['b_num']=$a2[$k]['b_num'];
					$r[$i]['l_num']=$a2[$k]['l_num'];
					$i++;
				}
			}
			$this->disconn($db);
			return $r;
		}
	}
	function updt_cst_cmp_cst($sys_id_old, $sys_id_new, $name1, $name2){
		
		$db = $this->conn();
		
		$q1 = "UPDATE cst SET sys_id='".$sys_id_new."', name1='".$name1."', name2='".$name2."' WHERE sys_id='".$sys_id_old."';";
		echo $q1."<br>";
		#$db->Execute($q1) or die("Error ".mysql_errno()." ".mysql_error());
		
		$q2 = "UPDATE cst_cmp SET sys_id='".$sys_id_new."' WHERE sys_id='".$sys_id_old."';";
		echo $q2."<br><br>";
		#$db->Execute($q2) or die("Error ".mysql_errno()." ".mysql_error());
		
		$this->disconn($db);
		
	}
	function cst_int($sys_id){
		$db = $this->conn();
		$q = "SELECT * FROM cst WHERE sys_id='".$sys_id."'";
		$rs = $db->Execute($q) or die("Error ".mysql_errno()." ".mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function cst2street(){
		$db = $this->conn();
		$q = "SELECT DISTINCT street FROM cst ORDER BY street ASC";
		$rs = $db->Execute($q) or die("Invalid getting street name: ".mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function cst2city(){
		$db = $this->conn();
		$q = "SELECT DISTINCT city FROM cst ORDER BY city ASC";
		$rs = $db->Execute($q) or die("Invalid getting city name: ".mysql_error());
		$this->disconn($db);
		return $rs->GetArray();
	}
	function chcksys($sys_id){
	
		$db = $this->conn();
		$q = "SELECT COUNT(COM_CUSTOMERS.CUSTOMER_ID) 
			  FROM COM_CUSTOMERS 
			  WHERE COM_CUSTOMERS.CUSTOMER_ID='".$sys_id."'";
		$rs = ibase_query($q) or die("Invalid count of customer_id: ".ibase_errmsg());
		if($sys_t = ibase_fetch_row($rs))
			if($sys_t[0]!=0)
				return 1;
			else
				return 0;
				
	}
	function chckcst( $sys_id ){
	
		$db = $this->conn();
		$q = "SELECT * FROM cst WHERE sys_id = '".$sys_id."'";
		$rs = $db->Execute( $q ) or die("Error ".mysql_errno()." ".mysql_error());
		if( $rs->NumRows() != 0 ) 
			return 1;
		else
			return 0;

	}
	function chckcstcmp($sys_id ) {
	
		$db = $this->conn();
		$sql = "SELECT * FROM cst_cmp WHERE sys_id='".$sys_id."'";
		$rs = $db->Execute( $sql )or die(mysql_error());
		if( $rs->NumRows() != 0 ) 
			return 1;
		else
			return 0;
	}
	function cst_sys($sys_id){
		$db = $this->conn_sysb();
		$sys_t=array();
		$q =	"SELECT COM_CUSTOMERS.CUSTOMER_ID, NAME1, NAME2,
				STREET, HOUSE_NUMBER, FLAT_NUMBER, POSTAL_CODE, CITY,
				SHIP_STREET, SHIP_HOUSE_NUMBER, SHIP_FLAT_NUMBER, SHIP_POSTAL_CODE, SHIP_CITY,
				INV_STREET, INV_HOUSE_NUMBER, INV_FLAT_NUMBER, INV_POSTAL_CODE, INV_CITY,
				ID_CARD_NUMBER, PESEL, TAX_NUMBER, REGON, 
				TEL1, TEL2, EMAIL, TESAT_EMAIL 
				FROM COM_CUSTOMERS 
				WHERE COM_CUSTOMERS.CUSTOMER_ID='".$sys_id."'";
		$rs = ibase_query($q) or die(ibase_errmsg());
		if(is_array($sys_t = ibase_fetch_assoc($rs)))	{
			$sys_t['POSTAL_CODE1'] = substr($sys_t['POSTAL_CODE'], 0, 2);
			$sys_t['POSTAL_CODE2'] = substr($sys_t['POSTAL_CODE'], 3, 3);
			$sys_t['SHIP_POSTAL_CODE1'] = substr($sys_t['SHIP_POSTAL_CODE'], 0, 2);
			$sys_t['SHIP_POSTAL_CODE2'] = substr($sys_t['SHIP_POSTAL_CODE'], 3, 3);
			$sys_t['INV_POSTAL_CODE1'] = substr($sys_t['INV_POSTAL_CODE'], 0, 2);
			$sys_t['INV_POSTAL_CODE2'] = substr($sys_t['INV_POSTAL_CODE'], 3, 3);
			$sys_t['TEL_NUMBER11'] = substr($sys_t['TEL_NUMBER'], 4, 1);
			$sys_t['TEL_NUMBER12'] = substr($sys_t['TEL_NUMBER'], 5, 1);
			$sys_t['TEL_NUMBER13'] = substr($sys_t['TEL_NUMBER'], 6, 1);
			$sys_t['TEL_NUMBER14'] = substr($sys_t['TEL_NUMBER'], 7, 1);
			$sys_t['TEL_NUMBER15'] = substr($sys_t['TEL_NUMBER'], 8, 1);
			$sys_t['TEL_NUMBER16'] = substr($sys_t['TEL_NUMBER'], 9, 1);
			$sys_t['TEL_NUMBER17'] = substr($sys_t['TEL_NUMBER'], 10, 1);
		}
		return $sys_t;
	}
	function cst_add($sys_t){
		$db = $this->conn();
		$q = "SELECT COUNT(sys_id) FROM cst WHERE sys_id='".$sys_t['CUSTOMER_ID']."'";
		$rs = $db->Execute($q) or die("Incorrect sys_id: ".mysql_error());
		$a = $rs->FetchRow();
		if($a['COUNT(sys_id)'] !=0 )
			return 1;
		$q = "INSERT INTO cst VALUES ('".$sys_t['CUSTOMER_ID']."', '".$sys_t['NAME1']."', '".$sys_t['NAME2']."', '".$sys_t['SHIP_STREET']."', 
			 '".$sys_t['SHIP_FLAT_NUMBER']."', '".$sys_t['SHIP_HOUSE_NUMBER']."', '".$sys_t['SHIP_CITY']."');";
		#echo $q;
		$db->Execute($q) or die("Invalid add to cst table: ".mysql_error());
		$this->disconn($db);
		return 1;
	}
	function cst_updt($sys_id, $sys_t){ 
		$db = $this->conn();
		if($sys_id == NULL)
			$this->sys2cst($sys_t['CUSTOMER_ID']);
		else{
			$q = "UPDATE cst SET sys_id='".$sys_t['CUSTOMER_ID']."',
								 street='".$sys_t['SHIP_STREET']."', 
								 l_num='".$sys_t['SHIP_FLAT_NUMBER']."',
								 b_num='".$sys_t['SHIP_HOUSE_NUMBER']."', 
								 city='".$sys_t['SHIP_CITY']."',
				 WHERE sys_id='".$sys_id."'";
			$db->Execute($q) or die("Invalid update to cst table: ".mysql_error());
			$this->cst2cmp_updt($sys_id, $sys_t['CUSTOMER_ID'] );
		}
		$this->disconn($db);
		return 1;
	}
	function sys2cst($sys_id){
		if($this->chcksys($sys_id))
			return $this->cst_add($this->cst_sys($sys_id));
		else
			return 0;
	}
	
	function insert_x( ){
	
		$db = $this->conn();
		$q = "SELECT sys_id FROM cst_cmp";
		$rs = $db->Execute($q) or die("Incorrect sys_id: ".mysql_error());
		$r = $rs->GetArray();
		//$arr = array();
		foreach ($r as $key => $val){
		
			// $sql = "INSERT INTO cst ( sys_id, name1, name2, street, l_num,
			 // b_num, zip_code, city, doc_nr, pesel_nip_regon, phone_1, 
			 // phone_2, email_1, email_2 ) 
			 // SELECT  sys_id, name1, name2, street, l_num,
			 // b_num, zip_code, city, doc_nr, pesel_nip_regon, phone_1, 
			 // phone_2, email_1, email_2  FROM cst2 WHERE cst2.sys_id = ".$val['sys_id'];
			
			// echo $sql."<br><br>";
			//   echo '$cst->cst_sys( '.$val['sys_id'] .');'."<br>";
			//$arr[] = $this->cst_sys( $val['sys_id'] ) ;
			//echo $val['sys_id']."<br>";
			$this->sys2cst( $val['sys_id'] );

		}
		
		//print_r( $arr );
	}
	
	
	
	function sys2cst_updt($id_old, $id_new){
		if(!$this->chcksys($id_new))
			return 0;
		else{
			$this->cst_updt($id_old, $this->cst_sys($id_new));
			return 1;
		}
	}
	function cst2cmp_updt($id_old, $id_new){
		$db = $this->conn();
		$q = "UPDATE cst_cmp 
			  SET sys_id='".$id_new."' 
			  WHERE sys_id='".$id_old."'";
		$db->Execute($q) or die("Invalid modification of sys_id: ".mysql_error());
		$this->disconn($db);
	}
	function cst2cmp($sys_id, $cmp_id){
		$db = $this->conn();
		$db->Execute("INSERT INTO cst_cmp ( sys_id , cmp_id )
		  			  VALUES ('".$sys_id."', $cmp_id);")or die("Invalid insert to cst_cmp: ".mysql_error());
		$this->disconn($db);
		return 1;
	}
	function cst2cmp_del($cmp_id){
		$db = $this->conn();
		$db->Execute("DELETE FROM cst_cmp WHERE cmp_id=$cmp_id")or die("Incorrect delete for cmp_id: ".mysql_error());
		$this->disconn($db);
	}
	function cmp_cst($a1){
		$db = $this->conn();
		$q = "SELECT * 
			 FROM cst_cmp 
			 LEFT JOIN cst ON cst_cmp.sys_id=cst.sys_id 
			 WHERE cst_cmp.cmp_id=";
		foreach ($a1 as $key => $val){
			$qq = $q.$val['cmp_id'];
			$rs = $db->Execute($qq) or die("Invalid insert to cst_cmp: ".mysql_error());
			$a2 = $rs->GetArray();
			$a1[$key]['sys_id'] = $a2[0]['sys_id'];
			$a1[$key]['street'] = $a2[0]['street'];
			$a1[$key]['b_num'] = $a2[0]['b_num'];
			$a1[$key]['l_num'] = $a2[0]['l_num'];
			$a1[$key]['city'] = $a2[0]['city'];
		}
		$this->disconn($db);
		return $a1;
	}
	function cst_cmp(){
	
		$db = $this->conn();
		$q = "SELECT * 
			 FROM cst 
			 LEFT JOIN cst_cmp ON cst.sys_id=cst_cmp.sys_id ORDER BY cst.sys_id";
		$rs = $db->Execute($q) or die("Invalid cst result: ".mysql_error());
		$a1 = $rs->GetArray();
		$this->disconn($db);
		$db = $this->conn();
		$q = "SELECT cmp.cmp_id, mac, ip, cmp.comment, cmp_type.name as cmpt, net_type.name as nett 
			 FROM cmp_subcmp 
			 LEFT JOIN cmp ON cmp_subcmp.subcmp_id=cmp.cmp_id 
			 LEFT JOIN cmp_type ON cmp.cmpt_id=cmp_type.cmpt_id 
			 LEFT JOIN cmp_net ON cmp.cmp_id=cmp_net.cmp_id 
			 LEFT JOIN dhcp_net ON cmp_net.net_id=dhcp_net.net_id 
			 LEFT JOIN net_type ON dhcp_net.nett_id=net_type.nett_id 
			 WHERE 
			 ((cmp_type.name='CM' AND net_type.name='DOCSIS') OR 
			  (cmp_type.name='ONT' AND net_type.name='PON') OR 
			 (cmp_type.name='CPE' AND net_type.name='ETHERNET')) 
			 AND cmp.cmp_id=";
			 
		//echo $q;
		$r=array();
		foreach ($a1 as $key => $val){
			$rs = $db->Execute($q.$val['cmp_id']) or die("Invalid select of cmp: ".mysql_error());
			if ($a2 = $rs->GetArray()){
				$r[$key]=$a1[$key];
				$r[$key]['mac'] = $a2[0]['mac'];
				$r[$key]['ip'] = $a2[0]['ip'];
				$r[$key]['comment'] = $a2[0]['comment'];
				$r[$key]['cmpt'] = $a2[0]['cmpt'];
				$r[$key]['nett'] = $a2[0]['nett'];
			}
		}
		$this->disconn($db);
	
		return $r;
	}
	
	function test2(){
	
		$db = $this->conn();
		$q = "SELECT * 
			 FROM cst 
			 LEFT JOIN cst_cmp ON cst.sys_id=cst_cmp.sys_id ORDER BY cst.sys_id";
		$rs = $db->Execute($q) or die("Invalid cst result: ".mysql_error());
		$a1 = $rs->GetArray();
		$this->disconn($db);
		$db = $this->conn();
		$q = "SELECT cmp.cmp_id, mac, ip, cmp_type.name as cmpt, net_type.name as nett 
			  FROM cmp_subcmp 
			  LEFT JOIN cmp ON cmp_subcmp.subcmp_id=cmp.cmp_id 
			  LEFT JOIN cmp_type ON cmp.cmpt_id=cmp_type.cmpt_id 
			  LEFT JOIN cmp_net ON cmp.cmp_id=cmp_net.cmp_id 
			  LEFT JOIN dhcp_net ON cmp_net.net_id=dhcp_net.net_id 
			  LEFT JOIN net_type ON dhcp_net.nett_id=net_type.nett_id
			  LEFT JOIN cmp_subcmp AS subcmp2 ON subcmp2.cmp_id=cmp.cmp_id 
			  WHERE ( cmp_type.name='ONT' OR cmp_type.name='CPE' ) AND cmp.cmp_id=";
		$r=array();
		foreach ($a1 as $key => $val){
			$rs = $db->Execute($q.$val['cmp_id']) or die("Invalid select of cmp: ".mysql_error());
			if ($a2 = $rs->GetArray()){

				$r[$key]=$a1[$key];
				$r[$key]['mac'] = $a2[0]['mac'];
				$r[$key]['ip'] = $a2[0]['ip'];
				$r[$key]['cmpt'] = $a2[0]['cmpt'];
				//$r[$key]['nett'] = $a2[0]['nett'];
			}
		}
		$this->disconn($db);
		
		foreach ($r as $key => $val){
		
			echo $key.")  ".$val['sys_id']." | ".$val['cmp_id']." | ".$val['mac']." | ".$val['ip']."<br>";
		
		}
		
		
		return $r;
	}
	
	function test4() {
	
		$db = $this->conn();
		$q = "SELECT * FROM cst_cmp";
		$r = $db->Execute($q) or die(mysql_error());
		$a = $r->GetArray();
		$this->disconn($db);

		$db = $this->conn();
		$aggr = array();
		$q = "SELECT 
					cmp.mac AS mac1,
					cmp.ip AS ip1,
					cmp_type.name AS name1,
					cmp2.mac AS mac2,
					cmp2.ip AS ip2,
					cmp2.comment AS comment2, 
					cmp_type2.name AS name2  
			  FROM cmp 
			  LEFT JOIN cmp_type ON cmp.cmpt_id = cmp_type.cmpt_id 
			  LEFT JOIN cmp_subcmp ON cmp.cmp_id = cmp_subcmp.cmp_id 
			  LEFT JOIN cmp AS cmp2 ON cmp_subcmp.subcmp_id = cmp2.cmp_id 
			  LEFT JOIN cmp_type AS cmp_type2 ON cmp2.cmpt_id = cmp_type2.cmpt_id 
			  WHERE cmp.cmp_id = ";
		$i = 0;
		foreach ($a as $k => $v){
			
			//$aggr[$k]['sys_id'] = $v['sys_id'];
			//$aggr[$k]['cmp_id'] = $v['cmp_id'];
			
			$rr = $db->Execute($q.$v['cmp_id']) or die(mysql_error());
			foreach ( $rr as $kk => $vv ) {
				
				$aggr[$i]['sys_id'] = $v['sys_id'];
				$aggr[$i]['name1'] = $vv['name1'];
				$aggr[$i]['mac1'] = $vv['mac1'];
				$aggr[$i]['ip1'] = $vv['ip1'];
				$aggr[$i]['name2'] = $vv['name2'];
				$aggr[$i]['mac2'] = $vv['mac2'];
				$aggr[$i]['ip2'] = $vv['ip2'];
				$aggr[$i]['comment2'] = $vv['comment2'];
				echo $v['sys_id']." / ".$vv['name1']." / ".$vv['mac1']." / ".$vv['ip1']." | ".$vv['name2']."  ".$vv['mac2']."  ".$vv['ip2']."  ".$vv['comment2']."<br>";
				
			}
			echo "<br>";
		}
		
		
		//print_r($aggr);
		$this->disconn($db);
	
	}
	
	function test3() {
	
		$db = $this->conn();
		$q = "SELECT * FROM cmp WHERE cmpt_id = 1 AND ip LIKE '79.173.36.%'";
		$rs = $db->Execute($q) or die(mysql_error());
		$a1 = $rs->GetArray();
		$this->disconn($db);
		
		$db = $this->conn();
		$q = "SELECT * 
			 FROM cst_cmp 
			 LEFT JOIN cst ON cst_cmp.sys_id = cst.sys_id 
			 WHERE cmp_id = ";
		$r = array();
		
		foreach ($a1 as $key => $val){
		
			$rs = $db->Execute( $q.$val['cmp_id'] ) or die(mysql_error());
			//echo $q.$val['cmp_id']."<br>";
			$a2 = $rs->FetchRow();
			
				//echo $a1[$key]['mac']."<br>";
				$r[$key]['sys_id'] = $a2['sys_id'];
				$r[$key]['mac'] = $a1[$key]['mac'];
				$r[$key]['ip'] = $a1[$key]['ip'];
				$r[$key]['cmp_id'] = $a1[$key]['cmp_id'];
				
		}	 
		
		//print_r($r);
		$this->disconn($db);
		$db = $this->conn();
		$incorr = array();
		
		foreach ($r as $k => $v) {
			//print_r($v);
			echo $k.")  ".$v['cmp_id']." | ".$v['mac']." | ".$v['ip']." | ".$v['sys_id'];
			if ( $v['sys_id'] == "" ) {
			
				$incorr[$k]['cmp_id'] = $v['cmp_id'];
				$incorr[$k]['mac'] = $v['mac'];
				$q = "SELECT sys_id FROM import_db 
					 WHERE mac = '".$v['mac']."'";
				
				$rs = $db->Execute( $q ) or die(mysql_error());
				$row = $rs->FetchRow();
				$incorr[$k]['sys_id'] = $row['sys_id'];
				
				echo "  ------------------->  ".$row['sys_id'];
				
			}
			
			echo "<br>";
		
		}	 
		
		//$this->sys2cst(107039);
		
		//foreach  ($incorr as $kk => $vv) {
		
			//echo $kk."<br>";
			//if ( $kk == 775 ) break;
			//$this->cst2cmp( $vv['sys_id'], $vv['cmp_id'] );
			//$this->sys2cst($vv['sys_id']);
			//echo "INSERT IGNORE INTO `cst_cmp` (  `sys_id` ,  `cmp_id` ) VALUES ('".$vv['sys_id']."',  '".$vv['cmp_id']."');<br>";
			
		//}
		$this->disconn($db);
	}
	
	function test(){
	
		$db = $this->conn();
		$q = "SELECT cmp_id, cst.sys_id 
			 FROM cst 
			 LEFT JOIN cst_cmp ON cst.sys_id=cst_cmp.sys_id ORDER BY cst.sys_id";
		$rs = $db->Execute($q) or die("Invalid cst result: ".mysql_error());
		$a1 = $rs->GetArray();
		$a2 = array();
		foreach ($a1 as $key => $val){
		
			echo $val['cmp_id']." - ".$val['sys_id']."<br>";
		
		}
		return $a2;
	}
	
	function cst_del($sys_id){
		$db = $this->conn();
		$q = "DELETE FROM cst 
			 WHERE sys_id='".$sys_id."'";
		$rs = $db->Execute($q) or die("Invalid delete contract from cst table: ".mysql_error());
		$this->disconn($db);
	}
	
	function cst_cmp_del($sys_id){
		$db = $this->conn();
		$db->Execute("DELETE FROM cst_cmp WHERE sys_id='".$sys_id."'")or die("Incorrect delete from cst_cmp table: ".mysql_error());
		$this->disconn($db);
	}
	function chck_cst_cmp($sys_id){
		$db = $this->conn();
		$q = "SELECT * FROM cst_cmp 
				WHERE sys_id='".$sys_id."'";
		$rs = $db->Execute($q) or die("Invalid query $q in Class.cst file: ".mysql_error());
		if($rs->NumRows() != 0){
			$this->disconn($db);
			return 0;
		}
		else{
			$this->disconn($db);
			return 1;
		}
	}
	function cli_del($sys_id){
		$this->cst_cmp_del($sys_id);
		if($this->chck_cst_cmp($sys_id))
			$this->cst_del($sys_id);
	}
}
?>
