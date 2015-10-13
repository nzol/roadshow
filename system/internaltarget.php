<?php

session_start();
include('globalfunctions.php');

@$SessionId = $_GET['SessionId'];

	

	$action = $_GET['Action'];


	switch ($action) {
		
		case "getScriptField":

			$ScriptAlias = $_GET['ScriptAlias'];
			$Field = $_GET['Field'];

			$ScriptFieldVal = getScriptInfo($ScriptAlias,$Field);

			echo $ScriptFieldVal;

		break;


		case "requestScript":

			$ScriptAlias = $_GET['ScriptAlias'];
			$Params = $_GET['Params'];
			
			//echo $ScriptAlias;


			$_SESSION['CLICKA_currentSessionId']=$SessionId;

			/*$ParamString = "str=str2";

			$ParamSplit = explode(";", $Params);
			foreach($ParamSplit as $ParamBlock) {
				$ParamBlockSplit = explode(":",$ParamBlock);
				$ParamName = $ParamBlockSplit[0];
				$ParamValue = $ParamBlockSplit[1];
				$ParamString = $ParamString . "&" . $ParamName . "=" . $ParamValue;
			}*/

			$getSession = $Mysql->query("SELECT * FROM users_sessions WHERE SessionToken='$SessionId'");


			if(checkUserScriptAccess($ScriptAlias,$SessionId)) {
				$getScriptPath = getScriptInfo($ScriptAlias,"Path");
				
				$returnSource = HTTPGet($CurrentURLBase . $getScriptPath,$Params,$SessionId);
				echo $returnSource;
			}
			else if($SessionId=="0") {
				$returnSource = file_get_contents("home.php");
				echo $returnSource;
			}
			else if($getSession->num_rows==0) {
				$returnSource = file_get_contents("sessionExpired.php");
				echo $returnSource;
			}
			else {
				echo "<ERR>System.InternalTarget.requestScript.AccessDenied";
			}

		break;


		case "requestErrorInfo":

			$ErrCode = $_GET['ErrCode'];
			$Field = $_GET['Field'];

			$getError = $Mysql->query("SELECT * FROM errorcodes WHERE ErrorString='$ErrCode'");
			$rsError = $getError->fetch_assoc();

			$RetVal = $rsError[$Field];

			echo $RetVal;

		break;

		case "signIn":

			$Username = $_GET['Username'];
			$Password = md5($_GET['Password']);

			$getUser = $Mysql->query("SELECT * FROM users WHERE EmailAddress='$Username' AND Password='$Password'");

			if($getUser->num_rows==0) {
				echo "<ERR>System.InternalTarget.signIn.AccessDenied";
			}
			else {
				$rsSession = $getUser->fetch_assoc();
				$newSession = createUserSession($rsSession['Id']);
				$_SESSION['CLICKA_currentSessionId']=$newSession;
				$_SESSION['CLICKA_currentUserId']=$rsSession['Id'];
				setcookie("CLICKA_currentSessionId",$newSession,time()+604800);
				echo $newSession;
			}

		break;


		case "setSessionVar":

			$SessionId = $_GET['SessionId'];
			$VarName = $_GET['VarName'];
			$VarVal = $_GET['VarVal'];

			$getExisting = $Mysql->query("SELECT * FROM users_sessions_vars WHERE SessionId='$SessionId' AND VarName='$VarName'");
			if($getExisting->num_rows==0) {

			$addVar = $Mysql->query("INSERT INTO users_sessions_vars (SessionId,VarName,VarVal) VALUES ('$SessionId','$VarName','$VarVal')");

			}
			else {
				$updateVar = $Mysql->query("UPDATE users_sessions_vars SET VarVal='$VarVal' WHERE SessionId='$SessionId' AND VarName='$VarName'");
			}

			echo "success";

		break;


		case "getSessionVar":

			$SessionId = $_GET['SessionId'];
			$VarName = $_GET['VarName'];

			$getVar = $Mysql->query("SELECT * FROM users_sessions_vars WHERE SessionId='$SessionId' AND VarName='$VarName'");
			$rsVar = $getVar->fetch_assoc();

			$VarVal = $rsVar['VarVal'];

			echo $VarVal;

		break;


		case "getLocalityId":

			$LocalitySearch = $_GET['q'];
			$getLocality = $Mysql->query("SELECT * FROM localities WHERE Name='$LocalitySearch'");
            $rsLocality = $getLocality->fetch_assoc();
            $LocalID = $rsLocality['Id'];

            echo $LocalID;

        break;

        case "cookieExpiry":

        	$CookiesExpire = strtotime("+30 days");
			$CookiesExpire = date("D, d M Y H:i:s",$CookiesExpire) . " UTC";

			echo $CookiesExpire;

        break;

       

        case "logout":

        	$delSession = $Mysql->query("DELETE FROM users_sessions WHERE SessionToken='$SessionId'");
			$delSessionVars = $Mysql->query("DELETE FROM users_sessions_vars WHERE SessionId='$SessionId'");

			session_destroy();


        break;

        case "ping":

        	echo "pong";

        break;

	}






?>