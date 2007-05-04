<?php

    function executeQuery($sQuery){
        $oRS = mysql_query($sQuery);
        
        if(!$oRS){
            die(mysql_error());
        }
        
        return $oRS;
    }
    
    function sqlString($s,$sStringDelimiter){
        $sToReplace = array($sStringDelimiter);
        $sBy = array($sStringDelimiter . $sStringDelimiter);
        
        return str_replace($sToReplace,$sBy,$s);
    }
    
    function fromPost($s){
        if(!get_magic_quotes_gpc()){
            return addslashes($s);
        }else{
            return $s;
        }
    }
    
    function urlRedirect($sPath,$bRelPath = true){
        if($bRelPath){
            header('location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/" . $sPath);
        }else{
            header('location: ' . $sPath);
        }
        exit();
    }
    function average($iA){
        $iSum = 0;
        
        for($i = 0;$i < count($iA);$i++){
            $iSum += $iA[$i];
        }
        
        return $iSum / count($iA);
    }
    function mediane($iA){
        
        $iMiddle = floor(count($iA) / 2);
        if(count($iA)%2 == 0){
            return (($iA[$iMiddle] + $iA[$iMiddle - 1]) / 2);
        }else{
            return $iA[$iMiddle];
        }
        
    }
    function nbGT($iA,$iGT){
        $iNB = 0;
        for($i = 0;$i < count($iA);$i++){
            if($iA[$i] >= $iGT){
                $iNB++;
            }
        }
        
        return $iNB;
    }
    function clipString($s,$iNbChar,$sSuffix){
        if(strlen($s) > $iNbChar){
            return substr($s,0,$iNbChar) . $sSuffix;
        }else{
            return $s;
        }
    }
    function isInArray($s,$a){
        for($i = 0;$i < count($a);$i++){
            if($a[$i] == $s){
                return true;
            }
        }
        return false;
    }
    function cleanForValid($s){
        $sReturn = trim($s);
        
        $sToReplace = array("\n","<br>","  ","\r");
        $sBy = array("",""," ","");
        
        return str_replace($sToReplace,$sBy,$sReturn);
    }
    function cleanForValidDictee($s){
        $sReturn = trim($s);
        
        $sToReplace = array("\n","<br>","  ","\r");
        $sBy = array(" "," "," "," ");
        
        return str_replace($sToReplace,$sBy,$sReturn);
    }
    function completeNumber($i,$iNbZ){
	$iLen = strlen(strval($i));
	$sToReturn = "";
	
	for($j = 0;$j < ($iNbZ - $iLen);$j++){
		$sToReturn .= "0";
	}
	
	$sToReturn .= $i;
	
	return $sToReturn;
    }
    function XMLStrtoStr($s){
        //return mb_convert_encoding($s,"ISO-8859-1","UTF8");
        return utf8_decode($s);
        //return mb_detect_encoding($s);
        //return $s;
    }
    
    function toLangFloat($f,$iNbDec = 2){
        global $sDecDelimiter;
        
        $sReturn = strval(round($f,$iNbDec));

        return str_replace(".",$sDecDelimiter,$sReturn);
    }
    function fromLangFloat($f,$iNbDec = 2){
        global $sDecDelimiter;

        return str_replace($sDecDelimiter,".",$f);
    }
    function cJS($s){
        $sToReplace = array("'");
        $sBy = array("\'");
        
        $sReturn = str_replace($sToReplace,$sBy,$s);
        
        return "'" . $sReturn . "'";
    }
    
    //Layout functions
    function getFormatedScore($dScore,$dPond,$bDisplayPC = true,$iPrecision = 0){
        $sSp = "&nbsp;";
        $sScoreDelimiter = "$sSp/$sSp";
        $sToReturn = "";
        
        if($dScore > -1){
            $sScore = toLangFloat($dScore);
        }else{
            $sScore = "-";
        }
        
        $sToReturn = $sScore . $sScoreDelimiter . $dPond;
        
        if($bDisplayPC){
            $sToReturn .= $sSp . getFormatedPC($dScore,$dPond);
        }
        
        return $sToReturn;
    }
    
    function getFormatedPC($dScore,$dPond,$iPrecision = 0){
        $sSp = "&nbsp;";
        $sPC = "-";
        
        if($dPond > 0){
            $sPC = round(($dScore/$dPond)*100,$iPrecision);
        }
        
        return "($sPC$sSp%)";
    }
    
    function generateHeader($sQuizName,$sQuizVersion,$sGetParam,$iSelectedTab){
        global $sLR;
        
        $sTabs = array($sLR["q_stats_link"],$sLR["q_quest_link"],$sLR["q_part_link"]);
        $sTabsLinks = array("viewquizstats.php","viewquizquestions.php","viewquizparticipations.php");
        
        ?>
            <tr>
                <td width="100%" align="left">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                            <?php
                            
                                for($i = 0;$i < count($sTabs);$i++){
                                    if($i == $iSelectedTab){
                                        echo "<td class=\"SelectedTab\" height=\"25\">&nbsp;&nbsp;$sTabs[$i]&nbsp;&nbsp;</td>";
                                    }else{
                                        echo "<td class=\"Tab\" height=\"25\">&nbsp;&nbsp;<a href=\"$sTabsLinks[$i]$sGetParam\">$sTabs[$i]</a>&nbsp;&nbsp;</td>";
                                    }
                                }
                            ?>
                            <td class="SpacerTab" width="100%">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        <?php
    }
    
    
    //Encoding & Escaping Functions
    //From
    function fromGPC($s){
        if(get_magic_quotes_gpc()){
            return stripslashes($s);
        }else{
            return $s;
        }
    }
    
    //To
    function toSQLString($s, $bNullAllowed = true, $sStrDelimiter = "'"){
        if(strlen($s) == 0){
            if($bNullAllowed){
                return "NULL";
            }else{
                return $sStrDelimiter . $sStrDelimiter;
            }
        }else{
            return $sStrDelimiter . addslashes($s) . $sStrDelimiter;
        }
    }
    
    function toJS($s){
        return addslashes($s);
    }
?>