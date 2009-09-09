<?php 
  /**
     * Fichier de départ pour le module RESULTAT
     *
     * @version    0.1 18 mai 2007
     * @copyright   2001-2007 École nationale d'administration publique (ENAP)
     * @author     David Boudreault
     * @package    RESULT
     */
/**
 *  CLAROLINE MAIN SETTINGS
 */
$tlabelReq = 'RESULTS';
$noPHP_SELF = TRUE;

// load Claroline kernel
require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php'; 
claro_set_display_mode_available(true);
include  get_path('includePath') . '/claro_init_header.inc.php';

//fichier de langue
add_module_lang_array($tlabelReq);
$nameTools  = get_lang('Results');

$currentCourseID  = $_course['sysCode'];
$courseDir = claro_get_course_path() ;

if ( ! get_init('in_course_context') || ! get_init('is_courseAllowed') || !get_init('is_authenticated') ) claro_disp_auth_form(true);

// get Claroline course table names
$toolTables = get_module_course_tbl( array( 'results_evaluations', 'results_entries' ), claro_get_current_course_id() );

// run course installer for on the fly table creation
install_module_in_course( 'RESULTS', claro_get_current_course_id() ) ;

 // tool global variables
$tbl_mdb_names       = claro_sql_get_main_tbl();
$tbl_user            = $tbl_mdb_names['user'];
$tbl_course = $tbl_mdb_names['course'];
$tbl_course_user      = $tbl_mdb_names['rel_course_user'];
$baseServDir = $coursesRepositorySys;
$baseWorkDir = $baseServDir.$courseDir;

//Créer le répertoire dans le cours si inexistant?
if (!is_dir($baseWorkDir.'/results'))
	mkdir ($baseWorkDir.'/results');

$is_allowedToEdit  = claro_is_allowed_to_edit();
$dspCurDirName = htmlspecialchars($curDirName);
$cmdCurDirPath = rawurlencode($curDirPath);
$cmdParentDir  = rawurlencode($parentDir);
?>
<script type="text/javascript">
function validerNote(note, maximum)
{
   if (note != ''){
	   note= parseFloat(note);
	   maximum = parseFloat(maximum);
	   if (  isNaN(note)){
			alert('La note doit être un nombre. Veuillez la modifier.');
	   }
	   else if (note > maximum ) {
			alert('La note dépasse le maximum autorisé pour cette évaluation. Veuillez la modifier.');
	   }
   }
}

function validerFormEntrerEvaluations() //Ne pas permettre de modifier les notes s'il reste un champ non-numérique
{
  var ok;
  var NbBox;
  var NbRadio;
  var l =0;
  var NbElements = frmGrille.elements.length;
 
  // Boucle tous les éléments du formulaire
  for ( l = 0; l < NbElements; l++)
  {
    var Nom_Element = frmGrille.elements[l].name;
    var Champ = frmGrille.elements[l];    
	//Si ce n'est pas la case du titre Chaine.substr(position1, longueur)
	if (Champ.type == 'text' && Nom_Element.substr(Nom_Element.length-5,5) != 'titre'){
		if ( isNaN(Champ.value))
	    { 
		  alert("Ce champ doit être un nombre. Veuillez le modifier :" ); 
		  Champ.focus(); 
		  return false; 
	    }
	}
  }
  return true;
}

function validerFormEntrerResultats() //Ne pas permettre de modifier les notes s'il reste un champ non-numérique
{
  var ok;
  var NbBox;
  var NbRadio;
  var l =0;
  var NbElements = frmEntrerResultats.elements.length;
 
  // Boucle tous les éléments du formulaire
  for ( l = 0; l < NbElements; l++)
  {
    var Nom_Element = frmEntrerResultats.elements[l].name;
    var Champ = frmEntrerResultats.elements[l];
    if (Champ.type == 'text'){
	    if ( isNaN(Champ.value))
	    { 
		  alert("Ce champ doit être un nombre. Veuillez le modifier :" ); 
		  Champ.focus(); 
		  return false; 
	    }
	}
  }
  return true;
}

</script>
<?php

//Afficher le titre de l'outil
echo claro_html_tool_title($nameTools);

$ModifierGrilleEvaluationEncore = false;
$sauvegarderInformationsGrille = false;
$afficherMenu = true;

