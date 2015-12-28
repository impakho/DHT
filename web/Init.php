<?php
$GLOBALS["sql_con"]=NULL;

function OpenDB(){
	@$GLOBALS['sql_con']=mysql_connect("localhost","root","mysql_password");
	if (!$GLOBALS['sql_con']) die("Could not connect: ".mysql_error());
	mysql_select_db("dht",$GLOBALS['sql_con']);
}

function RunDB($sql_query){
	mysql_query("SET NAMES 'utf8'");
	$result=mysql_query($sql_query);
	return $result;
}

function CloseDB(){
	mysql_close($GLOBALS['sql_con']);
}

function getKeyword($title){
	require('./pscws.class.php');
	$pscws = new PSCWS4();
	$pscws->set_dict('./scws/dict.utf8.xdb');
	$pscws->set_rule('./scws/rules.utf8.ini');
	$pscws->set_ignore(true);
	$pscws->send_text($title);
	$words = $pscws->get_tops();
	$res='';
	foreach ($words as $val) {
		$res.='|'.$val['word'];
	}
	$pscws->close();
	return substr($res,1);
}

function format_date($time){
	if (!is_numeric($time)){
		if (strpos($time,"-")===false) return '未知';
		$time=strtotime($time);
	}
	$t=time()-$time;
	$f=array(
		'31536000'=>'年',
		'2592000'=>'个月',
		'604800'=>'星期',
		'86400'=>'天',
		'3600'=>'小时',
		'60'=>'分钟',
		'1'=>'秒'
	);
	foreach ($f as $k=>$v){
		if (0 !=$c=floor($t/(int)$k)) {
			return $c.$v.'前';
		}
	}
}

function format_date_utc($time){
	if (!is_numeric($time)){
		if (strpos($time,"-")===false) return '未知';
		$time=strtotime($time);
	}
	return date('Y-m-d H:i:s',$time);
}

function format_size($sizeb){
	if (strpos($sizeb,"|")!==false){
		$size="";
		$sizeb_split=split("\|",$sizeb);
		for ($i=0;$i<count($sizeb_split);$i++){
			if ($i!=0) $size.='|';
			$size.=format_size($sizeb_split[$i]);
		}
		return $size;
	}else{
		$sizekb = $sizeb / 1024;
		$sizemb = $sizekb / 1024;
		$sizegb = $sizemb / 1024;
		$sizetb = $sizegb / 1024;
		if ($sizeb > 1) {$size = round($sizeb,2) . " B";}
		if ($sizekb > 1) {$size = round($sizekb,2) . " KB";}
		if ($sizemb > 1) {$size = round($sizemb,2) . " MB";}
		if ($sizegb > 1) {$size = round($sizegb,2) . " GB";}
		if ($sizetb > 1) {$size = round($sizetb,2) . " TB";}
		return $size;
	}
}
?>