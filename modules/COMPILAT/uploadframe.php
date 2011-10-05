<?php
/*
Module COMPILATIO v1.6 pour Claroline 
Compilatio - www.compilatio.net
*/
/*
Script de mise en ligne des documents defacon indépendante via un pop-up séparé 
permettant ainsi à l'utilisateur de pouvoir continuer à naviguer pdt l'upload des documents.
*/
?>
<html>
<FRAMESET ROWS="40,*" Frameborder="NO">
<FRAME SRC="chargement.php" NAME="haut">
<?php
$req="doc=" . urlencode($_REQUEST['doc']);
if(isset($_REQUEST['type'])) $req.="&type=" . $_REQUEST['type'];
$req.="&assigId=" . $_REQUEST['assigId'];
$req.="&tab=" . $_REQUEST['tab'];
?>
<FRAME SRC="upload.php?<?php echo $req;?>" NAME="bas">
</FRAMESET>
</html>