//Ajouter une évaluation
if (isset ($_POST["idAjoutEval"]) && $_POST["idAjoutEval"] != "" ){
		$sql = "INSERT INTO `$toolTables[results_evaluations]` SET titre = '-', maximum = '0', ponderation='0'";
		mysql_query($sql);	
		$modifierGrilleEvaluationEncore = true;
		$afficherMenu = false;
		$sauvegarderInformationsGrille = true;
		unset($_POST["idAjoutEval"]);
}

//Supprimer une évaluation
if (isset ($_POST["idSuppEval"]) && $_POST["idSuppEval"] != "" ){
	$id_eval_a_supprimer = $_POST["idSuppEval"];
	mysql_query("DELETE FROM `$toolTables[results_evaluations]` WHERE evaluation_id =' $id_eval_a_supprimer'");
	mysql_query("DELETE FROM `$toolTables[results_entries]` WHERE evaluation_id =' $id_eval_a_supprimer'");
	$modifierGrilleEvaluationEncore = true;
	$sauvegarderInformationsGrille = true;
	$afficherMenu = false;
	unset($_POST["idSuppEval"]);
}

if (isset($_POST["action"]) && ($_POST["action"]== get_lang('Save'))){
	 $sauvegarderInformationsGrille = true;
	 $afficherMenu = true;
}
//save the grid.

if ($sauvegarderInformationsGrille){
  $sql = "SELECT * FROM `$toolTables[results_evaluations]` ORDER BY evaluation_id";
  $result = mysql_query($sql);
  while ($thisEvaluation = mysql_fetch_array($result)){ 
	 $idEvaluation =  $thisEvaluation['evaluation_id'] ;
	 $champTitre = $idEvaluation ."_titre";
	 $champTitre = $_POST[$champTitre];
	 $champMaximum = $idEvaluation ."_maximum";
	 $champMaximum = $_POST[$champMaximum];
	 $champPonderation = $idEvaluation ."_ponderation";
	 $champPonderation = $_POST[$champPonderation];
	 $sql2 = "UPDATE `$toolTables[results_evaluations]` SET `titre` = '". addslashes($champTitre) ."', `maximum` = '". addslashes($champMaximum) ."', `ponderation`='". addslashes($champPonderation) ."' WHERE `evaluation_id` = '". $idEvaluation. "'" ;
	 mysql_query($sql2);
  }
 }

if ((isset($_POST["action"]) && ($_POST["action"]== get_lang('Enter evaluations'))) || $modifierGrilleEvaluationEncore){
	 
	 echo "<form name='frmGrille' method='post' action='".$PHP_SELF."' onSubmit='return validerFormEntrerEvaluations();'>";
	 echo "<table width='75%' bgcolor=\"#E6E6E6\" border=\"1\"><tr><th width=\"80%\">".get_lang('Evaluation')."</th><th>".get_lang('Max result')."</th><th>".get_lang('Weighting')."</th><th>";
	 echo "</th></tr>";
	 $result = mysql_query("SELECT * FROM `$toolTables[results_evaluations]` ORDER BY evaluation_id");
	 while ($thisEvaluation = mysql_fetch_array($result)){ 
		 $idEvaluation =  $thisEvaluation['evaluation_id'] ;
		 $champTitre = $idEvaluation ."_titre";
		 $champMaximum = $idEvaluation ."_maximum";
		 $champPonderation = $idEvaluation ."_ponderation";
		 $titre = $thisEvaluation['titre'] ;
		 $maximum = $thisEvaluation['maximum'];
		 $ponderation = $thisEvaluation['ponderation'];
		 echo "<tr>";
		 echo "<td><input type=\"text\" maxlength='255' size=\"60\" name=\"$champTitre\" value ='$titre'></td>";
		 echo "<td><input type=\"text\" maxlength='10' size=\"10\" name=\"$champMaximum\" value ='$maximum' onchange ='validerChampNumerique(this.value)'></td>";
		 echo "<td><input type=\"text\" maxlength='10'size=\"10\" name=\"$champPonderation\" value ='$ponderation' onchange ='validerChampNumerique(this.value)'></td>";
		 echo '<td><a href="#" onclick="document.forms.frmGrille.idSuppEval.value =' .$idEvaluation.'; var reponse = window.confirm(\''.get_lang('This evaluation and the results for this evaluation will be deleted. Continue?').'\'); if (reponse){document.forms.frmGrille.submit();}else{document.forms.frmGrille.idSuppEval.value =\'\';}"><img src="' . $imgRepositoryWeb . 'delete.gif" alt="Supprimer cette évaluation" border="0"></a></td>';
		 echo "</tr>";  
	 }
	 echo "</table>";
	 echo "<input type=\"hidden\" name=\"idSuppEval\" value =''>";
	 echo '<br><div><input type="submit" name="idAjoutEval" value="'.get_lang('Ajouter une évaluation') .'"></div>';
	 echo "<br><br>";
	 echo "<p align='center'><input type=\"submit\" name=\"action\" value=\"".get_lang('Save')."\"> &nbsp;&nbsp;";
	 echo claro_html_button($_SERVER['PHP_SELF'],get_lang('Back to the menu'));
	 echo "</p>";
	 $afficherMenu = false;
} //FIN MODIFICATION GRILLE D'ÉVALUATION

