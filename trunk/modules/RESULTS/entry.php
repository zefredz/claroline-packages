<?php 
  /**
     * Fichier de départ pour le module RESULTAT
     *
     * @version    1.9 september 2009
     * @copyright  2001-2007 École nationale d'administration publique (ENAP)
     * @author     David Boudreault
     * @credit 	   Philippe Dekimpe
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
require_once  get_path('incRepositorySys') . '/lib/courselist.lib.php';
require_once 'lib/RESULTS.lib.php';

//fichier de langue
$nameTools  = get_lang('Results');

$currentCourseID  = claro_get_current_course_id(); 
$courseDir = claro_get_course_path() ;

$dialogBox = new DialogBox();

ClaroBreadCrumbs::getInstance()->setCurrent( get_lang( 'Results' ) );

if ( ! get_init('in_course_context') || ! get_init('is_courseAllowed') || !get_init('is_authenticated') ) claro_disp_auth_form(true);

// get Claroline course table names
$toolTables = get_module_course_tbl( array( 'results_evaluations', 'results_entries' ), claro_get_current_course_id() );

// run course installer for on the fly table creation
install_module_in_course( 'RESULTS', claro_get_current_course_id() ) ;

 // tool global variables
$tbl_mdb_names      = claro_sql_get_main_tbl();
$tbl_user           = $tbl_mdb_names['user'];
$tbl_course         = $tbl_mdb_names['course'];
$tbl_course_user    = $tbl_mdb_names['rel_course_user'];
$baseWorkDir        = get_path('coursesRepositorySys') . $courseDir;

//Créer le répertoire dans le cours si inexistant?
if (!is_dir($baseWorkDir.'/results'))
	mkdir ($baseWorkDir.'/results');

$is_allowedToEdit  = claro_is_allowed_to_edit();

// Test input

$cmd             = isset($_REQUEST['cmd'])        ? $cmd = $_REQUEST['cmd'] : '';
$action          = isset($_POST['action'])        ? $action = $_POST['action'] : '';
$idAjoutEval     = isset($_POST['idAjoutEval'])   ? $idAjoutEval = $_POST['idAjoutEval'] : '';
$idSuppEval      = isset($_POST['idSuppEval'])    ? $idSuppEval = $_POST['idSuppEval'] : '';
$nbEtudiants     = isset($_POST['nbEtudiants'])   ? $nbEtudiants = $_POST['nbEtudiants'] : '';
$nbEvaluations   = isset($_POST['nbEvaluations']) ? $nbEvaluations = $_POST['nbEvaluations'] : '';

$out = "

<script type='text/javascript'>
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
		  alert('Ce champ doit être un nombre. Veuillez le modifier :' ); 
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
		  alert('Ce champ doit être un nombre. Veuillez le modifier :' ); 
		  Champ.focus(); 
		  return false; 
	    }
	}
  }
  return true;
}

</script>";

// Display tool name
$out .= claro_html_tool_title($nameTools);

$modifierGrilleEvaluationEncore = false;
$sauvegarderInformationsGrille = false;
$afficherMenu = true;

// Save action on evaluation
if (isset ($idAjoutEval) && $idAjoutEval != "" ){
		insert_evaluation();
		$modifierGrilleEvaluationEncore = true;
		$afficherMenu = false;
		$sauvegarderInformationsGrille = true;
		unset($idAjoutEval);
}

// Delete the evaluation
if (isset ($idSuppEval) && $idSuppEval != "" ){
	del_evaluation($idSuppEval);
	$modifierGrilleEvaluationEncore = true;
	$sauvegarderInformationsGrille = true;
	$afficherMenu = false;
	unset($idSuppEval);
}

if (isset($action) && ($action== get_lang('Save'))){
	 $sauvegarderInformationsGrille = true;
	 $afficherMenu = true;
}

// Save the evaluation grid

if ($sauvegarderInformationsGrille){
    $evalCouseList = get_evaluation_course_list();
	foreach ($evalCouseList as $thisEvaluation)  
	{
    	 $idEvaluation =  $thisEvaluation['evaluation_id'] ;
    	 $champTitre = $idEvaluation ."_titre";
    	 $champTitre = isset($_POST[$champTitre]) ? $champTitre = $_POST[$champTitre] : '';
    	 $champMaximum = $idEvaluation ."_maximum";
    	 $champMaximum = isset($_POST[$champMaximum]) ? $champMaximum = $_POST[$champMaximum] : '';
    	 $champPonderation = $idEvaluation ."_ponderation";
    	 $champPonderation = isset($_POST[$champPonderation]) ? $champPonderation = $_POST[$champPonderation] : '';
    	 $retour = set_evaluation($champTitre,$champMaximum,$champPonderation,$idEvaluation);
    }
    if ($retour)
        $dialogBox->success(get_lang('Results saved'));
 }

 // Construct evaluation grid
 
if ((isset($action) && ($action== get_lang('Enter evaluations'))) || $modifierGrilleEvaluationEncore){
	 
	 $out .= "<form name='frmGrille' method='post' action='".$_SERVER['PHP_SELF']."' onSubmit='return validerFormEntrerEvaluations();'>";
	 $out .= "<table width='75%' bgcolor=\"#E6E6E6\" border=\"1\"><tr><th width=\"80%\">".get_lang('Evaluation')."</th><th>".get_lang('Max result')."</th><th>".get_lang('Weighting')."</th><th>";
	 $out .= "</th></tr>";
	 
	 $evalCourseList = get_evaluation_course_list();
	 foreach ($evalCourseList as $thisEvaluation)  //modifier les notes des étudiants pour chaque évaluation dans la grille d'évaluation
	 {
		 $idEvaluation =  $thisEvaluation['evaluation_id'] ;
		 $champTitre = $idEvaluation ."_titre";
		 $champMaximum = $idEvaluation ."_maximum";
		 $champPonderation = $idEvaluation ."_ponderation";
		 $titre = $thisEvaluation['titre'] ;
		 $maximum = $thisEvaluation['maximum'];
		 $ponderation = $thisEvaluation['ponderation'];
		 $out .= "<tr>";
		 $out .= "<td><input type=\"text\" maxlength='255' size=\"60\" name=\"".$champTitre."\" value ='".$titre."'></td>";
		 $out .= "<td><input type=\"text\" maxlength='10' size=\"10\" name=\"".$champMaximum."\" value ='".$maximum."' onchange ='validerChampNumerique(this.value)'></td>";
		 $out .= "<td><input type=\"text\" maxlength='10'size=\"10\" name=\"".$champPonderation."\" value ='".$ponderation."' onchange ='validerChampNumerique(this.value)'></td>";
		 $out .= '<td><a href="#" onclick="document.forms.frmGrille.idSuppEval.value ='.$idEvaluation.'; var reponse = window.confirm(\''.get_lang('This evaluation and the results for this evaluation will be deleted. Continue?').'\'); if (reponse){document.forms.frmGrille.submit();}else{document.forms.frmGrille.idSuppEval.value =\'\';}">
		 	<img src="' . get_icon_url('delete') . 'delete.gif" alt="Supprimer cette évaluation" border="0"></a></td>';
		 $out .= "</tr>";  
	 }
	 $out .= "</table>";
	 $out .= "<input type=\"hidden\" name=\"idSuppEval\" value =''>";
	 $out .= '<br><div><input type="submit" name="idAjoutEval" value="'.get_lang('Ajouter une évaluation') .'"></div>';
	 $out .= "<br><br>";
	 $out .= "<p align='center'><input type=\"submit\" name=\"action\" value=\"".get_lang('Save')."\"> &nbsp;&nbsp;";
	 $out .= claro_html_button($_SERVER['PHP_SELF'],get_lang('Back to the menu'));
	 $out .= "</p>";
	 $afficherMenu = false;
}

// Save evaluations of students

if ((isset($action) && ($action== get_lang('Save results')) ))
	{
	//	event_access_tool($_tid, $_courseTool['label']);
		//Liste des évaluations
		$evalCourseList = get_evaluation_course_list();
	    foreach ($evalCourseList as $thisEvaluation)  //modifier les notes des étudiants pour chaque évaluation dans la grille d'évaluation
		{
			$evaluation_id = $thisEvaluation['evaluation_id'];
            $users = get_evaluation_course_user_list();
			foreach ($users as $thisUser)
			{
				$userId=$thisUser['user_id'];
				//Lire le formulaire posté
				$nomChamp = $userId  . '_' . $evaluation_id;
				$note_etudiant  = isset($_POST[$nomChamp]) ? $note_etudiant = trim($_POST[$nomChamp]) : '';
				if ($note_etudiant != ''){ //Mettre  à jour ou  ajouter selon  le résultat de l'étudiant pour cet évaluation
                    $retour = set_evaluation_note($userId,$evaluation_id,$note_etudiant);
                    
					//Permet d'avertir l'étudiant qu'un résultat est rentré.
					//$eventNotifier->notifyCourseEvent('resultats_event_added', $_cid, $_tid, $userId, $_gid, $_uid);
					//$ex_rss_refresh = TRUE;
				}
			}
		}
		if ($retour) $dialogBox->success(get_lang('Evaluation saved'));
		
	  //Grille d'évaluation  ajoutée
	  unset($action);
	  $afficherMenu = true;
	}
	
// Construct grid to encode student's evaluations

if (isset($action)){
	//ENTRER LES NOTES DES ÉTUDIANTS : ASSERTATION:  LE COURS CONTIENT DES ÉTUDIANTS ET CONTIENT DES ÉVALUATIONS
	if (($action==get_lang('Enter results'))){
		//Liste des évaluations du cours	
		
		$out .= 	"<form name ='frmEntrerResultats' action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" onSubmit='return validerFormEntrerResultats();'><table width=\"100%\" cellpadding=\"2\" cellspacing=\"1\" border=\"1\">\n";
		//COLONES  = ÉVALUATION  {TITRE, NOTE_MAX, PONDERATION}
		$nbEvaluations = 0;
		$out .= "<tr align=\"center\" valign=\"top\" bgcolor=\"#E6E6E6\">";
		$out .= "<td><b>NIP</b></td><td><b>Nom</b></td><td><b>Prénom</b></td>";
		
		$evalCourseList = get_evaluation_course_list();
		foreach ($evalCourseList as $thisEvaluation)
		{
			$listeEvaluations[$nbEvaluations] = $thisEvaluation['evaluation_id'];  // Save evaluation order list
			$out .= "<td align=\"center\"><b>". $thisEvaluation['titre'] ."</b>&nbsp;(". $thisEvaluation['maximum'] .")&nbsp;".$thisEvaluation['ponderation'] . "%</td>";
			$out .= "<input type=\"hidden\" name='evaluation" . $nbEvaluations . "' value='". $thisEvaluation['maximum']. "'>";
			$nbEvaluations++;
		}
		$out .= "</tr>";
		//Sélectionner les étudiants inscrits au cours présentement.
        $users = get_evaluation_course_user_list();
		$nbEtudiants = 0;
		//LIGNE = ÉTUDIANT {NOM, PRÉNOM, NIP}
		foreach ($users as $thisUser)
		{
		    $nbEtudiants++;
			$userName = $thisUser['username'];
			$userId = $thisUser['user_id'];
			$nom = $thisUser['nom'];
			$prenom = $thisUser['prenom'];
			$out .= "<tr align=\"center\" valign=\"top\" bgcolor=\"#E6E6E6\">\n".
				 "<td align=\"left\" width=\"10%\"><b>". $userName ."</b></td>\n".
				 "<td align=\"left\">". $nom ."</td>\n".
				 "<td align=\"left\">". $prenom ."</td>";
			
			//Pour chaque évaluation,  afficher la note de l'étudiant si disponible.
			$notes = get_evaluation_student_note($userId);
			for($i=0;$i<$nbEvaluations;$i++)
			{
				$noteEvalId     = $listeEvaluations[$i];
				$noteResult		= '';	
				
			    foreach ($notes as $note) {
		            if ($note['evaluation_id']==$listeEvaluations[$i])
    				    $noteResult		= $note['note'];		    
			    }
				
			    //Afficher la note si présente
				$out .= '<td align=\"left\" width=\"10%\">';
				$nomChampHiddenEvaluation = "document.frmEntrerResultats.evaluation". $noteEvalId .".value";
				$out .= '<input type="text" maxlength="6" name="'.$userId  . '_' .$noteEvalId .'" value = "'.$noteResult.'" size="6" ';
				if (claro_is_javascript_enabled()) 
					$out .= 'onchange = "validerNote(this.value, ' . $nomChampHiddenEvaluation . ')"';
				$out .= '></td>';
			}
			$out .= "</tr>";
		}
	  $out .= 	"</table>\n";
	  $out .= "<br><Strong> * Une case vide ne comptera pas dans le calcul de la moyenne. Veuillez inscrire 0 si c'est le cas.</Strong>";
	  $out .= "<input type=\"hidden\" name=\"nbEvaluations\" value=\"$nbEvaluations\">";
	  $out .= "<input type=\"hidden\" name=\"nbEtudiants\" value=\"$nbEtudiants\">";
	  $out .= "<p align='right'> <input type=\"submit\" name=\"action\" value=\"".get_lang('Save results')."\">&nbsp;&nbsp;&nbsp;&nbsp;";
	 $out .= claro_html_button($_SERVER['PHP_SELF'],get_lang('Back to the menu')). "</p></form>";
	  $afficherMenu = false;
	}
} 


// Display student's evalaution to student and to manager

if (!$is_allowedToEdit) 
{
	/*event_access_tool($_tid, $_courseTool['label']);
	$id=$row[0]; */
	$i = 0;
    $userNotes = get_evaluation_student_note(claro_get_current_user_id());
	$evalCourseList = get_evaluation_course_list();
	
	$out .= "<table cellpadding=\"2\" cellspacing=\"1\" border=\"1\">";

	$out .=	"<thead><tr class='claroBlockHeader' align='center' valign='top'>".
			"<th style='padding:0px 50px 0px 50px'>".get_lang('Title')."</th>\n".
			"<th style='padding:0px 20px 0px 20px'>".get_lang('Result')."</th>\n".
			"<th style='padding:0px 20px 0px 20px'>".get_lang('Max result')."</th>\n".
			"<th style='padding:0px 20px 0px 20px'>".get_lang('Weighting')."</th>\n".
	        "</tr></thead>";
	
	$totalMax = 0;
	$totalResult = 0;	
	
	foreach ($evalCourseList as $row)
	{
	  $out .= "<tr><td style='padding:0px 50px 0px 50px;text-align:center'>". $row['titre'] ."</td>";
	  $out .= "<td align=\"center\">&nbsp;";
	  foreach ($userNotes as $row2)
        {
            if ($row2['evaluation_id']==$row['evaluation_id'])
            {
                $out .= $row2['note']; //Afficher la note à l'étudiant
                $totalMax =  $totalMax + ($row['ponderation']);
                $totalResult = $totalResult + ($row2['note']/$row['maximum'])*$row['ponderation'];
            }
        }
	  $out .= "</td>";
	  $out .= "<td align=\"center\">". $row['maximum'] ."</td>";
	  $out .= "<td align=\"center\">". $row['ponderation'] ."</td>";
	  $out .="</tr>";
    }
    $out .= "<tr><td style='text-align:center;font-weight:bold'>".get_lang('Average')."</td><td align=\"center\">"
                .round($totalResult,1)."</td><td>&nbsp;</td><td align=\"center\">".round($totalMax,1)."</td></tr>";
    $out .= "</table>\n";
    $out .= "<br>";
}
else // Afficher les résultats des étudiants ainsi que les moyennes au professeur
{
	if (isset($action)  && ($action == get_lang('Display results')))
	{
	   $sortie = ""; //Texte à afficher à l'écran dans cette section
	   $ok_to_display=false;
	   $afficherMenu = false;
         
	   $sortie.= "<table width='100%' cellpadding='2' cellspacing='1' border='1'>\n";
	   $sortie.= "<tr align='center' valign='top' bgcolor='#E6E6E6'>\n";
	   $sortie.= "<td align='center' ><b>".get_lang('Username')."</b></td>\n";
	   $sortie.= "<td align='center'><b>".get_lang('Name')."</b></td>\n";
	   $sortie.= "<td align='center'><b>".get_lang('First name')."</b></td>\n";
	   $nbEvaluations = 0;
	   $i = 0; 
	   
	   // Affichage des colonnes
	   $evalCourseList = get_evaluation_course_list();
	   foreach ($evalCourseList as $eval)
        {
        	  $sortie.="<td align=\"center\"> <b>".$eval['titre'] . "</b>";
        	  if (($eval['maximum'])&&($eval['maximum']!="1"))
        	     $sortie.= "<br> sur ".$eval['maximum'];
        	  if (($eval['ponderation'])&&($eval['ponderation']!="1"))
        	     $sortie.= "<br>".$eval['ponderation']."% ";
        	  $sortie.= "</td>\n";
        	  $maximum[$i]=$eval['maximum'];
        	  if (!isset($action))
        	  {
        	     $entete_colonne[$i][0]=$eval['titre'];
        	     $entete_colonne[$i][1]=$eval['maximum'];
        	     $entete_colonne[$i][2]=$eval['ponderation'];
        	  }
        	  $pond[$i++]=$eval['ponderation'];
        	  $evaluations[$nbEvaluations] = $eval['evaluation_id']; //GARDER L'ORDRE DES ÉVALUATIONS (DES COLONNES) EN MÉMOIRE
        	  $nbEvaluations++;
        }
        
        // Affichage des étudiants et des résultats
		$sortie.= "<td align=\"center\"><b>Total</b></td>\n";
		$sortie.=	"</tr>\n";
		
        $users = get_evaluation_course_user_list();
		foreach ($users as $thisUser)
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

			$usersNotes = get_evaluation_student_note($thisUser['user_id']);
			//Calculer la moyenne de l'étudiant à partir de la note de chaque travail.
			$moyenne_etudiant = 0;
			$affichageNotesTravaux = array();
			foreach ($usersNotes as $note) //Afficher les notes pour cet utilisateur
			{
				//Parcourir le tableaux des évaluations, mettre le résultat dans la bonne colonne
				for ($noEvaluation = 0; $noEvaluation < $nbEvaluations; $noEvaluation ++){
					if ($evaluations[$noEvaluation] == $note['evaluation_id'] ){
						$affichageNotesTravaux[$noEvaluation] = "<td align=\"right\">&nbsp;".$note['note']."</td>\n";
						$moyenne_etudiant += (($note['note'] / $note['maximum'] ) * $note['ponderation'] );
					}
				}
			}
			
			//Afficher les notes pour cet utilisateur avec les colonnes vides
			for ($x = 0; $x < $nbEvaluations ; $x++){
				if (!isset($affichageNotesTravaux[$x]))
					$sortie.= "<td align=\"right\">&nbsp;".
							"</td>\n";
			    else
					$sortie.= $affichageNotesTravaux[$x];
			}
			
			//Afficher la moyenne de l'étudiant pour le cours.
			$sortie.= "<td align = 'right' bgcolor=\"#E6E6E6\"> &nbsp; <b>" . round($moyenne_etudiant,1) ."</b>" ;
			$sortie.=	"</td> </tr>\n";
		}
		
		//Afficher la moyenne des étudiants pour chaque travail
		$resultMoyenne = get_evaluation_average();
		$sortie.= "<tr bgcolor=\"#E6E6E6\"> <td align = 'center' ><b>". get_lang('Average') ."</b></td><td>&nbsp;</td><td>&nbsp;</td>";
		//Calculer la moyenne de la note finale.
		$total = 0;
		$ponderationTotal = 0;
		foreach ($resultMoyenne as $thisMoyenne)
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
			//$out .= "rentre une fois <br>";
			if (!isset($affichageMoyennesTravaux[$x]))
				$sortie.= '<td>&nbsp;</td>';
		    else
				$sortie.= $affichageMoyennesTravaux[$x];
		}
		
		//Afficher la moyenne du cours (TOTAL)
		$sortie.= "<td align = 'right' ><b>". round($total,1) ." sur " . $ponderationTotal . "</b></td>";
		$sortie.= "</tr>";
	    $sortie.= "</table>\n<br>";
		
		$out .= $sortie; //afficher le tout.
		
		$out .= "<div align='right'>";
		$out .= claro_html_button($_SERVER['PHP_SELF'],get_lang('Back to the menu'));
		$out .= "</div>";			
		// filepath

	    $fileName = $baseWorkDir . '/results/results.xls';
	    
	    $fresultat = fopen ($fileName, "w+");
		
		fwrite($fresultat,"<html><head></head><body>".$sortie."</body></html>");
		fclose($fresultat);

		$downloadLink = '<a href="'.get_path('coursesRepositoryWeb').claro_get_course_path().'/results/results.xls'.'">' .get_lang('Click here to obtain an Excel file of these results').'</a>';	
		$out .= $downloadLink;
		
		unset($action); // FIN AFFICHAGE DES RÉSULTATS
	}
}

// Display menu

if ($is_allowedToEdit){
   $out .= "<a name=\"#debut\">";
   $out .= "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">";
	if ($afficherMenu){
      $out .= "<center><input type=\"submit\" name=\"action\" value=\"".get_lang('Enter evaluations')."\">";
	 
      // Si le cours contient des évaluations
	  $nbEval = count(get_evaluation_course_list());
	  if ($nbEval > 0)
	  {
		$nbNote = count(get_evaluation_course_user_list()); //resultat
		if ($nbNote > 0){ //Si le cours contient des étudiants
		    //Afficher les boutons pour pouvoi entrer les notes des étudiants et afficher les résultats + moyennes.
			$out .= "<input type=\"submit\" name=\"action\" value=\"".get_lang('Enter results')."\">";
			$out .= "<input type=\"submit\" name=\"action\" value=\"".get_lang('Display results')."\"><br>";
		}
	  }
	}
	
   $out .= "</center></form><br>";
}

/*  DISPLAY SECTION */

$out .= $dialogBox->render();

$claroline->display->body->appendContent($out);

// generate output
echo $claroline->display->render();
?>