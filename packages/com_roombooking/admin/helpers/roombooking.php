<?php
defined('_JEXEC') or die('=;)');
include_once(JPATH_ROOT . '/components/com_billing/useraccount.php');
include_once(JPATH_ROOT . '/components/com_billing/account.php');


class VmapiHelper
{
    
    public static function pak_options($pref = null)
    {
        $db = JFactory::getDBO();
        //build the list of categories
        $query = 'SELECT a.title AS text, a.id AS value' . ' FROM #__balansiptv_pak' . $pref . ' AS a' . ' ORDER BY a.title';
        $db->setQuery($query);
        $pgbc = $db->loadObjectList();
        return $pgbc;
        
    }
    
    public static function add_plus($data, $m = 0, $d)
    {
        $data    = explode('-', $data);
        $strtime = mktime(0, 0, 0, $data[1] + $m, $data[2] + $d, $data[0]);
        return date('Y-m-d', $strtime);
    }
    
    public static function load_all_pak($users)
    {
        $db = JFactory::getDBO();
        $q  = 'SELECT a.*, a.price as price_history, b.title, b.price, b.id as bid 
		FROM #__vmapi_balansiptv_pack AS a 
		LEFT JOIN #__balansiptv_pak AS b ON a.vid=b.id
		WHERE a.users=' . $users . ' ORDER BY a.id DESC';
        $db->setQuery($q);
        return $db->LoadObjectList();
        
    }
    public static function histor($users)
    {
        $db = JFactory::getDBO();
        $q  = 'SELECT * FROM #__balansiptv_histor AS a 
    WHERE a.users=' . $users . " ORDER BY a.id DESC";
        $db->setQuery($q);
        return $db->LoadObjectList();
    }
    
    public static function load_pak($id, $users = null)
    {
        if ($id == '') {
            return;
        }
        //
        if ($users)
            $users = ' AND a.users=' . $users;
        else
            $users = '';
        
        $db = JFactory::getDBO();
        $q  = 'SELECT a.*, a.price as price_history, b.title, b.price, b.last_date
	   FROM #__balansiptv_pack AS a 
	   LEFT JOIN #__balansiptv_pak AS b ON a.vid = b.id
	   WHERE a.id=' . $id . $users;
        $db->setQuery($q);
        return $db->LoadObject();
    }
    
    public static function GetDaysBetween($date1, $date2)
    {
        $date1 = explode('-', $date1);
        $date2 = explode('-', $date2);
        $start_date = mktime(0, 0, 0, $date1[1], $date1[2], $date1[0]);
        $end_date   = mktime(0, 0, 0, $date2[1], $date2[2], $date2[0]);
        
        //Теперь вычислим метку Unix для указанной даты
        $birthdate_unix = $start_date;
        $current_unix   = $end_date;
        $period_unix    = $current_unix - $birthdate_unix;
        $age_in_days    = floor($period_unix / (24 * 60 * 60));
        return $age_in_days;
    }
    
	function GetUserGroupDiscountWithDilerOption($uid)
	{
		$db = JFactory::getDBO();

		$query = "select diler_id from `#__balansiptv_diler` where `user_id` = $uid"; 
		$result = $db->setQuery($query);  
		$is_dilers_user = $db->loadResult();

		if ($is_dilers_user->diler_id !=0)
		{
		  	$discount=GetUserGroupDiscount($is_dilers_user); 
		}
		else
		{
			$discount=GetUserGroupDiscount($uid);
		}
		return $discount;
	}

	function GetUserGroupDiscountOnlyDiler($uid)
	{
		$db = JFactory::getDBO();

		$query = "SELECT diler_id FROM `#__balansiptv_diler` WHERE `user_id` = $uid";
		$result = $db->setQuery($query);  
		$is_dilers_user = $db->loadObject();
		
		if(count($is_dilers_user) > 0) {
		if (!$is_dilers_user->diler_id)
		{
			$discount = GetUserGroupDiscount($uid);
		}
		else {
			$discount = 0;
		}
		}
		else {
		$discount = 0;
		}
		
		return $discount;
	}
	
	function GetLastDiscount($uid)
	{
		$db = JFactory::getDBO();

		$query = "select fulldiscount from `#__balansiptv_pack` where `id` = $uid";
		$result = $db->setQuery($query);  
		$fd = $db->loadObject();
		BillingLogMessage('D', 'q', $query.' dis:'.$fd->fulldiscount);

		return $fd->fulldiscount;
	}