if ((isset($_POST["action"]) && ($_POST["action"]== get_lang('Save results')) ))
	{
	//	event_access_tool($_tid, $_courseTool['label']);
		//Liste des évaluations
		$nbEtudiants = $_POST["nbEtudiants"];
		$nbEvaluations = $_POST["nbEvaluations"];
		$result = mysql_query("SELECT * FROM `$toolTables[results_evaluations]` ORDER BY evaluation_id");
	    while ($thisEvaluation = mysql_fetch_array($result))  //modifier les notes des étudiants pour chaque évaluation dans la grille d'évaluation
		{
			$evaluation_id=$thisEvaluation['evaluation_id'];
			$sql2 = "SELECT DISTINCT `$tbl_user`.`user_id`, `$tbl_user`.`nom`, `$tbl_user`.`prenom`, `$tbl_user`.`username`,
								  `$tbl_course_user`.`isCourseManager`
							FROM `$tbl_user`, `$tbl_course_user`
							WHERE `$tbl_user`.`user_id`=`$tbl_course_user`.`user_id`
							AND `$tbl_course_user`.`code_cours`='".$currentCourseID."'
							AND `$tbl_course_user`.`isCourseManager`='0'
							ORDER BY
								UPPER(`$tbl_user`.`username`)";
			$result2= mysql_query($sql2);
			while ($thisUser = mysql_fetch_array($result2)){
				$userId=$thisUser['user_id'];
				//Lire le formulaire posté
				$nomChamp = $userId  . '_' . $evaluation_id;
				if (isset($_POST["$nomChamp"])){ //Mettre  à jour ou  ajouter selon  le résultat de l'étudiant pour cet évaluation
					$note_etudiant = trim($_POST[$nomChamp]);
					$sql = "INSERT INTO `$toolTables[results_entries]` (user_id,evaluation_id,note) VALUES ('".$userId."',".$evaluation_id.",'".addslashes(trim($note_etudiant))."') ON DUPLICATE KEY UPDATE note='".addslashes(trim($note_etudiant))."'";
					mysql_query($sql);
					//Permet d'avertir l'étudiant qu'un résultat est rentré.
					//$eventNotifier->notifyCourseEvent('resultats_event_added', $_cid, $_tid, $userId, $_gid, $_uid);
					//$ex_rss_refresh = TRUE;
				}
			}
		}
	  //Grille d'évaluation  ajoutée
	  unset($_POST["action"]);
	  $afficherMenu = true;
	}
