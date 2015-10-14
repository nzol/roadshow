HI<?php

$Mysql = new Mysqli("localhost","jsm","7aqCbgQ2k","zadmin_roadshow");

if(isset($_SESSION['CLICKA_currentUserId'])) {
	$CurrentUserId = $_SESSION['CLICKA_currentUserId'];
}
else if(isset($_COOKIE['CLICKA_currentUserId'])) {
	$CurrentUserId = $_COOKIE['CLICKA_currentUserId'];
}
else {
	$CurrentUserId = "0";
}

$CurrentURLBase = "http://roadshow.kiwiupload.com/";

// System Sessions & Tokens

function checkSystemSession() {
	/*global $Mysql;
	if(isset($_SESSION['CLICKA_currentSystemToken'])) {
		return "fine";
	}
	else {
		$newToken = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0011123434656787890"),0,15);
		$_SESSION['CLICKA_currentSystemToken']=$newToken;
		$addToken = $Mysql->query("INSERT INTO system_tokens (TokenString) VALUES ('$newToken')");
	}*/
	return true;
}


function validateToken() {
	/*$TokenString = $_SESSION['CLICKA_currentSystemToken'];
	global $Mysql;
	$getToken = $Mysql->query("SELECT * FROM system_tokens WHERE TokenString='$TokenString'");
	if($getToken->num_rows==0) {
		return false;
	}
	else {
		return true;
	}*/
	return true;
}


function checkUserSession($SessionId) {

	global $Mysql;

	$getSession = $Mysql->query("SELECT * FROM users_sessions WHERE SessionToken='$SessionId'");

	if($getSession->num_rows==0) {
		return false;
	}
	else {
		return true;
	}

}

// Scripts

function getScriptInfo($ScriptAlias,$Field) {

	global $Mysql;

	$getScript = $Mysql->query("SELECT * FROM scripts WHERE Alias='$ScriptAlias'");
	$rsScript = $getScript->fetch_assoc();

	$RetVal = $rsScript[$Field];

	return $RetVal;

}

// Rights

function getSessionUser($SessionId) {

	global $Mysql;

	$getSession = $Mysql->query("SELECT * FROM users_sessions WHERE SessionToken='$SessionId'");
	$rsSession = $getSession->fetch_assoc();

	$UserId = $rsSession['UserId'];

	return $UserId;

}

function userHoldsRight($RightId,$SessionId) {

	global $Mysql;
	
	$UserId = getSessionUser($SessionId);

	$getUserRight = $Mysql->query("SELECT * FROM rights_assigned WHERE UserId='$UserId' AND RightId='$RightId'");

	if($getUserRight->num_rows==0) {
		return false;
	}
	else {
		return true;
	}
}

function checkUserScriptAccess($ScriptAlias,$SessionId) {

	global $Mysql;

	$getScriptRight = $Mysql->query("SELECT * FROM scripts WHERE Alias='$ScriptAlias'") or die($Mysql->error);
	$rsScriptRight = $getScriptRight->fetch_assoc();

	$ScriptRight = $rsScriptRight['RightId'];

	if(userHoldsRight($ScriptRight,$SessionId)) {
		return true;
	}
	else {
		return false;
	}

}


function createUserSession($UserId) {

	global $Mysql;

	$SessionToken = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123457689"),0,31);

	$addSession = $Mysql->query("INSERT INTO users_sessions (UserId,SessionToken) VALUES ('$UserId','$SessionToken')");

	return $SessionToken;

}


function destroyUserSession($SessionId) {

	global $Mysql;

	$delSession = $Mysql->query("DELETE FROM users_sessions WHERE SessionToken='$SessionId'");

	session_destroy();

}

function HTTPGet($url,$params,$sessionid="0") {

	$paramssplit = explode(";", $params);
	$buildqueryarr = array();

	foreach($paramssplit as $paramline) {
		$param_split = explode(":",$paramline);
		//echo print_r($param_split);
		$buildqueryarr[$param_split[0]]=$param_split[1];
	}

	$buildqueryarr["CLICKA_currentSessionId"]=$sessionid;
	$buildqueryarr["SessionId"]=$sessionid;

	$postdata = http_build_query($buildqueryarr);

	

	$opts = array('http' =>
	    array(
	        'method'  => 'POST',
	        'header'  => 'Content-type: application/x-www-form-urlencoded',
	        'content' => $postdata
	    )
		);

	

	$context  = stream_context_create($opts);

	$result = file_get_contents($url, false, $context);

	return $result;

}

function getSessionVar($SessionId,$VarName) {

	global $Mysql;

	$getVar = $Mysql->query("SELECT * FROM users_sessions_vars WHERE SessionId='$SessionId' AND VarName='$VarName'");
	$rsVar = $getVar->fetch_assoc();

	if($getVar->num_rows==0) {

		$VarVal = "notfound";

	}
	else {

		$VarVal = $rsVar['VarVal'];

	}

	

	return $VarVal;

}


function setSessionVar($SessionId,$VarName,$VarVal) {

	global $Mysql;

	$getSession = $Mysql->query("SELECT * FROM users_sessions_vars WHERE SessionId='$SessionId' AND VarName='$VarName'");
	if($getSession->num_rows==0) {
		$addSessionVar = $Mysql->query("INSERT INTO users_sessions_vars (SessionId,VarName,VarVal) VALUES ('$SessionId','$VarName','$VarVal')");
	}
	else {
		$updateSession = $Mysql->query("UPDATE users_sessions_vars SET VarVal='$VarVal' WHERE SessionId='$SessionId' AND VarName='$VarName'");
	}


	return true;

}

function getUserInfo($UserId,$Field) {

	global $Mysql;

	$getUser = $Mysql->query("SELECT * FROM users WHERE Id='$UserId'");
	$rsUser = $getUser->fetch_assoc();

	$Value = $rsUser[$Field];

	return $Value;


}



function nav($to) {
	$browser = @get_browser(null, true);
	$browserName = $browser['browser'];
	if($browserName=="IE") {
		echo "<script language=\"javascript\">
				window.location = '$to'
				</script>";
	}
	else {
	
		echo "<meta http-equiv=\"Refresh\" content=\"0;$to\" />";
		
	}
}






?>