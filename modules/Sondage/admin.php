<?php 
require_once dirname(__FILE__) . "/../../claroline/inc/claro_init_global.inc.php";

$sql = 'SELECT * FROM ' . get_conf('mainTblPrefix') . 'module_sondage';		//demande des donnees a la table
$result = claro_sql_query_fetch_all($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
echo '<table>';
	echo '<tr><td>n°</td><td>utilisateur</td><td>commentaires</td></tr>';
	foreach ($result as $this_result) 
	{    //affichage des donnees
		echo'<tr><td>';
			echo $this_result['num'];
		echo '</td>';
		echo '<td>';
			echo $this_result['id'];
		echo '</td>';
		echo '<td>';
			echo $this_result['valeur'];
		echo '</td></tr>';
	} 
	echo ('</table>');
	?>