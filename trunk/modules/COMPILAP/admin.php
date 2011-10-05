<?php
/*
Applet COMPILATIO v1.6 pour Claroline 
Compilatio - www.compilatio.net

Script permettrant de voir le nb de crédits restants du compte, et de tester le connexion SOAP
*/
//Declaration of all session variables, used librairies,... of Claroline kernel
include("../../claroline/inc/claro_init_global.inc.php");
ini_set('soap.wsdl_cache_enabled', 0); 
ini_set('default_socket_timeout', '100');
 

//Display the Claroline header (top banner)
include($includePath."/claro_init_header.inc.php");
 


require_once '../COMPILAT/lib/compilatio.class.php';
require_once '../COMPILAT/lib/compilatio.lib.php';
if (get_conf('using_SSL'))
{
  $urlsoap='https://service.compilatio.net/webservices/CompilatioUserClient.wsdl';
}
else
{
  $urlsoap='http://service.compilatio.net/webservices/CompilatioUserClient2.wsdl'; 
}


$compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('proxy_host'),get_conf('proxy_port'));
$quotas=$compilatio->GetQuotas();
$use_space=number_format($quotas->usedSpace/1000000,2);
$total_space=$quotas->space/1000000;

echo "<h3>Module Anti-plagiat Compilatio</h3>";

echo "<b>" . get_lang('Compilatio quotas').":</b><!--br>".get_lang('Used space') . ": " .$use_space." Mo ".get_lang('of')." ".$total_space." Mo--><br>".get_lang('Analysis credits') . ": " .$quotas->usedCredits." ".get_lang('of')." ".$quotas->credits;

?>
<br><br>
<?
if(!isset($_GET['action']))
{?>
<body style="margin:0px;padding:0px">
<form style="margin:0px;" method="GET">
<input type="submit" name="action" value="Test de Connexion SOAP">
</form>
<?}
else
{?>

Test de connexion SOAP...
<br>

1) Connection au serveur SOAP Compilatio<br>
<?$compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('soap_proxy_host'),get_conf('soap_proxy_port'));
if ($compilatio)
{ echo "Connection effectuée<br>";
?>
2) Envoi d'un texte vers le serveur Compilatio<br><?
$texte="Ceci est un test d'envoi de texte vers le serveur Compilatio via son API\nClé Compilatio utilisée: " . get_conf('clef_compilatio');
$id_compi=$compilatio->SendDoc('Doc de test','test','test','text/plain',$texte);
if(is_md5($id_compi))
{
  echo "Transfert réussi";
}
else
{
  echo "Le transfert a échoué. Vérifiez votre clé, l'ouverture des ports de votre serveur et éventuellement vos paramètres de proxy.";
}
}
else
{
echo "Impossible d'effectuer la connection au serveur SOAP Compilatio<br>";
echo "Vérifiez votre clé, l'ouverture des ports de votre serveur et éventuellement vos paramètres de proxy.";
}
}
?>

<?


//Display the Claroline footer
include($includePath."/claro_init_footer.inc.php");
?>
