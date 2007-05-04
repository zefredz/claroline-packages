<?php

$sRequiredTables =      array(  "nq_participants",
                                "nq_participations",
                                "nq_questions",
                                "nq_quizs",
                                "nq_users");

$sCreationQueryParticipants =   "CREATE TABLE `nq_participants` (
				    `IDParticipant` int(10) unsigned NOT NULL auto_increment,
				    `Prenom` varchar(45) NOT NULL default '',
				    `Nom` varchar(45) NOT NULL default '',
				    `Groupe` varchar(45) NOT NULL default '',
				    `Matricule` varchar(45) NOT NULL default '',
				    `Courriel` varchar(45) NOT NULL default '',
				    `Coordonnees` longtext,
				    `ParticipationDate` datetime NOT NULL default '0000-00-00 00:00:00',
				    `Final` int(11) NOT NULL default '0',
				    `IDQuiz` int(11) NOT NULL default '0',
				    `Actif` int(11) NOT NULL default '1',
				    PRIMARY KEY  (`IDParticipant`)
				  ) ENGINE=MyISAM";
                                
$sCreationQueryParticipations = "CREATE TABLE `nq_participations` (
				    `IDParticipant` int(10) unsigned NOT NULL default '0',
				    `IDQuestion` int(10) unsigned NOT NULL default '0',
				    `Pointage` float NOT NULL default '0',
				    `PointageAuto` float NOT NULL default '0',
				    `ReponseHTML` longtext NOT NULL,
				    PRIMARY KEY  (`IDParticipant`,`IDQuestion`)
				  ) ENGINE=MyISAM";
                                
$sCreationQueryQuestions =      "CREATE TABLE `nq_questions` (
				    `IDQuestion` int(10) unsigned NOT NULL auto_increment,
				    `QuestionName` longtext NOT NULL,
				    `QuestionType` varchar(45) NOT NULL default '',
				    `QuestionTypeTD` varchar(45) NOT NULL default '',
				    `Ponderation` float unsigned NOT NULL default '0',
				    `EnonceHTML` longtext NOT NULL,
				    `ReponseHTML` longtext,
				    `ReponseXML` longtext NOT NULL,
				    `IDQuiz` int(10) unsigned NOT NULL default '0',
				    `NoQuestion` int(10) unsigned NOT NULL default '0',
				    `Active` int(11) NOT NULL default '1',
				    PRIMARY KEY  (`IDQuestion`)
				  ) ENGINE=MyISAM";
                                
$sCreationQueryQuizs =          "CREATE TABLE `nq_quizs` (
				    `IDQuiz` int(10) unsigned NOT NULL auto_increment,
				    `QuizIdent` varchar(45) NOT NULL default '',
				    `QuizVersion` varchar(45) NOT NULL default '',
				    `QuizName` varchar(45) NOT NULL default '',
				    `NbQuestions` int(10) unsigned NOT NULL default '0',
				    `VersionDate` datetime NOT NULL default '0000-00-00 00:00:00',
				    `Password` varchar(45) NOT NULL default '',
				    `Title` longtext,
				    `Auteur` longtext,
				    `Actif` int(11) NOT NULL default '0',
				    PRIMARY KEY  (`IDQuiz`)
				  ) ENGINE=MyISAM";
                                
$sCreationQueryUsers =          "CREATE TABLE `nq_users` (
				    `IDUser` int(10) unsigned NOT NULL auto_increment,
				    `Username` varchar(45) NOT NULL default '',
				    `LoginPassword` varchar(45) NOT NULL default '',
				    PRIMARY KEY  (`IDUser`)
				  ) ENGINE=MyISAM";

$sCreationQuerys = 		array(   	$sCreationQueryParticipants,
		                            $sCreationQueryParticipations,
		                            $sCreationQueryQuestions,
		                            $sCreationQueryQuizs,
		                            $sCreationQueryUsers);

function checkTablesStruct($sRequiredTables){
    $sTableList = array();
    $bStructOK = true;
    
    $oRS = mysql_query("show tables");
    $iNbResult = mysql_num_rows($oRS);
    
    for($i = 0;$i < $iNbResult;$i++){
        $sTableList[$i] = mysql_result($oRS,$i);
    }
    
    for($i = 0;$i < count($sRequiredTables);$i++){
        if(!isInArray($sRequiredTables[$i],$sTableList)){
            $bStructOK = false;
        }
    }
    
    return $bStructOK;
}

