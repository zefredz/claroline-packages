<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

	// protect file
	if( count( get_included_files() ) == 1 )
	{
		die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
	}

    /**
     * CLAROLINE
     *
     * @version 1.9 $Revision$
     *
     * @copyright 2001-2006 Universite catholique de Louvain (UCL)
     *
     * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
     * This program is under the terms of the GENERAL PUBLIC LICENSE (GPL)
     * as published by the FREE SOFTWARE FOUNDATION. The GPL is available
     * through the world-wide-web at http://www.gnu.org/copyleft/gpl.html
     *
     * @author KOCH Gregory <gregk84@gate71.be>
     *
     * @package CLFAQ
     */

	 
	 
	 
	//claro_sql_query_get_single_value => selectionne 1 valeur
	//claro_sql_query_get_single_row => selectionne 1 ligne
	//claro_sql_query_fetch_all_rows => selectionne toutes les lignes
	 
	 
	 
	 
	 
class QuizsStats
{
 
	var $data;
	var $pointages;
	var $ponderationtotal;
	var $nbparticipations;
	var $datelastparticipation;
		
	// dDateLastParticipation
	function getDateLastParticipation()
	{
    	return $this->datelastparticipation;
	}
	
	// NbParticipations
	function getNbParticipations()
	{
    	return $this->nbparticipations;
	}

	// ponderationtotal
	function getPonderationTotal()
	{
    	return $this->ponderationtotal;
	}
	
	function setPonderationTotal($ponderationtotal)
	{
		$this->ponderationtotal = $ponderationtotal;
	}
	
	// averageHTML
	function getAverageHTML()
	{
    	return $this->averageHTML;
	}
	
	// medianeHTML
	function getMedianeHTML()
	{
    	return $this->medianeHTML;
	}
	
	// iPointages
	function getPointages()
	{
    	return $this->pointages;
	}
	
	// nbPartGT60
	function getNbPartGT60()
	{
    	return $this->nbPartGT60;
	}
	
	// constructeur
	function QuizsStats($data)
	{
		$this->data = $data;
		$this->pointages = null;
		$this->ponderationtotal = null;
		$this->nbparticipations = null;
		$this->datelastparticipation = null;
	}
	
	// compute
	function compute()
	{
		$this->nbparticipations = count($this->data);
		
		$this->selectDateLastParticipation();
		
		$this->selectScore();
		
		$this->selectAverage();
		
		$this->selectMediane();
		
		$this->selectNumerPaticipantsMoyenne();
	}
	
	// selectDateLastParticipation
	function selectDateLastParticipation()
	{    
		$this->iFinal = $this->data[0]['Final'];
		if(intval($this->iFinal) == 1){
			$this->datelastparticipation =  date($GLOBALS['sDefaultDateHourFormat'],$this->data[0]['ParticipationDate']);
		}else{
			$this->datelastparticipation = "-";
		}
	}
	
	//Score array
    function selectScore()
	{    
		$this->pointages = array();
		for($i = 0;$i < $this->nbparticipations;$i++){
			$this->pointages[$i] = $this->data[$i]['PointageTotal'];
		}
	
		sort($this->pointages);
	}
	
	//Average
	function selectAverage()
	{    
		$this->fAverage = average($this->pointages);
		$this->averageHTML = getFormatedScore($this->fAverage,$this->getPonderationTotal());
	}
	
	//Mediane
	function selectMediane()
	{
		$this->fMediane = mediane($this->pointages);
		$this->medianeHTML = getFormatedScore($this->fMediane,$this->getPonderationTotal());
	}
	
	//Number of participations with a score gretter than 60%
	function selectNumerPaticipantsMoyenne()
	{
		$this->nbPartGT60 = nbGT($this->pointages,(0.6 * $this->getPonderationTotal()));
	}

}	
	


	
	 
// class category
class netquiz 
{

	/*
	var $id = null;
	var $category = '';
	var $description = '';
	*/
	
	
	
	
	
	// authparticipant.php -> récupérer le dernier IDParticipant en DB
	function lastIdParticipant()
	{
			
		$tblNameList = array(
			'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participants` par  `".$nameTables['nq_participants']."`	
		
		$sql =   "select max(IDParticipant) as last_id from `nq_participants`";
		
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('LOADED_FAILED');
		}
			
	}

	
	
	
	
