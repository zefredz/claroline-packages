<?php
global $sLR;
$sDBFileName = "db.inc.php";

if(file_exists($sDBFileName)){
    include_once($sDBFileName);
}

$sDefaultUsername = "admin";
$sDefaultPassword = "1234";

$sDefaultClipString = "[...]";

$sDefaultDateFormat = "Y\-m\-d";
$sDefaultDateHourFormat = "Y\-m\-d \/ H \h i";
$sExportDateFormat = "Ymd";

$sRowColorA = "#F2F2F2";
$sRowColorB = "#FFFFFF";

$sXMLFileFolder = "./XML/";

$sTypeLabel = array();
$sTypeLabel[0]["choix"] = $sLR["qt_lbl"][0]["choix"];
$sTypeLabel[0]["reponses"] = $sLR["qt_lbl"][0]["reponses"];
$sTypeLabel[0]["vraifaux"] = $sLR["qt_lbl"][0]["vraifaux"];
$sTypeLabel[1] = $sLR["qt_lbl"][1];
$sTypeLabel[2] = $sLR["qt_lbl"][2];
$sTypeLabel[3] = $sLR["qt_lbl"][3];
$sTypeLabel[4] = $sLR["qt_lbl"][4];
$sTypeLabel[5] = $sLR["qt_lbl"][5];
$sTypeLabel[6] = $sLR["qt_lbl"][6];
$sTypeLabel[7] = $sLR["qt_lbl"][7];
$sTypeLabel[8] = $sLR["qt_lbl"][8];

$LETTRES = 0;
$CHIFFRES = 1;
$NONE = 2;

$sBRepBullet = "vert.jpg";
$sMRepBullet = "rouge.jpg";
$sRepIncBullet = "jaune.jpg";

$sBRepMsg = $sLR["v_ga_msg"];
$sMRepMsg = $sLR["v_wa_msg"];
$sRepIncMsg = $sLR["v_ia_msg"];
$sNoRepMsg = $sLR["v_na_msg"];

$sMsgMotsMOrtho = $sLR["v_sm_msg"];
$sMsgMotsManq = $sLR["v_wm_msg"];
$sMsgMotsTrop = $sLR["v_sw_msg"];

$sBRepMsgCol = "#315315";
$sMRepMsgCol = "#921010";
$sRepIncMsgCol = "#9D7707";
$sNoRepMsgCol = "#921010";

$sCanceledQPrefix = "<em>(" . $sLR["qq_staia_lbl"] . ")</em> ";
$sCanceledSPrefix = "<em>(" . $sLR["qp_staia_lbl"] . ")</em> ";

$sDecDelimiter = ",";

$iQNameMaxNbChar = 40;

?>