if (isset($_POST["action"])){
	//ENTRER LES NOTES DES ÉTUDIANTS : ASSERTATION:  LE COURS CONTIENT DES ÉTUDIANTS ET CONTIENT DES ÉVALUATIONS
	if (($_POST["action"]==get_lang('Enter results'))){
		//Liste des évaluations du cours	
		$result2 = mysql_query("SELECT * FROM `$toolTables[results_evaluations]` ORDER BY evaluation_id");
		echo 	"<form name ='frmEntrerResultats' action=\"$PHP_SELF\" method=\"post\" onSubmit='return validerFormEntrerResultats();'><table width=\"100%\" cellpadding=\"2\" cellspacing=\"1\" border=\"1\">\n";
		//COLONES  = ÉVALUATION  {TITRE, NOTE_MAX, PONDERATION}
		$nbEvaluations = 0;
		echo "<tr align=\"center\" valign=\"top\" bgcolor=\"#E6E6E6\">";
		echo "<td><b>NIP</b></td><td><b>Nom</b></td><td><b>Prénom</b></td>";
		while ($thisEvaluation = mysql_fetch_array($result2))
		{
			$nbEvaluations++;
			$listeEvaluations[] = $thisEvaluation['evaluation_id'];
			echo "<td align=\"center\"><b>",$thisEvaluation['titre'],"</b>&nbsp;(".$thisEvaluation['maximum'].")&nbsp;".$thisEvaluation['ponderation'] . "%</td>";
			echo "<input type=\"hidden\" name='evaluation" . $nbEvaluations . "' value='". $thisEvaluation['maximum']. "'>";
		}
		echo "</tr>";
		//Sélectionner les étudiants inscrits au cours présentement.
		$sql = "SELECT DISTINCT `$tbl_user`.`user_id`, `$tbl_user`.`nom`, `$tbl_user`.`prenom`, `$tbl_user`.`username`,
								  `$tbl_course_user`.`isCourseManager`
							FROM `$tbl_user`, `$tbl_course_user`
							WHERE `$tbl_user`.`user_id`=`$tbl_course_user`.`user_id`
							AND `$tbl_course_user`.`code_cours`='".$currentCourseID."'
							AND `$tbl_course_user`.`isCourseManager`='0'
							ORDER BY
								UPPER(`$tbl_user`.`username`)"; 
		$result = mysql_query($sql); 
		$nbEtudiants = 0;
		//LIGNE = ÉTUDIANT {NOM, PRÉNOM, NIP}
		while ($thisUser = mysql_fetch_array($result))
		{
			$nbEtudiants++;
			$userName=$thisUser['username'];
			$userId = $thisUser['user_id'];
			$nom = $thisUser['nom'];
			$prenom = $thisUser['prenom'];
			echo "<tr align=\"center\" valign=\"top\" bgcolor=\"#E6E6E6\">\n",
				 "<td align=\"left\" width=\"10%\"><b>",$userName,"</b></td>\n",
				 "<td align=\"left\">",$nom,"</td>\n",
				 "<td align=\"left\">",$prenom,"</td>";
			
			//Pour chaque évaluation,  afficher la note de l'étudiant si disponible.
			for ($y=0;$y<$nbEvaluations; $y++){
				$resultat_description_id = $listeEvaluations[$y];
				$sql3 = "SELECT $toolTables[results_entries].note FROM  `$toolTables[results_entries]` , `$tbl_user` WHERE ($toolTables[results_entries].user_id = $userId AND $toolTables[results_entries].user_id = `$tbl_user`.user_id AND $toolTables[results_entries].evaluation_id = $resultat_description_id)";
				$result3 = mysql_query($sql3);
				$note = ""; //cas où note inexistante pour ce travail
				if (mysql_num_rows($result3)>0){ //Si le résultat de l'étudiant est disponible
					$thisResultat = mysql_fetch_array($result3);
					$note = $thisResultat['note'];
				}
				//Afficher la note si présente
				echo '<td align=\"left\" width=\"10%\">';
				$nomChampHiddenEvaluation = "document.frmEntrerResultats.evaluation". (((int)$y) + 1) .".value";
				echo '<input type="text" maxlength="6" name="'.$userId  . '_' .$resultat_description_id .'" value = "'.$note.'" size="6" ';
				if (claro_is_javascript_enabled()) 
					echo 'onchange = "validerNote(this.value, ' . $nomChampHiddenEvaluation . ')"';
				echo '></td>';
			}
			echo "</tr>";
		}
	  echo 	"</table>\n";
	  echo "<br><Strong> * Une case vide ne comptera pas dans le calcul de la moyenne. Veuillez inscrire 0 si c'est le cas.</Strong>";
	  echo "<input type=\"hidden\" name=\"nbEvaluations\" value=\"$nbEvaluations\">";
	  echo "<input type=\"hidden\" name=\"nbEtudiants\" value=\"$nbEtudiants\">";
	  echo "<p align='right'> <input type=\"submit\" name=\"action\" value=\"".get_lang('Save results')."\">&nbsp;&nbsp;&nbsp;&nbsp;";
	 echo claro_html_button($_SERVER['PHP_SELF'],get_lang('Back to the menu')). "</p></form>";
	  $afficherMenu = false;
	}
} // fin if (isset($action))