	function GetSumPack($user, $vid)
	{
		$db = JFactory::getDBO();

		$query = "select sum(fullsumma) as fs,sum(summa) as s from `#__balansiptv_histor` where `id_kassir` = $user and `vid` = $vid and dk = '-'";
		BillingLogMessage('GetSumPack', 'GetSumPack', $query);
		$result = $db->setQuery($query);  
		$fd = $db->loadObject();
		return $fd;
	}
	
    public static function addSubmenu($vName)
    {
        JSubMenuHelper::addEntry(JText::_('Панель управления'), 'index.php?option=com_vmapi', $vName == 'der');
    }
    
    //Картинка 
    public static function I($name, $w = null, $v = false, $alt = null)
    {
        if (!empty($w)) {
            $w = 'style="' . $w . '"';
        }
        if (!$v)
            echo '<img  alt="' . $alt . '" title="' . $alt . '"   border="0" ' . $w . ' src="' . JURI::root() . 'administrator/components/com_vmapi/assets/images/' . $name . '" />';
        else
            return '<img border="0" alt="' . $alt . '" title="' . $alt . '" ' . $w . ' src="' . JURI::root() . 'administrator/components/com_vmapi/assets/images/' . $name . '" />';
    }
    
    function AJAX()
    {
        if (!isset($_REQUEST['ajax_fedyanin'])) {
            $_REQUEST['ajax_fedyanin'] = 1;
            $document = JFactory::getDocument();
            $document->addScript(JURI::root() . 'administrator/components/com_vmapi/JsHttpRequest/lib/JsHttpRequest/JsHttpRequest.js');
        }
    }
    
    public static function USERS($id)
    {
        $db = JFactory::getDBO();
		$UA = new UserAccount();
        $q  = 'SELECT * FROM #__vmapi_balans WHERE users=' . $id;
		
        $db->setQuery($q);
        $row = $db->LoadObject();
        if (!isset($row)) {
            $q = 'INSERT INTO #__vmapi_balans (`users`) VALUES (' . $id . ')';
            $db->setQuery($q);
            $db->query();
            //$row->id = $db->insertid();
			
			if($id > 0)
			{
				$bal = $UA->GetBalance($id);
			}
			else
			{
				$bal = 0;
			}

			$UA = new UserAccount();
            $row->users  = $id;
            $row->money  = $bal;
            $row->status = 0;
            $row->serv = "";
            $row->kom    = null;
			$row->pass   = null;
            
        }
        
        $q = 'SELECT * FROM #__users WHERE id=' . $id;
        $db->setQuery($q);
        $user      = $db->LoadObject();
        $row->user = $user;
        return $row;
    }
    
    
    public static function UPDATE($status, $user)
    {
        $db = JFactory::getDBO();
        $q = 'UPDATE #__vmapi_balans SET status=' . $status . ' WHERE users=' . $user;
        $db->setQuery($q);
        $db->Query();
    }

    public static function UPDATEDATA($phone, $address, $user, $fio, $pass, $serv, $diler, $credit, $allow_freepak, $anb, $pot, $end_nb_date, $credit_limit, $update_detail, $allow_cashdesk, $warehouse_id )
    {
        $db = JFactory::getDBO();
        $q = "UPDATE #__vmapi_balans SET phone = '$phone', `address` = '$address', fio = '$fio', pass = '$pass', serv = '$serv', credit = '$credit', allow_freepak = '$allow_freepak', anb = '$anb', pot = '$pot', end_nb_date = '$end_nb_date', credit_limit = '$credit_limit', update_detail = '$update_detail', allow_cashdesk = '$allow_cashdesk', warehouse_id = '$warehouse_id' WHERE users=" . $user;
        $db->setQuery($q);
        $db->Query();

        
    }

    
    public static function money_add($price , $user)
    {
        $db = JFactory::getDBO();
        $q = 'UPDATE #__vmapi_balans SET money=money+' . $price . ' WHERE users=' . $user;
        $db->setQuery($q);
        $db->Query();
        $UA    = new UserAccount();
        $pid   = date('His') . rand(100, 999); 
		if($price > 0)
		{
			$descr = JText::_('CREDIT');
			$UA->AddMoneyEasy($user, $price, $pid, $descr);
		}
		else
		{
			$descr = JText::_('DEBIT'); 
			$UA->WithdrawMoney($user, -$price, $pid, $descr);			
		}
    }
    