function createTablesStruct($sTablesToDrop,$sCreationQuerys,$sAdminUser,$sAdminPassword){
    //Drop tables
    for($i = 0;$i < count($sTablesToDrop);$i++){
        $sQuery = "DROP TABLE IF EXISTS `$sTablesToDrop[$i]`";
        
        $bSuccess = mysql_query($sQuery);
        if(!$bSuccess){
            die(mysql_error());
        }
    }
    
    //Create tables
    for($i = 0;$i < count($sCreationQuerys);$i++){
        $sQuery = $sCreationQuerys[$i];
        
        $bSuccess = mysql_query($sQuery);
        if(!$bSuccess){
            die(mysql_error());
        }
    }
    
    //Insert user
    $sAdminUser = toSQLString($sAdminUser,false);
    $sAdminPassword = toSQLString($sAdminPassword,false);
    
    $sQuery = "insert into nq_users (Username,LoginPassword) values($sAdminUser,password($sAdminPassword))";
    
    $bSuccess = mysql_query($sQuery);
    if(!$bSuccess){
        die(mysql_error());
    }
}
function showIdentForm($bFirstTime,$sMsg){
    global $sLR;
    
    if(!$bFirstTime){
	include_once("functions.inc.php");
	
	//Username
	$iIDUser = $_SESSION["IDUser"];
	$sQuery =   "select * from nq_users where IDUser = $iIDUser";
	
	$oRS = executeQuery($sQuery);
	
	$sUserName = mysql_result($oRS,0,"Username");
	$sFormAction = "saveuser.php";
    }else{
	$sFormAction = "createtables.php";
	$sUserName = "";
    }
    ?>
	<form action="<?php echo $sFormAction; ?>" method="post" onsubmit="return checkIdentForm();">
	    <div align="center">
		    <table cellpadding="0" cellspacing="0" border="0" width="450" ID="Table1">
			    <tr>
				    <td width="100%" height="125"><img src="images/spacer.gif" width="100%" height="125" /></td>
			    </tr>
			    <tr>
				    <td width="100%" align="center">
					    <?php if($bFirstTime) { ?>
						<font class="section_header"><?php echo $sLR["log_title"]; ?></font>
					    <?php }else{ ?>
						<font class="section_header"><?php echo $sLR["log_cpw_title"]; ?></font>
					    <?php } ?>
				    </td>
			    </tr>
			    <tr>
				    <td width="100%" height="30"><img src="images/spacer.gif" width="100%" height="30" /></td>
			    </tr>
			    <tr>
				    <td width="100%" class="InputCell" style="padding:0px;">
					    <table cellpadding="0" cellspacing="0" border="0" width="100%" ID="Table2">
						    <?php if($bFirstTime) { ?>
						    <tr>
							    <td width="100%" colspan="2" height="10" class="WarningCell">
								    <img src="images/spacer.gif" width="100%" height="10" />
							    </td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" align="center" class="WarningCell">
								    <?php echo $sLR["log_ft_msg"]; ?>
							    </td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" height="10" class="WarningCell">
								    <img src="images/spacer.gif" width="100%" height="10" />
							    </td>
						    </tr>
						    <?php } ?>
						    <tr>
							    <td width="100%" colspan="2" height="10">
								    <img src="images/spacer.gif" width="100%" height="10" />
							    </td>
						    </tr>
						    <tr>
							    <td width="50%" align="right" valign="middle">
								    <strong><?php echo $sLR["log_un_lbl"]; ?> :&nbsp;&nbsp;</strong>
							    </td>
							    <td width="50%" align="left" valign="middle">
								<?php if($bFirstTime){ ?>
								    <input type="text" name="txtUsername" id="txtUsername" value="" />
								<?php }else{
								    echo "<strong>$sUserName</strong>";
								    echo "<input type=\"hidden\" name=\"txtUsername\" id=\"txtUsername\" value=\"$sUserName\" />";
								} ?>
							    </td>
						    </tr>
						    <?php if(!$bFirstTime){ ?>
							<tr>
							    <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
							</tr>
							<tr>
								<td width="50%" align="right" valign="middle">
									<strong><?php echo $sLR["log_ops_lbl"]; ?> :&nbsp;&nbsp;</strong>
								</td>
								<td width="50%" align="left" valign="middle">
									<input type="password" name="txtOldPassword" id="txtOldPassword" />
								</td>
							</tr>
						    <?php } ?>
						    <tr>
							    <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
						    </tr>
						    <tr>
							    <td width="50%" align="right" valign="middle">
								    <strong><?php echo $sLR["log_pw_lbl"]; ?> :&nbsp;&nbsp;</strong>
							    </td>
							    <td width="50%" align="left" valign="middle">
								    <input type="password" name="txtPassword1" id="txtPassword1" />
							    </td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
						    </tr>
						    <tr>
							    <td width="50%" align="right" valign="middle">
								    <strong><?php echo $sLR["log_rpw_lbl"]; ?> :&nbsp;&nbsp;</strong>
							    </td>
							    <td width="50%" align="left" valign="middle">
								    <input type="password" name="txtPassword2" id="txtPassword2" />
							    </td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" height="20">
								    <img src="images/spacer.gif" width="100%" height="20" />
							    </td>
						    </tr>
						    <?php if(isset($sMsg)){ ?>
						    <tr>
							    <td width="100%" colspan="2" align="center">
								    <font class="red"><?php echo $sMsg; ?></font>
							    </td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" height="20">
								    <img src="images/spacer.gif" width="100%" height="20" />
							    </td>
						    </tr>
						    <?php } ?>
						    <tr>
							    <td width="100%" colspan="2" align="center">
								    <?php
									if(!$bFirstTime){
									    echo "<input type=\"button\" value=\"" . $sLR["log_rtn_btn"] . "\" onclick=\"cancel()\" />";
									}
								    
								    ?>
								    <input type="submit" value="<?php echo $sLR["log_go_btn"]; ?>" />
							    </td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
						    </tr>
					    </table>
				    </td>
			    </tr>
		    </table>
		    <?php if(!$bFirstTime){echo "<input type=\"hidden\" name=\"IDUser\" value=\"$iIDUser\" />";} ?>
	    </div>
	</form>
    <?php
}
?>