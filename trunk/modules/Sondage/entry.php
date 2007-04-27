<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
include_once claro_get_conf_repository().'Sondage.conf.php';
require_once get_path('incRepositorySys')  . '/lib/user.lib.php';
echo(get_conf('Message'));

echo '<form name="sondage" method="get" action="index.php">'
	.	'<input type="hidden" name="page" value="1" />'
	.	'<textarea rows="3" type="text" name="valeur" value=""></textarea>'
	.	'<input type="submit" name="btvote" value="'. get_lang('submit') .'" >'
	.	'</form>';

if (isset($_REQUEST['page'])) $page=$_REQUEST['page'];
else	$page=0;
if($page==1){
	if(claro_is_user_authenticated()){
	$_user = user_get_properties(claro_get_current_user_id()); //get user data
	$_user['firstName'] = $_user['firstname'];	//find user firstName
	$_user['lastName' ] = $_user['lastname'];	//find user lastName
	$thisUser=$_user['lastName' ].' '.$_user['firstName'];;
	if (get_conf('User_select')=='TRUE' && $thisUser ==' '){	//if anonimus user
		$thisUser='Anonyme';
	}
		if($_GET['valeur']<> ''){
				$query="INSERT INTO cl_module_sondage (id,valeur) VALUES('" . addslashes($thisUser) . "','" . addslashes($_GET['valeur']) . "')"; //ajout des donnees dans la table
				$result=mysql_query($query) or die( ' Erreur' );
				if (!$result) 
				{
					$message  = 'Requête invalide :' . mysql_error() . "\n";
					$message .= 'Requête complète :' . $query;
					die($message);
				}
		}
		else 
		{
			echo(get_conf('Error_input'));
		}	
	}
	else 
	{
		echo(get_conf('Error_user'));
	}
}
?>