    public static function loadVid($vid)
    {
        if ($vid == '') {
            return;
        }
        $db = JFactory::getDBO();
        $q  = 'SELECT * FROM #__balansiptv_pak WHERE id=' . $vid;
        $db->setQuery($q);
        return $db->LoadObject();
    }
    
    
    public static function balansiptv_histor($fsum, $summa, $users, $text, $dk = '+', $vid = 0, $paid_to, $id_kassir, $optdis = 0)
    {
        $db = JFactory::getDBO();
        $q  = 'INSERT INTO #__balansiptv_histor (`fullsumma`,`users`,`content`,`vid`,`summa`,`dk`,`paid_to`,`id_kassir`,`optdis`) 
   VALUES ("'. $fsum .'","'. $users . '","' . $text . '", "' . $vid . '","' . $summa . '", "' . $dk . '", "' . $paid_to . '","'.$id_kassir.'","'.$optdis.'")';
   
   		
   
		
        BillingLogMessage('histor','histor',$q);
        $db->setQuery($q);
        $db->Query();
    }
	
    public static function is_admin($id)
    {
        $db = JFactory::getDBO();
        $q  = 'SELECT group_id FROM `#__user_usergroup_map` WHERE user_id=' . $id;
        $db->setQuery($q);
        $row = $db->LoadObject();		
        if ($row->group_id==8||$row->group_id==7) {			
            return true;
        } 	
        return false;
    }
	
	public static function GetOptDiscount($m)
	{
		$db = JFactory::getDBO();
		$query = "select max(value) as mv from `#__balansiptv_discounts` where name <= $m";
		$db->setQuery($query);  
		$dis = $db->LoadObject();
		//BillingLogMessage('D', 'backend', $query.' dis:'.$dis->mv);
		return $dis->mv;
	}

    public static function getUStatus($id)
    {
        $db = JFactory::getDBO();
        $q  = 'SELECT status FROM `#__vmapi_balans` WHERE users=' . $id;
        $db->setQuery($q);
        $row = $db->LoadObject();		
        return $row->status;
    }

    public static function getSingle($id)
    {
        $db = JFactory::getDBO();
        $q  = 'SELECT count(diler_id) as cc FROM `#__balansiptv_diler` WHERE diler_id<>0 and user_id=' . $id;
		BillingLogMessage('getSingle', 'getSingle', $q);
        $db->setQuery($q);
        $row = $db->LoadObject();		
		if ($row->cc > 0)
			return 1;
		return 1;
    }
	
	public function allowed_freepak($id)
	{
		$db = JFactory::getDBO();
		// select the pack
		$db->setQuery("SELECT * FROM `#__balansiptv_freepak`");
		$loadfreepak = $db->loadObject();
		
		$db->setQuery("SELECT `id` FROM `#__balansiptv` WHERE `allow_freepak` = 1 AND `users` = '".$id."'");
		$result = $db->loadObject();
		if(count($result) > 0) { 
			return true;
		}
		else {
		$db->setQuery("SELECT a.* , b.* FROM `#__balansiptv_pack` as a , `#__balansiptv_diler` as b WHERE DATE(a.data_end) > NOW() AND a.stop = 0 AND a.users = b.user_id AND b.diler_id = '".$id."' GROUP BY (a.users)");
		$result = $db->loadObjectList();
		if(count($result) >= $loadfreepak->user_req) { 
			return true;	
		}
		else {
			return false;	
		}
		}
		
		return false;
		
	}
	
	public function numberof_freepak_users()
	{
		$db = JFactory::getDBO();
		// select the pack
		$db->setQuery("SELECT * FROM `#__balansiptv_freepak`");
		$loadfreepak = $db->loadObject();
		
		$db->setQuery("SELECT * FROM `#__users` WHERE `block` = 0");
		$users = $db->loadObjectList();
		$totalfreeusers = 0;
		
		foreach($users as $u) {
		$db->setQuery("SELECT `id` FROM `#__balansiptv` WHERE `allow_freepak` = 1 AND `users` = '".$u->id."'");
		$result = $db->loadObject();
		if(count($result) > 0) { 
			$totalfreeusers++;
		}
		else {
		$db->setQuery("SELECT a.* , b.* FROM `#__balansiptv_pack` as a , `#__balansiptv_diler` as b WHERE DATE(a.data_end) > NOW() AND a.stop = 0 AND a.users = b.user_id AND b.diler_id = '".$u->id."' GROUP BY (a.users)");
		$result = $db->loadObjectList();
		if(count($result) >= $loadfreepak->user_req) { 
			$totalfreeusers++;	
		}
		else {
			//return false;	
		}
		}
		}
		
		return $totalfreeusers;
		
	}
	