if (!$is_allowedToEdit) //Afficher les résultats à l'étudiant
{
	event_access_tool($_tid, $_courseTool['label']);
	$result = mysql_query("SELECT * FROM `$tbl_course` WHERE `code`='$_cid'");
	$row = mysql_fetch_array($result);
	$id=$row[0];

	$result = mysql_query("SELECT * FROM `$toolTables[results_evaluations]` ORDER BY evaluation_id");
	$i=0;
	while ($row = mysql_fetch_array($result))
	{
	   $table_resultat_descriptions[$i][0]=$row['titre'];
	   $table_resultat_descriptions[$i][1]=$row['maximum'];
	   $table_resultat_descriptions[$i++][2]=$row['ponderation'];
	}
	echo 	"<table cellpadding=\"2\" cellspacing=\"1\" border=\"1\">";

	echo	"<tr align=\"center\" valign=\"top\" bgcolor=\"#E6E6E6\">",
			"<td align=\"left\">".get_lang('Title')."\n",
			"<td align=\"left\">".get_lang('Result')."</td>\n";
	$i=0;
	$result = mysql_query("SELECT DISTINCT `$tbl_user`.`user_id`, `$tbl_user`.`nom`, `$tbl_user`.`prenom`, `$tbl_user`.`username`

	                        FROM `$tbl_user`, `$tbl_course_user`
	                        WHERE `$tbl_user`.`user_id`='".$_uid."'");

	$thisUser = mysql_fetch_array($result);
    $sql ="SELECT * FROM  `$toolTables[results_entries]` WHERE `user_id`='".$thisUser['user_id']."' ORDER BY evaluation_id";
	$result2 = mysql_query($sql);
	while ($row2 = mysql_fetch_array($result2))
	{
	  echo "<tr><td align=\"center\">",$table_resultat_descriptions[$i][0],"</td>";
	  echo "<td align=\"center\">&nbsp;";
	  echo $row2['note']; //Afficher la note à l'étudiant
	  echo"</tr>";
	  $maximum[$i]=$row[3];
	  if (!isset($_POST["action"]))
	  {
	     $entete_colonne[$i][0]=$row[2];
	     $entete_colonne[$i][1]=$row[3];
	     $entete_colonne[$i][2]=$row[4];
	  }
	  $pond[$i++]=$row[4];
	}
	echo  "</tr>\n";
	echo "</table>\n";
	echo "<br>";
}
else // Afficher les résultats des étudiants ainsi que les moyennes au professeur
{
	if ($_POST["action"]== get_lang('Display results'))
	{
	   $sortie = ""; //Texte à afficher à l'écran dans cette section
	   $ok_to_display=false;
	   $afficherMenu = false;
	   $action = $_POST["action"];
	   $result = mysql_query("SELECT * FROM `$tbl_course` WHERE `code`='$_cid'");
	   $row = mysql_fetch_array($result);
	   $id=$row[0];
	   $result = mysql_query("SELECT * FROM  `$toolTables[results_evaluations]` ORDER BY evaluation_id");
         
	   $sortie.= "<table width='100%' cellpadding='2' cellspacing='1' border='1'>\n";
	   $sortie.= "<tr align='center' valign='top' bgcolor='#E6E6E6'>\n";
	   $sortie.= "<td align='center' ><b>".get_lang('Username')."</b></td>\n";
	   $sortie.= "<td align='center'><b>".get_lang('Name')."</b></td>\n";
	   $sortie.= "<td align='center'><b>".get_lang('First name')."</b></td>\n";
	   $nbEvaluations=0;
			while ($row = mysql_fetch_array($result))
			{
				  $sortie.="<td align=\"center\"> <b>".$row['titre'] . "</b>";
				  if (($row['maximum'])&&($row['maximum']!="1"))
				     $sortie.= "<br> sur ".$row['maximum'];
				  if (($row['ponderation'])&&($row['ponderation']!="1"))
				     $sortie.= "<br>".$row['ponderation']."% ";
				  $sortie.= "</td>\n";
				  $maximum[$i]=$row['maximum'];
				  if (!isset($_POST["action"]))
				  {
				     $entete_colonne[$i][0]=$row['titre'];
				     $entete_colonne[$i][1]=$row['maximum'];
				     $entete_colonne[$i][2]=$row['ponderation'];
				  }
				  $pond[$i++]=$row['ponderation'];
				  $evaluations[$nbEvaluations] = $row['evaluation_id']; //GARDER L'ORDRE DES ÉVALUATIONS (DES COLONNES) EN MÉMOIRE
				  $nbEvaluations++;
			}
			$sortie.= "<td align=\"center\"><b>Total</b></td>\n";
			$sortie.=	"</tr>\n";
			$sql = "SELECT DISTINCT `$tbl_user`.`user_id`, `$tbl_user`.`nom`, `$tbl_user`.`prenom`, `$tbl_user`.`username`,
								 `$tbl_course_user`.`isCourseManager`
							FROM `$tbl_user`, `$tbl_course_user`
							WHERE `$tbl_user`.`user_id`=`$tbl_course_user`.`user_id`
							AND `$tbl_course_user`.`code_cours`='".$currentCourseID."'
							AND `$tbl_course_user`.`isCourseManager`='0'
							ORDER BY
								UPPER(`$tbl_user`.`username`)";

			$result = mysql_query($sql); //resultat
			while ($thisUser = mysql_fetch_array($result))
			{
				$sortie.= "<tr valign=\"top\" align=\"center\">\n".

					"<td align=\"center\">".

					$thisUser['username'].
					
					"</td>\n".

					"<td align=\"center\">".

					$thisUser['nom'].
					
					"</td>\n".
					"<td align=\"center\">".

					$thisUser['prenom'].
					
					"</td>\n";

				$sql = "SELECT result.note, result.evaluation_id, eval.maximum, eval.ponderation FROM  `$toolTables[results_entries]` result ,  `$toolTables[results_evaluations]` eval WHERE result.user_id='".$thisUser['user_id']."' AND result.evaluation_id = eval.evaluation_id ORDER BY result.user_id, result.evaluation_id"; 
				$result2 = mysql_query($sql);
				//Calculer la moyenne de l'étudiant à partir de la note de chaque travail.
				$moyenne_etudiant = 0;
				while ($row2 = mysql_fetch_array($result2)) //Afficher les notes pour cet utilisateur
				{
					//Parcourir le tableaux des évaluations, mettre le résultat dans la bonne colonne
					for ($noEvaluation = 0; $noEvaluation < $nbEvaluations; $noEvaluation ++){
						if ($evaluations[$noEvaluation] == $row2['evaluation_id'] ){
							$affichageNotesTravaux[$noEvaluation] = "<td align=\"right\">&nbsp;".$row2['note']."</td>\n";
							$moyenne_etudiant += (($row2['note'] / $row2['maximum'] ) * $row2['ponderation'] );
						}
					}
				}
				//Afficher les notes pour cet utilisateur
				for ($x = 0; $x < $nbEvaluations ; $x++){
					if ($affichageNotesTravaux[$x] == "")
						$sortie.= "<td align=\"right\">&nbsp;".
								"</td>\n";
				    else
						$sortie.= $affichageNotesTravaux[$x];
				}
				
				//Afficher la moyenne de l'étudiant pour le cours.
				$sortie.= "<td align = 'right' bgcolor=\"#E6E6E6\"> &nbsp; <b>" . round($moyenne_etudiant,1) ."</b>" ;
				$sortie.=	"</td> </tr>\n";
			}
			//Compter la moyenne de chacun des travaux du cours. 
			//Compter dans la moyenne seulement les étudiants INSCRITS au cours (les étudiants peuvent être désinscrits en cours de session et avoir une note) 
			//et seulement les travaux dont la note a été entrée (différente de '');
			$sql = "SELECT `result`.`evaluation_id`, `eval`.`ponderation`, `eval`.`maximum`, avg(`result`.`note`) AS moyenne
					FROM `$toolTables[results_entries]` result ,  
						 `$toolTables[results_evaluations]` eval, 
						 `$tbl_course_user`
				    WHERE result.evaluation_id = `eval`.`evaluation_id` 
					AND   `result`.`user_id`= `$tbl_course_user`.`user_id`
					AND   `result`.`note` <> ''
					AND `$tbl_course_user`.`code_cours`='".$currentCourseID."'
					AND `$tbl_course_user`.`isCourseManager`='0'
					GROUP BY `result`.`evaluation_id`
					ORDER BY `eval`.`evaluation_id`";
					$resultMoyenne = mysql_query($sql);
			
			//Afficher la moyenne des étudiants pour chaque travail
			$sortie.= "<tr bgcolor=\"#E6E6E6\"> <td align = 'center' ><b> Moyenne</b></td><td>&nbsp;</td><td>&nbsp;</td>";
			//Calculer la moyenne de la note finale.
			$total = 0;
			$ponderationTotal = 0;
			while ($thisMoyenne= mysql_fetch_array($resultMoyenne))
			{
					$ponderationTotal += $thisMoyenne['ponderation'];
					//Parcourir le tableaux des évaluations, mettre la moyenne du travail si elle existe
					for ($noEvaluation = 0; $noEvaluation < $nbEvaluations; $noEvaluation ++){
						if ($evaluations[$noEvaluation] == $thisMoyenne['evaluation_id'] ){
								$affichageMoyennesTravaux[$noEvaluation] = "<td align = 'right' ><b>".round($thisMoyenne['moyenne']  , 1) . " sur ". $thisMoyenne['maximum'] . "</b></td>";
								$total += (($thisMoyenne['moyenne'] / $thisMoyenne['maximum'] ) * $thisMoyenne['ponderation'] );
							}
					}
			}
			//Afficher la moyenne pour chaque travail
			for ($x = 0; $x < $nbEvaluations ; $x++){
				//echo "rentre une fois <br>";
				if ($affichageMoyennesTravaux[$x] == "")
					$sortie.= '<td>&nbsp;</td>';
			    else
					$sortie.= $affichageMoyennesTravaux[$x];
			}
			
			//Afficher la moyenne du cours (TOTAL)
			$sortie.= "<td align = 'right' ><b>". round($total,1) ." sur " . $ponderationTotal . "</b></td>";
			$sortie.= "</tr>";
		    $sortie.= "</table>\n<br>";
			
			echo $sortie; //afficher le tout.
			
			echo "<div align='right'>";
			echo claro_html_button($_SERVER['PHP_SELF'],get_lang('Back to the menu'));
			echo "</div>";			
			// filepath
		    $baseWorkDir = get_path('coursesRepositorySys') . $courseDir;

		    $fileName = $baseWorkDir . '/results/results.xls';
		    
		    $fresultat = fopen ($fileName, "w+");
			
			fwrite($fresultat,"<html><head></head><body>".$sortie."</body></html>");
			fclose($fresultat);

			$downloadLink = '<a href="'.$rootWeb. '/courses/'.$courseDir. '/results/results.xls'.'">' .get_lang('Click here to obtain an Excel file of these results').'</a>';	
			echo $downloadLink;
			
			unset($_POST["action"]); // FIN AFFICHAGE DES RÉSULTATS
	}
}

//MENU
if ($is_allowedToEdit){
   echo "<a name=\"#debut\">";
   echo "<form method=\"post\" action=\"$PHP_SELF\">";
	if ($afficherMenu){
      echo "<center><input type=\"submit\" name=\"action\" value=\"".get_lang('Enter evaluations')."\">";
	 
	  $sql = "SELECT * FROM $toolTables[results_evaluations]";
      //Si le cours contient une grille d'évaluation
	  $result = mysql_query($sql);
	  if (mysql_num_rows($result)>0){
		$sql2 = "SELECT DISTINCT `$tbl_course_user`.`user_id`
							FROM `$tbl_user`, `$tbl_course_user`
							WHERE `$tbl_user`.`user_id`=`$tbl_course_user`.`user_id`
							AND `$tbl_course_user`.`code_cours`='".$currentCourseID."'
							AND `$tbl_course_user`.`isCourseManager`='0'";
	    
		$result2 = mysql_query($sql2); //resultat
		if (mysql_num_rows($result2)>0){ //Si le cours contient des étudiants
		    //Afficher les boutons pour pouvoi entrer les notes des étudiants et afficher les résultats + moyennes.
			echo "<input type=\"submit\" name=\"action\" value=\"".get_lang('Enter results')."\">";
			echo "<input type=\"submit\" name=\"action\" value=\"".get_lang('Display results')."\"><br>";
		}
	  }
	}
	
   echo "</center></form><br>";
}
include $includePath.'/claro_init_footer.inc.php';
?>