	// addquiz.php -> récupérer le dernier IdQuiz en DB
	function lastIdQuiz()
	{
			
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`	
		
		$sql =   "select max(IDQuiz) as last_id from `nq_quizs`";
		
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('LOADED_FAILED');
		}
			
	}
	
	
	
	
	
	// sQuizIdent
	function getQuizIdent()
	{
    	return $this->quizident;
	}
	
	function setQuizIdent($quizident)
	{
		$this->quizident = $quizident;
	}
	
	
	// sQuizVersion
	function getQuizVersion()
	{
    	return $this->quizversion;
	}
	
	function setQuizVersion($quizversion)
	{
		$this->quizversion = $quizversion;
	}
	
	// authparticipant.php -> récupérer IdQuiz en DB
	// addquiz.php -> récupérer IdQuiz en DB
	function fetchIdQuiz()
	{
		if( is_null( $this->getQuizIdent() ) && is_null( $this->getQuizVersion() ) )
		{
			return false;
		}
		else
		{
			// load from db
			// properties
			// Nom de/des DB
			
			$tblNameList = array(
				'nq_quizs'
			);
			
			$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			
			### Debug ###
			// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`	
			
			$sql = "select IDQuiz from `nq_quizs` where QuizIdent = '".$this->getQuizIdent()."' and QuizVersion = '".$this->getQuizVersion()."'";
			$result = claro_sql_query_get_single_value ($sql);
			
			if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
			{
				//return $this->_setProperties($result);
				return $result;
			}
			else
			{				
				return claro_failure::set_failure('IDQUIZ_LOADED_FAILED');
			}
		}
	}
	

	
	
	
	// sPrenom
	function getPrenom()
	{
    	return $this->prenom;
	}
	
	function setPrenom($prenom)
	{
		$this->prenom = $prenom;
	}
	
	// sNom
	function getNom()
	{
    	return $this->nom;
	}
	
	function setNom($nom)
	{
		$this->nom = $nom;
	}
	
	// sGroupe 
	function getGroupe()
	{
    	return $this->groupe;
	}
	
	function setGroupe($groupe)
	{
		$this->groupe = $groupe;
	}
	
	// sMatricule
	function getMatricule()
	{
    	return $this->matricule;
	}
	
	function setMatricule($matricule)
	{
		$this->matricule = $matricule;
	}
	
	// sCourriel
	function getCourriel()
	{
    	return $this->courriel;
	}
	
	function setCourriel($courriel)
	{
		$this->courriel = $courriel;
	}
	
	// iIDQuiz
	function getIdQuiz()
	{
    	return $this->idquiz;
	}
	
	function setIdQuiz($idquiz)
	{
		$this->idquiz = $idquiz;
	}
	
	// authparticipant.php -> insérer le participant en DB
	function insertUser()
	{
		
		// Nom de/des DB
		$tblNameList = array(
			'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`
		
		$sql = "INSERT INTO `nq_participants` (Prenom, Nom, Groupe, Matricule, Courriel, IDQuiz) VALUES (".toSQLString( $this->getPrenom(),false ).",".toSQLString( $this->getNom(),false ).",".toSQLString( $this->getGroupe(),false ).",".toSQLString( $this->getMatricule(),false ).",".toSQLString( $this->getCourriel(),false ).",".toSQLString( $this->getIdQuiz(),false ).");";

		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{
			return claro_failure::set_failure('PARTICIPANT_INSERTION_FAILED');
		}
		
	}

	
	
	
/*
	// QuizIdent
	function getQuizIdent()
	{
    	return $this->quizident;
	}
	
	function setQuizIdent($quizident)
	{
		$this->quizident = $quizident;
	}
	
	// QuizVersion
	function getQuizVersion()
	{
    	return $this->quizversion;
	}
	
	function setQuizVersion($quizversion)
	{
		$this->quizversion = $quizversion;
	}
*/	
	// QuizName
	function getQuizName()
	{
    	return $this->quizname;
	}
	
	function setQuizName($quizname)
	{
		$this->quizname = $quizname;
	}

	// NbQuestions
	function getNbQuestions()
	{
    	return $this->nbquestions;
	}
	
	function setNbQuestions($nbquestions)
	{
		$this->nbquestions = $nbquestions;
	}
	/*
	// VersionDate
	function getVersionDate()
	{
    	return $this->versiondate;
	}
	
	function setVersionDate($versiondate)
	{
		$this->versiondate = $versiondate;
	}
	*/
	// Password
	function getPassword()
	{
    	return $this->password;
	}
	
	function setPassword($password)
	{
		$this->password = $password;
	}
	
	// Auteur
	function getAuteur()
	{
    	return $this->auteur;
	}
	
	function setAuteur($auteur)
	{
		$this->auteur = $auteur;
	}
	
	// Actif
	function getActif()
	{
    	return $this->actif;
	}
	
	function setActif($actif)
	{
		$this->actif = $actif;
	}
	
	// addquiz.php -> insérer un quiz en DB
	function insertQuiz()
	{
		
		// Nom de/des DB
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`
		
		$sql = "INSERT INTO `nq_quizs` (QuizIdent, QuizVersion, QuizName, NbQuestions, VersionDate, Password, Auteur, Actif) VALUES ('".addslashes( $this->getQuizIdent() )."','".addslashes( $this->getQuizVersion() )."','".addslashes( $this->getQuizName() )."','".addslashes( $this->getNbQuestions() )."',NOW(),'".addslashes( $this->getPassword() )."','".addslashes( $this->getAuteur() )."','".addslashes( $this->getActif() )."');";

		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{
			return claro_failure::set_failure('QUIZ_INSERTION_FAILED');
		}
		
	}
	


	
	
	// sTitre
	function getTitre()
	{
    	return $this->titre;
	}
	
	function setTitre($titre)
	{
		$this->titre = $titre;
	}
	
	// iType
	function getType()
	{
    	return $this->type;
	}
	
	function setType($type)
	{
		$this->type = $type;
	}
	
	// sType (TypeTD dans la db)
	function getTypeTd()
	{
    	return $this->typetd;
	}
	
	function setTypeTd($typetd)
	{
		$this->typetd = $typetd;
	}
	
	// iPonderation
	function getPonderation()
	{
    	return $this->ponderation;
	}
	
	function setPonderation($ponderation)
	{
		$this->ponderation = $ponderation;
	}
	
	// sEnonce
	function getEnonce()
	{
    	return $this->enonce;
	}
	
	function setEnonce($enonce)
	{
		$this->enonce = $enonce;
	}
	
	// sReponseXML
	function getReponseXML()
	{
    	return $this->reponsexml;
	}
	
	function setReponseXML($reponsexml)
	{
		$this->reponsexml = $reponsexml;
	}
	
	/*
	// iIDQuiz
	function getIdQuiz()
	{
    	return $this->idquiz;
	}
	
	function setIdQuiz($idquiz)
	{
		$this->idquiz = $idquiz;
	}
	*/
	
	// iNoQuestion
	function getNoQuestion()
	{
    	return $this->noquestion;
	}
	
	function setNoQuestion($noquestion)
	{
		$this->noquestion = $noquestion;
	}
	
	// addquiz.php -> insérer les questions en DB
	function insertQuestions()
	{
		
		// Nom de/des DB
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."`
		
		$sql = "INSERT INTO `nq_questions` (QuestionName, QuestionType, QuestionTypeTD, Ponderation, EnonceHTML, ReponseXML, IDQuiz, NoQuestion) VALUES ('".addslashes( $this->getTitre() )."', '".addslashes( $this->getType() )."', '".addslashes( $this->getTypeTd() )."', '".addslashes( $this->getPonderation() )."', '".addslashes( $this->getEnonce() )."', '".addslashes( $this->getReponseXML() )."', '".addslashes( $this->getIdQuiz() )."', '".addslashes( $this->getNoQuestion() )."');";

		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{
			return claro_failure::set_failure('QUESTIONS_INSERTION_FAILED');
		}
		
	}
	
	
	
	
	// addquizlist.php -> select QuizIdent et QuizVersion de la table nq_quizs  en DB
	function selectAllQuizs()
	{
			
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`	
		
		$sql =   "select QuizIdent,QuizVersion from `nq_quizs`";
		
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('LOADED_FAILED');
		}
			
	}
	
	
	
	
	
	// quizlist.php -> select les questions en DB
	function selectQuizsList()
	{
			
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`	
		
		$sql =   "select *,UNIX_TIMESTAMP(VersionDate) AS TS_VersionDate from `nq_quizs`";
		
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('LOADED_FAILED');
		}
			
	}	
	
	
	
	
	
	// iIdQuestion
	function getIdQuestion()
	{
    	return $this->idquestion;
	}
	
	function setIdQuestion($idquestion)
	{
		$this->idquestion = $idquestion;
	}
	
	// editpointage.php -> select les ponderations en DB
	function selectPonderation()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."`	
		
		$sql = "select Ponderation from `nq_questions` where IDQuestion = '".$this->getIdQuestion()."'";
		
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('LOADED_FAILED');
		}
			
	}	

	
	
	
	
	/*
	// iIdQuestion
	function getIdQuestion()
	{
    	return $this->idquestion;
	}
	
	function setIdQuestion($idquestion)
	{
		$this->idquestion = $idquestion;
	}
	*/
	
	// fPointage
	function getPointage()
	{
    	return $this->pointage;
	}
	
	function setPointage($pointage)
	{
		$this->pointage = $pointage;
	}

	// iIDParticipant
	function getIdParticipant()
	{
    	return $this->idparticipant;
	}
	
	function setIdParticipant($idparticipant)
	{
		$this->idparticipant = $idparticipant;
	}

	// editpointage.php -> update des points en DB
	function updateScore()
	{
			
		$tblNameList = array(
			'nq_participations'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participations` par  `".$nameTables['nq_participations']."`	
		
		$sql = "update `nq_participations` set Pointage = '".$this->getPointage()."' where IDQuestion = '".$this->getIdQuestion()."' and IDParticipant = '".$this->getIdParticipant()."'";
		
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('UPDATED_FAILED');
		}
			
	}	
	
	
	
	
	
	/*
	// iIdQuestion
	function getIdQuestion()
	{
    	return $this->idquestion;
	}
	
	function setIdQuestion($idquestion)
	{
		$this->idquestion = $idquestion;
	}
	*/
	
	// fPointage
	function getQuestionsActif()
	{
    	return $this->questionsactif;
	}
	
	function setQuestionsActif($questionsactif)
	{
		$this->questionsactif = $questionsactif;
	}

	// editqstatus.php -> update du status des questions
	function updateQuestionsStatus()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."`	
		
		$sql = "update `nq_questions` set Active = '".$this->getQuestionsActif()."' where IDQuestion = '".$this->getIdQuestion()."'";
		
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('UPDATED_FAILED');
		}

	}	

	
	
	
	
	/*
	// iIDParticipant
	function getIdParticipant()
	{
    	return $this->idparticipant;
	}
	
	function setIdParticipant($idparticipant)
	{
		$this->idparticipant = $idparticipant;
	}
	*/
	
	// iActif
	function getParticipantsActif()
	{
    	return $this->participantsactif;
	}
	
	function setParticipantsActif($participantsactif)
	{
		$this->participantsactif = $participantsactif;
	}

	// editsstatus.php -> update du status des participants
	function updateParticipantsStatus()
	{
			
		$tblNameList = array(
			'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participants` par  `".$nameTables['nq_participants']."`	
		
		$sql = "update `nq_participants` set Actif = '".$this->getParticipantsActif()."' where IDParticipant = '".$this->getIdParticipant()."'";

		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('UPDATED_FAILED');
		}
			
	}	

	
	
	
	/*
	// iIDQuiz
	function getIdQuiz()
	{
    	return $this->idquiz;
	}
	
	function setIdQuiz($idquiz)
	{
		$this->idquiz = $idquiz;
	}
	*/

	// sOrderByField
	function getOrderByField()
	{
    	return $this->orderbyfield;
	}
	
	function setOrderByField($orderbyfield)
	{
		$this->orderbyfield = $orderbyfield;
	}

	// sOrderByDirection
	function getOrderByDirection()
	{
    	return $this->orderbydirection;
	}
	
	function setOrderByDirection($orderbydirection)
	{
		$this->orderbydirection = $orderbydirection;
	}

	// exportparticipations.php -> export des participations
	function selectParticipations()
	{
			
		$tblNameList = array(
			'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participants` par  `".$nameTables['nq_participants']."`	
		
		$sql =   "select nq_participants.IDParticipant, nq_participants.Prenom, nq_participants.Nom, nq_participants.Groupe, nq_participants.Final,  nq_participants.Courriel, " .
                "UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDate, sum(nq_participations.Pointage) as PointageTotal, nq_participants.Matricule, nq_participants.Actif " .
                "from nq_participants " .
                "left join nq_participations using (IDParticipant) " .
                "right join nq_questions on nq_participations.IDQuestion = nq_questions.IDQuestion " .
                "where nq_questions.Active = 1 and " .
                "nq_participants.IDQuiz = '".$this->getIdQuiz()."' " .
                "group by nq_participants.IDParticipant " .
                "order by '".$this->getOrderByField()."' '".$this->getOrderByDirection()."'";
		
		
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}	
	
	
	
	
	
	/*
	// iIDQuiz
	function getIdQuiz()
	{
    	return $this->idquiz;
	}
	
	function setIdQuiz($idquiz)
	{
		$this->idquiz = $idquiz;
	}
	*/

	// exportparticipations.php -> Nom du quiz et date de version
	// exportquestions.php -> Nom du quiz et date de version
	function selectNameQuizAndDate()
	{
			
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`	
		$sql = "select QuizName, UNIX_TIMESTAMP(VersionDate) as VersionDate from `nq_quizs` where IDQuiz = '".$this->getIdQuiz()."'";
		
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}	

	
	
	
	
	/*
	// iIDQuiz
	function getIdQuiz()
	{
    	return $this->idquiz;
	}
	
	function setIdQuiz($idquiz)
	{
		$this->idquiz = $idquiz;
	}
	*/

	// exportparticipations.php -> recuperation du nombre Total du quiz
	function selectPonderationTotal()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."`	
		$sql = "select sum(Ponderation) as PonderationTotal from `nq_questions` where IDQuiz = '".$this->getIdQuiz()."'";
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
	
	
	
	
	
	/*
	// iIDQuiz
	function getIdQuiz()
	{
    	return $this->idquiz;
	}
	
	function setIdQuiz($idquiz)
	{
		$this->idquiz = $idquiz;
	}
	

	// sOrderByField
	function getOrderByField()
	{
    	return $this->orderbyfield;
	}
	
	function setOrderByField($orderbyfield)
	{
		$this->orderbyfield = $orderbyfield;
	}

	// sOrderByDirection
	function getOrderByDirection()
	{
    	return $this->orderbydirection;
	}
	
	function setOrderByDirection($orderbydirection)
	{
		$this->orderbydirection = $orderbydirection;
	}
	*/
	
	// exportquestions.php -> export des participations
	function selectQuestions()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."`	
		
		$sql =  "select nq_questions.NoQuestion, nq_questions.Ponderation, AVG(nq_participations.Pointage) as Average, " .
                "nq_questions.QuestionName , nq_questions.QuestionTypeTD, nq_questions.IDQuestion, nq_questions.Active " .
                "from nq_questions " .
                "left join nq_participations using (IDQuestion) " .
                "left join (select * from nq_participants where Actif = 1) nq_participants_actif on nq_participations.IDParticipant = nq_participants_actif.IDParticipant " .
                "where nq_questions.IDQuiz = '".$this->getIdQuiz()."' " .
                "group by nq_questions.IDQuestion " .
                "order by '".$this->getOrderByField()."' '".$this->getOrderByDirection()."'";
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}




	
	// quizdelete.php -> IDQuestion
	function selectIdQuestion()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."`	
		$sql = "select IDQuestion from `nq_questions` where IDQuiz = '".$this->getIdQuiz()."'";
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}

	
	
	
	
	// quizdelete.php -> Select participations
	function selectAllParticipations()
	{
			
		$tblNameList = array(
			'nq_participations'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participations` par  `".$nameTables['nq_participations']."`	
		$sql = "select * from `nq_participations` where IDQuestion = '".$this->getIdQuestion()."'";
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
	
	
	
	
	
	// quizdelete.php -> Delete participations
	function deleteAllParticipations()
	{
			
		$tblNameList = array(
			'nq_participations'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participations` par  `".$nameTables['nq_participations']."`	
		$sql = "delete from `nq_participations` where IDParticipant = '".$this->getIdParticipant()."' and IDQuestion = '".$this->getIdQuestion()."'";
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('DELETED_FAILED');
		}
			
	}
	
	
	
	
	
	// quizdelete.php -> Delete questions
	function deleteAllQuestions()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."`	
		$sql = "delete from `nq_questions` where IDQuestion = '".$this->getIdQuestion()."'";
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('DELETED_FAILED');
		}
			
	}



	
	
	// quizdelete.php -> Delete quizs
	function deleteQuizs()
	{
			
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`	
		$sql = "delete from `nq_quizs` where IDQuiz = '".$this->getIdQuiz()."'";
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('DELETED_FAILED');
		}
			
	}
	
	
	
	
	
	// quizdelete.php -> Delete participants
	function deleteParticipants()
	{
			
		$tblNameList = array(
			'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participants` par  `".$nameTables['nq_participants']."`	
		$sql = "delete from `nq_participants` where IDQuiz = '".$this->getIdQuiz()."'";
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('DELETED_FAILED');
		}
			
	}	
	
	
	
	
	
	// viewparticipantdetail.php -> Select Quiz name
	function selectQuizName()
	{
			
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`	
		$sql = "select QuizName from `nq_quizs` where IDQuiz = '".$this->getIdQuiz()."'";
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}

	
	
	
	
	// viewparticipantdetail.php -> Select infos participant
	function selectDetailsParticipant()
	{
			
		$tblNameList = array(
			'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participants` par  `".$nameTables['nq_participants']."`	
		$sql = "select Nom,Prenom,Groupe,Matricule,Courriel,Coordonnees, Final, UNIX_TIMESTAMP(ParticipationDate) as ParticipationDate, Actif from `nq_participants` where IDParticipant = '".$this->getIdParticipant ()."'";
		if ( false !== ($result = claro_sql_query_get_single_row($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
	
	
	
	
	
	// viewparticipantdetail.php -> Select Participants total Quiz
	function selectTotalQuiz()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."`
		$sql = "select sum(Ponderation) as PonderationTotal from `nq_questions` where IDQuiz = '".$this->getIdQuiz()."' and Active = 1";
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}

	
	
	
	
	// viewparticipantdetail.php -> Select Participants total score
	function selectTotalScore()
	{
			
		$tblNameList = array(
			'nq_participations', 'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participations` par  `".$nameTables['nq_participations']."` et `nq_questions` par  `".$nameTables['nq_questions']."`
		$sql = "select sum(Pointage) as PointageTotal from `nq_participations`, `nq_questions` where IDParticipant = '".$this->getIdParticipant ()."' and nq_participations.IDQuestion = nq_questions.IDQuestion and nq_questions.Active = 1";
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}

	
	
	
	
	// viewparticipantdetail.php -> Select Toutes les Participations
	function selectParticipationsList()
	{
			
		$tblNameList = array(
			'nq_participations', 'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participations` par  `".$nameTables['nq_participations']."` et `nq_questions` par  `".$nameTables['nq_questions']."`
		$sql = "select * from `nq_participations`, `nq_questions` where nq_participations.IDParticipant = '".$this->getIdParticipant ()."' and nq_participations.IDQuestion = nq_questions.IDQuestion";
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
	
	
	
	
	
	// viewquestiondetail.php -> Select Question détail
	function selectDetailsQuestion()
	{
			
		$tblNameList = array(
			'nq_questions', 'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."` et remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`
		$sql = "select * from `nq_questions`, `nq_quizs` where IDQuestion = '".$this->getIdQuestion()."' and nq_questions.IDQuiz = nq_quizs.IDQuiz";
		if ( false !== ($result = claro_sql_query_get_single_row($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}

	
	
	
	
	// viewquestiondetail.php -> Select Nombre de participant et moyenne
	function selectNumberParticipantAndMoyenne()
	{
			
		$tblNameList = array(
			'nq_participations', 'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participations` par  `".$nameTables['nq_participations']."` et remplacer `nq_participants` par  `".$nameTables['nq_participants']."`
		$sql = "select count(nq_participations.IDParticipant) as NbRepondants, avg(Pointage) as Moyenne from `nq_participations`, `nq_participants` where nq_participations.IDQuestion = '".$this->getIdQuestion()."' and nq_participations.IDParticipant = nq_participants.IDParticipant and nq_participants.Actif = 1";
		if ( false !== ($result = claro_sql_query_get_single_row($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}

	
	
	
	
	// viewquestiondetail.php -> Select Participations list
	function selectQuestionDetailParticipationsList()
	{
			
		$tblNameList = array(
			'nq_participations', 'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participations` par  `".$nameTables['nq_participations']."` et `nq_participants` par  `".$nameTables['nq_participants']."`
		$sql = "select *, UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDateUT from `nq_participations`, `nq_participants` where nq_participations.IDQuestion = '".$this->getIdQuestion()."' and nq_participations.IDParticipant = nq_participants.IDParticipant";
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
	
	
	
	
	
	// viewquizstats.php -> Info du quiz
	function selectQuizInfo()
	{
			
		$tblNameList = array(
			'nq_quizs', 'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_participations` par  `".$nameTables['nq_participations']."` et `nq_participants` par  `".$nameTables['nq_participants']."`
		$sql = "select QuizName, UNIX_TIMESTAMP(VersionDate) AS VersionDate, Password, Actif, sum(nq_questions.Ponderation) as PonderationTotal from `nq_quizs`, `nq_questions` where nq_quizs.IDQuiz = '".$this->getIdQuiz()."' and nq_quizs.IDQuiz = nq_questions.IDQuiz and nq_questions.Active = 1 group by nq_quizs.IDQuiz";
		if ( false !== ($result = claro_sql_query_get_single_row($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
	
	
	
	
	
	// viewquizstats.php -> Nombre de participations et premiere date
	function selectNumberParticipationsAndDate()
	{
		/*	
		$tblNameList = array(
			'nq_quizs', 'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		*/
		### Debug ###
		// il faut remplacer `nq_participations` par  `".$nameTables['nq_participations']."` et `nq_participants` par  `".$nameTables['nq_participants']."`
		$sql = "select UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDate, nq_participants.Final as Final, nq_participants.IDParticipant as IDParticipant, " .
                "sum(nq_participations.Pointage) as PointageTotal " .
                "from nq_participants " .
                "left join nq_participations using (IDParticipant) " .
                "right join nq_questions on nq_participations.IDQuestion = nq_questions.IDQuestion " .
                "where nq_questions.Active = 1 and " .
                "nq_participants.Actif = 1 and " .
                "nq_participants.IDQuiz = '".$this->getIdQuiz()."' " .
                "group by nq_participants.IDParticipant order by nq_participants.ParticipationDate desc";
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			$tmp = new QuizsStats($result);
			return $tmp;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
	
	
	
	
	
	// viewquizparticipations.php -> Info du quiz Participations
	function selectViewQuizInfo()
	{
			
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_quizs` par  `".$nameTables['nq_quizs']."`
		$sql = "select QuizName, UNIX_TIMESTAMP(VersionDate) as VersionDate, Password, Actif from `nq_quizs` where IDQuiz = '".$this->getIdQuiz()."'";
		if ( false !== ($result = claro_sql_query_get_single_row($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}

	
	
	
	
	// viewquizparticipations.php -> update du status des quizs
	// viewquizquestions.php -> update du status des quizs
	// viewquizstats.php -> update du status des quizs
	function updateQuizsStatus()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		
		### Debug ###
		// il faut remplacer `nq_questions` par  `".$nameTables['nq_questions']."`	
		
		$sql = "update `nq_quizs` set Actif = '".$this->getActif()."' where IDQuiz = '".$this->getIdQuiz()."'";
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('UPDATED_FAILED');
		}

	}		
	
}

?>