	public function loadfreepack()
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM `#__balansiptv_freepak`");
		$pack = $db->loadObject();
		
		return $pack;
		
	}
	
	public function activeusercount($id)
	{
		$db = JFactory::getDBO();
		// select the pack
		
		$db->setQuery("SELECT a.* , b.* FROM `#__balansiptv_pack` as a , `#__balansiptv_diler` as b WHERE DATE(a.data_end) > NOW() AND a.stop = 0 AND a.users = b.user_id AND b.diler_id = '".$id."' GROUP BY (a.users)");
		$result = $db->loadObjectList();
		return count($result);
		
	}
	
	public function balansiptvdiscountdealer($uid , $mon)
	{
		$db = JFactory::getDBO();
		
		$db->setQuery("SELECT a.* , b.* FROM `#__billing_user_group` as a , `#__billing_group` as b WHERE a.`uid` = '".$uid."' AND a.`group_id` = b.id");
		//echo "SELECT a.* , b.* FROM `#__billing_user_group` as a , `#__billing_group` as b WHERE a.`uid` = '".$uid."' AND a.`group_id` = b.id";
	   	$result = $db->loadObject();
		
		if($result->typediscount == 1) {
			$discount = $result->discount;
		}
		else {
		// select number of users under the dealer
		$db->setQuery("SELECT COUNT(`user_id`) as total FROM `#__balansiptv_diler` WHERE `diler_id` = '".$uid."'");
		$total	=	$db->loadObject()->total;
		
		if(isset($total)) {
		// selecting nearest coupon
		$db->setQuery("SELECT * , (".$total." - `count_users`) as close FROM `#__balansiptv_dilerdiscounts` WHERE count_users <= ".$total." ORDER BY close ASC" , 0 ,1);
		$discount	=	$db->loadObject()->value;
		}
		else {
		$db->setQuery("SELECT * , (".$mon." - `name`) as close FROM `#__balansiptv_discounts` WHERE name <= ".$mon." ORDER BY close ASC" , 0 ,1);
		$discount	=	$db->loadObject()->value;	
		}
		}
		
		return $discount;
	}
	
	function loadconfig1($user)
	{
		$db = JFactory::getDBO();
		
		$allowed_free_pack 	= balansiptvHelper::allowed_freepak($user);
		
		$arr = array();
		$spk = $db->setQuery("SELECT a.* , b.* FROM `#__balansiptv_pack` as a , `#__balansiptv_pak` as b WHERE a.vid = b.id AND a.users = $user AND DATE(a.data_end) > NOW() AND a.stop = 0 GROUP BY a.vid")->loadObjectList();
		

		foreach($spk as $s) {
			$arr[] = $s->vid;
			$grp_ids = $s->grouped_paks;
			$grp_ids = explode(',' , $grp_ids);
			foreach($grp_ids as $gr) {
				if($gr != '') {
				$arr[] = $gr;
				}
			}
		}
		
		
		
		if($allowed_free_pack) {
			$db->setQuery("SELECT id , port, caid, ident1, ident2, ident3, ident4, grouped_paks FROM `#__balansiptv_freepak`");
			$arr2 = $db->loadObjectList();
			foreach($arr2 as $ar2) {
				$grp_ids = $ar2->grouped_paks;
				$grp_ids = explode(',' , $grp_ids);
				foreach($grp_ids as $gr) {
					if($gr != '') {
					$arr[] = $gr;
					}
				}
			}
		}
		
		array_unique($arr);
		
		$str = '';
		$where = '';
		$str = implode(',' , $arr);
		
		if($str_fr != '') {
			$str.=','.$str_fr;	
		}
		
		if($str != '') {
			$where = ' WHERE `id` IN ( '.$str.')' ;
		}
		else {
			$where = ' WHERE 1 =2' ;	
		}

		$db->setQuery("SELECT id , grouped_paks FROM `#__balansiptv_pak` ".$where);
		$arr1 = $db->loadObjectList();
		
		
		
		if(isset($arr2)) {
		return array_merge($arr1 , $arr2);
		}
		else {
		return $arr1;	
		}
	}
	
	function finddealerofregularuser($uid)
	{
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `diler_id` FROM `#__balansiptv_diler` WHERE `user_id` = '".$uid."'");
		return $db->loadObject()->diler_id;
		
	}
	
	function getCached($url , $type , $id , $delete , $offset) {
		$cache_root_folder = JPATH_CACHE.DIRECTORY_SEPARATOR.'vmapi';
		$cache_filters_folder = $cache_root_folder.DIRECTORY_SEPARATOR.'filters_all';
		$cache_vendors_folder = $cache_root_folder.DIRECTORY_SEPARATOR.'vendors';
		$cache_products_folder = $cache_root_folder.DIRECTORY_SEPARATOR.'products';
		$cache_product_folder = $cache_root_folder.DIRECTORY_SEPARATOR.'product';
		$cache_product_pictures_folder = $cache_root_folder.DIRECTORY_SEPARATOR.'product_pictures';
		
		// checking if folder not exists
		if(!is_dir($cache_root_folder)) {
			mkdir($cache_root_folder , 0777);
		}
		if(!is_dir($cache_filters_folder)) {
			mkdir($cache_filters_folder , 0777);
		}
		if(!is_dir($cache_vendors_folder)) {
			mkdir($cache_vendors_folder , 0777);
		}
		if(!is_dir($cache_products_folder)) {
			mkdir($cache_products_folder , 0777);
		}
		if(!is_dir($cache_product_folder)) {
			mkdir($cache_product_folder , 0777);
		}
		if(!is_dir($cache_product_pictures_folder)) {
			mkdir($cache_product_pictures_folder , 0777);
		}
		
		if($type == 'filters_all') {
			$cache_file = $cache_filters_folder.DIRECTORY_SEPARATOR.$id.'.json';
		}
		if($type == 'vendors') {
			$cache_file = $cache_vendors_folder.DIRECTORY_SEPARATOR.$id.'.json';
		}
		if($type == 'products') {
			if($offset == 0) {
				$cache_file = $cache_products_folder.DIRECTORY_SEPARATOR.$id.'.json';
			}
			else {
				$cache_file = $cache_products_folder.DIRECTORY_SEPARATOR.$id.'-'.$offset.'.json'; 
			}
		}
		if($type == 'product') {
			$cache_file = $cache_product_folder.DIRECTORY_SEPARATOR.$id.'.json';
		}
		if($type == 'product_pictures') {
			$cache_file = $cache_product_pictures_folder.DIRECTORY_SEPARATOR.$id.'.json';
		}
		
		if($delete == 1) {
			if(file_exists($cache_file)) {
				unlink($cache_file);
			}
		}
		if(!file_exists($cache_file)) {
			if ( copy($url, $cache_file) ) {
				echo "Copy success!";
			}else{
				echo "Copy failed.";
			}
		}
		
		return json_decode(file_get_contents($cache_file));
		
	}
	
	function ifCashDesk($uid) {
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM `#__vmapi_cashdesks` WHERE `cashdesk_user_id` = '".$uid."'");
		if(count($db->loadObject()) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function getCashDeskId($uid) {
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM `#__vmapi_cashdesks` WHERE `cashdesk_user_id` = '".$uid."'");
		if(count($db->loadObject()) > 0) {
			return $db->loadObject()->id;
		}
		else {
			return 0;
		}
	}
	
	function getCashDeskAssignedUsersList($cashdesk_id) {
		$db = JFactory::getDBO();
		$query = 'SELECT username AS text, id AS value '
		  . ' FROM `#__users` WHERE `id` IN (SELECT `user_id` FROM `#__vmapi_cashdesk_users` WHERE `cashdesk_id` = '.$cashdesk_id.') '
		  . ' ORDER BY `username`  ';

		$db->setQuery( $query );
		$cashdeskusers = $db->loadObjectList();
		
		return JHTML::_('select.genericlist',  $cashdeskusers, 'cashdeskuser', 'class="inputbox" required="required" id="cashdeskuser"', 'value', 'text', '', '' );
	}
	
}
?>
