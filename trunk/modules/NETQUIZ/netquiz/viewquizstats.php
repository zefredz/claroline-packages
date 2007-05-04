<?php

### debut debug #################################################################################################

// inclusion du noyeux de claroline
include "../../../claroline/inc/claro_init_global.inc.php";
require_once get_path('includePath').'/lib/user.lib.php';

// lib
require_once "../lib/netquiz.class.php";

// recupération des données utilisateurs
$current_user_data = user_get_properties(claro_get_current_user_id());

// Vérification que l'utilisateur soit enregistré
if(!claro_is_user_authenticated()) 
{
	claro_die(get_lang("Not allowed"));
	//claro_disp_auth_form();
}
else
{

### fin debug #################################################################################################

    include_once("langr.inc.php");
    include_once("settings.inc.php");
    include_once("functions.inc.php");
    //include("isauth.php");
    
    //Variables
    $iIDQuiz = $_GET["id"];
    $sGetParam = "?id=$iIDQuiz";
    
	/*
    //Connection
    $oServerConn = mysql_connect($sMySQLLocation,$sMySQLUser,$sMySQLPassword);
    if(!$oServerConn){
        die(mysql_error());
    }
    
    mysql_select_db($sMySQLDatabase);
   
    //Status or password change
    $bUpdateStatPass = false;
    if(isset($_GET["stat"])){
        $bUpdateStatPass = true;
        $iActif = $_GET["stat"];
        
        $sQuery = "update nq_quizs set Actif = $iActif where IDQuiz = $iIDQuiz";
    
        executeQuery($sQuery);
    }
  
    if(isset($_GET["pass"])){
        $bUpdateStatPass = true;
        $sPassword = toSQLString(fromGPC($_GET["pass"]),false);
        
        $sQuery = "update nq_quizs set Password = $sPassword where IDQuiz = $iIDQuiz";
    
        executeQuery($sQuery);
    }

  */
  
	// Declaration de la Class netquiz		
	$netquiz = new netquiz();

	//Status change
    $bUpdateStatPass = false;
    if(isset($_GET["stat"])){
        $bUpdateStatPass = true;
        $iActif = $_GET["stat"];
        
		$netquiz->setIdQuiz( $iIDQuiz );
		$netquiz->setActif( $iActif );
		$netquiz->updateQuizsStatus();
		
    }
	
	// Class netquiz : select quiz info
	$netquiz->setIdQuiz( $iIDQuiz );
	$QuizInfo = $netquiz->selectQuizInfo();

    $sQuizName = $QuizInfo['QuizName'];
    $dVersionDate = date($sDefaultDateFormat,$QuizInfo['VersionDate']);
    $sPassword = $QuizInfo['Password'];
    $iActif = $QuizInfo['Actif'];
    $fPonderationTotal = $QuizInfo['PonderationTotal'];
    
    if($iActif == 0){
        $sCheckedActif = "";
        $sCheckedInactif = " checked";
    }else{
        $sCheckedActif = " checked";
        $sCheckedInactif = "";
    }

	// Nombre de participations et premiere date
	$netquiz->setIdQuiz( $iIDQuiz );
	
	$NumberParticipationsAndDate = $netquiz->selectNumberParticipationsAndDate();
	$NumberParticipationsAndDate->setPonderationTotal( $fPonderationTotal );
	$NumberParticipationsAndDate->compute();
	

	//$iNbParticipations = count($NumberParticipationsAndDate);

	$sTitle = $sQuizName . "&nbsp;(&nbsp;$dVersionDate&nbsp;)";
	
	
	
	/*	
    if($iNbParticipations == 0){
        $dDateLastParticipation = "-";
        
        //Average
        $sAverageHTML = getFormatedScore(0,$fPonderationTotal);
        
        //Mediane
        $sMedianeHTML = getFormatedScore(0,$fPonderationTotal);
        
        $iNbPartGT60 = "-";
        
    }else{
        $iFinal = mysql_result($oRS,0,"Final");
        if(intval($iFinal) == 1){
            $dDateLastParticipation =  date($sDefaultDateHourFormat,mysql_result($oRS,0,"ParticipationDate"));
        }else{
            $dDateLastParticipation = "-";
        }
        
        //Score array
        $iPointages = array();
        for($i = 0;$i < $iNbParticipations;$i++){
            $iPointages[$i] = mysql_result($oRS,$i,"PointageTotal");
        }
        
        sort($iPointages);
        
        //Average
        $fAverage = average($iPointages);
        $sAverageHTML = getFormatedScore($fAverage,$fPonderationTotal);
        
        //Mediane
        $fMediane = mediane($iPointages);
        $sMedianeHTML = getFormatedScore($fMediane,$fPonderationTotal);
    
        //Number of participations with a score gretter than 60%
        $iNbPartGT60 = nbGT($iPointages,(0.6 * $fPonderationTotal));
    }  
	
	
    $sTitle = $sQuizName . "&nbsp;(&nbsp;$dVersionDate&nbsp;)";

	
	//Info du quiz
    $sQuery =   "select QuizName, UNIX_TIMESTAMP(VersionDate) AS VersionDate, Password, Actif, sum(nq_questions.Ponderation) as PonderationTotal " .
                "from nq_quizs, nq_questions " .
                "where nq_quizs.IDQuiz = $iIDQuiz and nq_quizs.IDQuiz = nq_questions.IDQuiz and nq_questions.Active = 1 " .
                "group by nq_quizs.IDQuiz";
    	
    $oRS = executeQuery($sQuery);
    
    $sQuizName = htmlentities(mysql_result($oRS,0,"QuizName"));
    $dVersionDate = date($sDefaultDateFormat,mysql_result($oRS,0,"VersionDate"));
    $sPassword = htmlentities(mysql_result($oRS,0,"Password"));
    $iActif = mysql_result($oRS,0,"Actif");
    $fPonderationTotal = mysql_result($oRS,0,"PonderationTotal");
    
    if($iActif == 0){
        $sCheckedActif = "";
        $sCheckedInactif = " checked";
    }else{
        $sCheckedActif = " checked";
        $sCheckedInactif = "";
    }


    //Nombre de participations et premiere date
    $sQuery =   "select UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDate, nq_participants.Final as Final, nq_participants.IDParticipant as IDParticipant, " .
                "sum(nq_participations.Pointage) as PointageTotal " .
                "from nq_participants " .
                "left join nq_participations using (IDParticipant) " .
                "right join nq_questions on nq_participations.IDQuestion = nq_questions.IDQuestion " .
                "where nq_questions.Active = 1 and " .
                "nq_participants.Actif = 1 and " .
                "nq_participants.IDQuiz = $iIDQuiz " .
                "group by nq_participants.IDParticipant order by nq_participants.ParticipationDate desc";
                
    $oRS = executeQuery($sQuery);
	
    $iNbParticipations = mysql_num_rows($oRS);

	
	
	
	print('<pre>');
	var_dump($iNbParticipations);
	print('</pre>');
	
	
	
	
    if($iNbParticipations == 0){
        $dDateLastParticipation = "-";
        
        //Average
        $sAverageHTML = getFormatedScore(0,$fPonderationTotal);
        
        //Mediane
        $sMedianeHTML = getFormatedScore(0,$fPonderationTotal);
        
        $iNbPartGT60 = "-";
        
    }else{
        $iFinal = mysql_result($oRS,0,"Final");
        if(intval($iFinal) == 1){
            $dDateLastParticipation =  date($sDefaultDateHourFormat,mysql_result($oRS,0,"ParticipationDate"));
        }else{
            $dDateLastParticipation = "-";
        }
        
        //Score array
        $iPointages = array();
        for($i = 0;$i < $iNbParticipations;$i++){
            $iPointages[$i] = mysql_result($oRS,$i,"PointageTotal");
        }
        
        sort($iPointages);
        
        //Average
        $fAverage = average($iPointages);
        $sAverageHTML = getFormatedScore($fAverage,$fPonderationTotal);
        
        //Mediane
        $fMediane = mediane($iPointages);
        $sMedianeHTML = getFormatedScore($fMediane,$fPonderationTotal);
    
        //Number of participations with a score gretter than 60%
        $iNbPartGT60 = nbGT($iPointages,(0.6 * $fPonderationTotal));
    }  
    $sTitle = $sQuizName . "&nbsp;(&nbsp;$dVersionDate&nbsp;)";
/**/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="includes/main.css" />
        <script src="includes/functions.js" language="javascript"></script>
        <title><?php echo $sLR["title"]; ?></title>
    </head>
       	<body style="margin:0px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                            <td align="center" style="background:url(images/bandetitre_pat.jpg) repeat-x;" height="56">
                                    <img src="images/bandetitre.jpg" />
                            </td>
                    </tr>
                    <tr>
                            <td width="100%" height="100">
                                    &nbsp;
                            </td>
                    </tr>
                    <tr>
                            <td align="center">
                                <table width="750" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="100%" height="30">
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td height="30" width="50%" valign="middle" align="left"><font class="section_header"><?php echo $sLR["q_title"]; ?></font></td>
                                                    <td height="30" width="50%" valign="middle" align="right"><a class="small" href="quizlist.php"><?php echo $sLR["menu_bql_link"]; ?></a></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="5" valign="middle"><img src="images/ligne.gif" width="100%" height="1" alt="" border="0" /></td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="30" valign="middle" align="right">
                                            &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="50"><img src="images/spacer.gif" width="100%" height="50"></td>
                                    </tr>
                                    <tr>
                                        <td width="100%" valign="middle" align="left">
                                            <font class="list_header"><?php echo($sTitle); ?></font>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="30"><img src="images/spacer.gif" width="100%" height="30"></td>
                                    </tr>
                                    <tr>
                                        <td width="100%" valign="middle" align="left">
                                            <form method="get" action="viewquizstats.php<?php echo $sGetParam ?>" style="display:inline;" id="frmStatPass">
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td width="40%" align="left">
                                                        <strong><?php echo $sLR["q_sta_lbl"]; ?> : </strong><input type="radio" onchange="postForm('frmStatPass')" name="stat" id="stat1" value="1"<?php echo $sCheckedActif ?> /> <label for="stat1"><?php echo $sLR["q_staa_lbl"]; ?></label> <input type="radio" onchange="postForm('frmStatPass')" name="stat" id="stat0" value="0"<?php echo $sCheckedInactif ?> /> <label for="stat0"><?php echo $sLR["q_staia_lbl"]; ?></label>
                                                    </td>
                                                    <td width="60%" align="right">
                                                        <!-- <strong><?php echo $sLR["q_pw_lbl"]; ?> : </strong><input type="text" name="pass" size="20" maxlength="45" value="<?php echo $sPassword ?>" /> <font class="blue">></font> <a href="javascript:postForm('frmStatPass');"><?php echo $sLR["q_acc_lbl"]; ?></a> -->
                                                    </td>
                                                </tr>
                                            </table>
                                            <input type="hidden" value="<?php echo $iIDQuiz ?>" name="id" />
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100%" height="50"><img src="images/spacer.gif" width="100%" height="50"></td>
                                    </tr>
                                    <tr>
                                        <td width="100%">
                                            <table width="745" cellpadding="0" cellspacing="0" border="0">
                                                <?php generateHeader($sQuizName,$dVersionDate,$sGetParam,0)?>
                                                <tr>
                                                    <td width="100%" class="ContentCell" align="left" style="padding:5px;">
                                                        <br />
                                                        <strong><?php echo $sLR["q_nbp_lbl"]; ?> :</strong> <?php echo $NumberParticipationsAndDate->getNbParticipations(); ?><br />
                                                        <strong><?php echo $sLR["q_ls_lbl"]; ?> :</strong> <?php echo $NumberParticipationsAndDate->getDateLastParticipation(); ?><br />
                                                        <strong><?php echo $sLR["q_avg_lbl"]; ?> :</strong> <?php echo $NumberParticipationsAndDate->getAverageHTML(); ?><br />
                                                        <strong><?php echo $sLR["q_med_lbl"]; ?> :</strong> <?php echo $NumberParticipationsAndDate->getMedianeHTML(); ?><br />
                                                        <strong><?php echo $sLR["q_gt60_lbl"]; ?> :</strong> <?php echo $NumberParticipationsAndDate->getNbPartGT60(); ?><br />
                                                        <br />
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                    </tr>
            </table>
    </body>
</html>

<?php
    //mysql_close();
}	
?>