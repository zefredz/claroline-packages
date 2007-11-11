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
     * @version 1.9 $Revision: 183 $
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
		if( !empty($this->data) ) {
			$this->iFinal = $this->data[0]['Final'];
			if(intval($this->iFinal) == 1){
				$this->datelastparticipation =  date($GLOBALS['sDefaultDateHourFormat'],$this->data[0]['ParticipationDate']);
			}else{
				$this->datelastparticipation = "-";
			}
		}
		else
		{
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
	
	// authparticipant.php -> récupérer le dernier IDParticipant en DB
	function lastIdParticipant()
	{
			
		$tblNameList = array(
			'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql =   "select max(IDParticipant) as last_id from `".$nameTables['nq_participants']."`";
		
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
		$sql =   "select max(IDQuiz) as last_id from  `".$nameTables['nq_quizs']."`";
		
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
        
			$tblNameList = array(
				'nq_quizs'
			);
			
			$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
			$sql = "select IDQuiz from `".$nameTables['nq_quizs']."` where QuizIdent = '".$this->getQuizIdent()."' and QuizVersion = '".$this->getQuizVersion()."'";
			$result = claro_sql_query_get_single_value ($sql);
			
			if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
			{
				return $result;
			}
			else
			{				
				return claro_failure::set_failure('IDQUIZ_LOADED_FAILED');
			}
		}
	}
	
    // current user id
	function getCurrentUserid()
	{
    	return $this->currentuserid;
	}
	
	function setCurrentUserId($currentuserid)
	{
		$this->currentuserid = $currentuserid;
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
		$sql = "INSERT INTO `".$nameTables['nq_participants']."` (Prenom, Nom, Groupe, Matricule, Courriel, IDQuiz, currentUserId) VALUES (".toSQLString( $this->getPrenom(),false ).",".toSQLString( $this->getNom(),false ).",".toSQLString( $this->getGroupe(),false ).",".toSQLString( $this->getMatricule(),false ).",".toSQLString( $this->getCourriel(),false ).",".toSQLString( $this->getIdQuiz(),false ).",". $this->getCurrentUserid().");";

		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{
			return claro_failure::set_failure('PARTICIPANT_INSERTION_FAILED');
		}
		
	}

	// repquizid
	function getRepQuizId()
	{
    	return $this->repquizid;
	}
	
	function setRepQuizId($repquizid)
	{
		$this->repquizid = $repquizid;
	}
	
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
		$sql = "INSERT INTO `".$nameTables['nq_quizs']."` (RepQuizId, QuizIdent, QuizVersion, QuizName, NbQuestions, VersionDate, Password, Auteur, Actif) VALUES ('".addslashes( $this->getRepQuizId() )."','".addslashes( $this->getQuizIdent() )."','".addslashes( $this->getQuizVersion() )."','".addslashes( $this->getQuizName() )."','".addslashes( $this->getNbQuestions() )."',NOW(),'".addslashes( $this->getPassword() )."','".addslashes( $this->getAuteur() )."','".addslashes( $this->getActif() )."');";

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
		$sql = "INSERT INTO  `".$nameTables['nq_questions']."` (QuestionName, QuestionType, QuestionTypeTD, Ponderation, EnonceHTML, ReponseXML, IDQuiz, NoQuestion) VALUES ('".addslashes( $this->getTitre() )."', '".addslashes( $this->getType() )."', '".addslashes( $this->getTypeTd() )."', '".addslashes( $this->getPonderation() )."', '".addslashes( $this->getEnonce() )."', '".addslashes( $this->getReponseXML() )."', '".addslashes( $this->getIdQuiz() )."', '".addslashes( $this->getNoQuestion() )."');";

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
		$sql =   "select QuizIdent,QuizVersion from `".$nameTables['nq_quizs']."`";
		
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
		$sql =   "select IDQuiz, RepQuizId, QuizName, Actif from `".$nameTables['nq_quizs']."`";
		
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
	function selectQuizsListDate()
	{
			
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql = "select  IDQuiz, QuizName, UNIX_TIMESTAMP(VersionDate) AS TS_VersionDate from `".$nameTables['nq_quizs']."`";
		
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
		$sql = "select Ponderation from `".$nameTables['nq_questions']."` where IDQuestion = '".$this->getIdQuestion()."'";
		
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('LOADED_FAILED');
		}
			
	}	
	
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
		$sql = "update `".$nameTables['nq_participations']."` set Pointage = '".$this->getPointage()."' where IDQuestion = '".$this->getIdQuestion()."' and IDParticipant = '".$this->getIdParticipant()."'";
		
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('UPDATED_FAILED');
		}
			
	}	
	
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
		$sql = "update `".$nameTables['nq_questions']."` set Active = '".$this->getQuestionsActif()."' where IDQuestion = '".$this->getIdQuestion()."'";
		
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('UPDATED_FAILED');
		}

	}	

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
		$sql = "update  `".$nameTables['nq_participants']."` set Actif = '".$this->getParticipantsActif()."' where IDParticipant = '".$this->getIdParticipant()."'";

		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('UPDATED_FAILED');
		}
			
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

	// exportparticipations.php -> export des participations
	function selectParticipations()
	{
			
		$tblNameList = array(
			'nq_participants', 'nq_participations', 'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql =   "select nq_participants.IDParticipant, nq_participants.Prenom, nq_participants.Nom, nq_participants.Groupe, nq_participants.Final,  nq_participants.Courriel, " .
                "UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDate, sum(nq_participations.Pointage) as PointageTotal, nq_participants.Matricule, nq_participants.Actif " .
                "from `".$nameTables['nq_participants']."` AS nq_participants " .
                "left join `".$nameTables['nq_participations']."` AS nq_participations using (IDParticipant) " .
                "right join `".$nameTables['nq_questions']."` AS nq_questions on nq_participations.IDQuestion = nq_questions.IDQuestion " .
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

	// exportparticipations.php -> export des participations
	function selectParticipationsCurrentUser()
	{
			
		$tblNameList = array(
			'nq_participants', 'nq_participations', 'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql =   "select nq_participants.IDParticipant, nq_participants.Prenom, nq_participants.Nom, nq_participants.Groupe, nq_participants.Final,  nq_participants.Courriel, " .
                "UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDate, sum(nq_participations.Pointage) as PointageTotal, nq_participants.Matricule, nq_participants.Actif " .
                "from `".$nameTables['nq_participants']."` AS nq_participants " .
                "left join `".$nameTables['nq_participations']."` AS nq_participations using (IDParticipant) " .
                "right join `".$nameTables['nq_questions']."` AS nq_questions on nq_participations.IDQuestion = nq_questions.IDQuestion " .
                "where nq_questions.Active = 1 and " .
                "nq_participants.IDQuiz = '".$this->getIdQuiz()."' and " .
                "nq_participants.currentUserId = '".$this->getCurrentUserid()."' " .
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
	
	// exportparticipations.php -> Nom du quiz et date de version
	// exportquestions.php -> Nom du quiz et date de version
	function selectNameQuizAndDate()
	{
			
		$tblNameList = array(
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql = "select QuizName, UNIX_TIMESTAMP(VersionDate) as VersionDate from `".$nameTables['nq_quizs']."` where IDQuiz = '".$this->getIdQuiz()."'";
		
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}	

	// exportparticipations.php -> recuperation du nombre Total du quiz
	function selectPonderationTotal()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql = "select sum(Ponderation) as PonderationTotal from `".$nameTables['nq_questions']."` where IDQuiz = '".$this->getIdQuiz()."'";
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
	
	// exportquestions.php -> export des participations
	function selectQuestions()
	{
			
		$tblNameList = array(
			'nq_questions', 'nq_participations', 'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql =  "select nq_questions.NoQuestion, nq_questions.Ponderation, AVG(nq_participations.Pointage) as Average, " .
                "nq_questions.QuestionName , nq_questions.QuestionTypeTD, nq_questions.IDQuestion, nq_questions.Active " .
                "from `".$nameTables['nq_questions']."` AS nq_questions " .
                "left join `".$nameTables['nq_participations']."` AS nq_participations using (IDQuestion) " .
                "left join (select IDParticipant, Prenom, Nom, Groupe, Matricule, Courriel, Coordonnees, ParticipationDate, Final, IDQuiz, Actif from `".$nameTables['nq_participants']."` AS nq_participants where Actif = 1) nq_participants_actif on nq_participations.IDParticipant = nq_participants_actif.IDParticipant " .
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
    
	// exportquestions.php -> export des participations
	function selectQuestionsCurrentUser()
	{
			
		$tblNameList = array(
			'nq_questions', 'nq_participations', 'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql =  "select nq_questions.NoQuestion, nq_questions.Ponderation, AVG(nq_participations.Pointage) as Average, " .
                "nq_questions.QuestionName , nq_questions.QuestionTypeTD, nq_questions.IDQuestion, nq_questions.Active " .
                "from `".$nameTables['nq_questions']."` AS nq_questions " .
                "left join `".$nameTables['nq_participations']."` AS nq_participations using (IDQuestion) " .
                " join (select IDParticipant, Prenom, Nom, Groupe, Matricule, Courriel, Coordonnees, ParticipationDate, Final, IDQuiz, Actif from `".$nameTables['nq_participants']."` AS nq_participants where Actif = 1 and currentUserId = '".$this->getCurrentUserid()."') nq_participants_actif on nq_participations.IDParticipant = nq_participants_actif.IDParticipant " .
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
        $sql = "select IDQuestion from `".$nameTables['nq_questions']."` where IDQuiz = '".$this->getIdQuiz()."'";
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
        $sql = "select IDParticipant, IDQuestion, Pointage, PointageAuto, ReponseHTML from `".$nameTables['nq_participations']."` where IDQuestion = '".$this->getIdQuestion()."'";
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
		$sql = "delete from `".$nameTables['nq_participations']."` where IDParticipant = '".$this->getIdParticipant()."' and IDQuestion = '".$this->getIdQuestion()."'";
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
        $sql = "delete from  `".$nameTables['nq_questions']."` where IDQuestion = '".$this->getIdQuestion()."'";
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
        $sql = "delete from `".$nameTables['nq_quizs']."` where IDQuiz = '".$this->getIdQuiz()."'";
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
        $sql = "delete from `".$nameTables['nq_participants']."` where IDQuiz = '".$this->getIdQuiz()."'";
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
        $sql = "select QuizName from `".$nameTables['nq_quizs']."` where IDQuiz = '".$this->getIdQuiz()."'";
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
        $sql = "select Nom,Prenom,Groupe,Matricule,Courriel,Coordonnees, Final, UNIX_TIMESTAMP(ParticipationDate) as ParticipationDate, Actif from `".$nameTables['nq_participants']."` where IDParticipant = '".$this->getIdParticipant ()."'";
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
        $sql = "select sum(Ponderation) as PonderationTotal from `".$nameTables['nq_questions']."` where IDQuiz = '".$this->getIdQuiz()."' and Active = 1";
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
        $sql = "select sum(Pointage) as PointageTotal from `".$nameTables['nq_participations']."` AS nq_participations,  `".$nameTables['nq_questions']."` AS nq_questions where IDParticipant = '".$this->getIdParticipant ()."' and nq_participations.IDQuestion = nq_questions.IDQuestion and nq_questions.Active = 1";
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
        $sql = "select nq_questions.IDQuestion, nq_questions.NoQuestion, nq_questions.QuestionName, nq_questions.QuestionType, nq_questions.QuestionTypeTD, nq_questions.EnonceHTML, nq_questions.ReponseHTML, nq_questions.Ponderation, nq_questions.Active, nq_participations.IDParticipant, nq_participations.IDQuestion, nq_participations.Pointage, nq_participations.PointageAuto, nq_participations.ReponseHTML from  `".$nameTables['nq_participations']."` AS nq_participations, `".$nameTables['nq_questions']."` AS nq_questions where nq_participations.IDParticipant = '".$this->getIdParticipant ()."' and nq_participations.IDQuestion = nq_questions.IDQuestion";
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
        $sql = "select nq_questions.IDQuestion, nq_questions.QuestionName, nq_questions.QuestionType, nq_questions.QuestionTypeTD, nq_questions.Ponderation, nq_questions.EnonceHTML, nq_questions.ReponseHTML, nq_questions.ReponseXML, nq_questions.IDQuiz, nq_questions.NoQuestion, nq_questions.Active, nq_quizs.IDQuiz, nq_quizs.RepQuizId, nq_quizs.QuizIdent, nq_quizs.QuizVersion, nq_quizs.QuizName, nq_quizs.NbQuestions, nq_quizs.VersionDate, nq_quizs.Password, nq_quizs.Title, nq_quizs.Auteur, nq_quizs.Actif from `".$nameTables['nq_questions']."` AS nq_questions, `".$nameTables['nq_quizs']."` AS nq_quizs where IDQuestion = '".$this->getIdQuestion()."' and nq_questions.IDQuiz = nq_quizs.IDQuiz";
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
        $sql = "select count(nq_participations.IDParticipant) as NbRepondants, avg(Pointage) as Moyenne from `".$nameTables['nq_participations']."` AS nq_participations, `".$nameTables['nq_participants']."` AS nq_participants where nq_participations.IDQuestion = '".$this->getIdQuestion()."' and nq_participations.IDParticipant = nq_participants.IDParticipant and nq_participants.Actif = 1";
		if ( false !== ($result = claro_sql_query_get_single_row($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}

    // viewquestiondetail.php -> Select Nombre de participant et moyenne de l'utilisateur courant
	function selectNumberParticipantAndMoyenneCurrentUser()
	{
			
		$tblNameList = array(
			'nq_participations', 'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
        $sql = "select count(nq_participations.IDParticipant) as NbRepondants, avg(Pointage) as Moyenne from `".$nameTables['nq_participations']."` AS nq_participations, `".$nameTables['nq_participants']."` AS nq_participants where nq_participants.currentUserId = '".$this->getCurrentUserid()."' and nq_participations.IDQuestion = '".$this->getIdQuestion()."' and nq_participations.IDParticipant = nq_participants.IDParticipant and nq_participants.Actif = 1";
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
        $sql = "select nq_participations.IDParticipant, nq_participations.IDQuestion, nq_participations.Pointage, nq_participations.PointageAuto, nq_participations.ReponseHTML, nq_participants.IDParticipant, nq_participants.Prenom, nq_participants.Nom, nq_participants.Groupe, nq_participants.Matricule, nq_participants.Courriel, nq_participants.Coordonnees, nq_participants.ParticipationDate, nq_participants.Final, nq_participants.IDQuiz, nq_participants.Actif, UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDateUT from  `".$nameTables['nq_participations']."` AS nq_participations, `".$nameTables['nq_participants']."` AS nq_participants where nq_participations.IDQuestion = '".$this->getIdQuestion()."' and nq_participations.IDParticipant = nq_participants.IDParticipant";
		if ( false !== ($result = claro_sql_query_fetch_all_rows($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
    
	// viewquestiondetail.php -> Select Participations list
	function selectQuestionDetailParticipationsListCurrentUser()
	{
			
		$tblNameList = array(
			'nq_participations', 'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
        $sql = "select nq_participations.IDParticipant, nq_participations.IDQuestion, nq_participations.Pointage, nq_participations.PointageAuto, nq_participations.ReponseHTML, nq_participants.IDParticipant, nq_participants.Prenom, nq_participants.Nom, nq_participants.Groupe, nq_participants.Matricule, nq_participants.Courriel, nq_participants.Coordonnees, nq_participants.ParticipationDate, nq_participants.Final, nq_participants.IDQuiz, nq_participants.Actif, UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDateUT from  `".$nameTables['nq_participations']."` AS nq_participations, `".$nameTables['nq_participants']."` AS nq_participants where nq_participants.currentUserId = '".$this->getCurrentUserid()."' and nq_participations.IDQuestion = '".$this->getIdQuestion()."' and nq_participations.IDParticipant = nq_participants.IDParticipant";
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
        $sql = "select QuizName, UNIX_TIMESTAMP(VersionDate) AS VersionDate, Password, Actif, sum(nq_questions.Ponderation) as PonderationTotal from `".$nameTables['nq_quizs']."` AS nq_quizs,  `".$nameTables['nq_questions']."` AS nq_questions where nq_quizs.IDQuiz = '".$this->getIdQuiz()."' and nq_quizs.IDQuiz = nq_questions.IDQuiz and nq_questions.Active = 1 group by nq_quizs.IDQuiz";
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
		
		$tblNameList = array(
			'nq_participants', 'nq_participations', 'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
        $sql = "select UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDate, nq_participants.Final as Final, nq_participants.IDParticipant as IDParticipant, " .
                "sum(nq_participations.Pointage) as PointageTotal " .
                "from `".$nameTables['nq_participants']."` AS nq_participants " .
                "left join `".$nameTables['nq_participations']."` AS nq_participations using (IDParticipant) " .
                "right join `".$nameTables['nq_questions']."` AS nq_questions on nq_participations.IDQuestion = nq_questions.IDQuestion " .
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
	
    // viewquizstats.php -> Nombre de participations et premiere date pour l'utilisateur courant
	function selectNumberParticipationsAndDateCurrentUser()
	{
		
		$tblNameList = array(
			'nq_participants', 'nq_participations', 'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
        $sql = "select UNIX_TIMESTAMP(nq_participants.ParticipationDate) as ParticipationDate, nq_participants.Final as Final, nq_participants.IDParticipant as IDParticipant, " .
                "sum(nq_participations.Pointage) as PointageTotal " .
                "from `".$nameTables['nq_participants']."` AS nq_participants " .
                "left join `".$nameTables['nq_participations']."` AS nq_participations using (IDParticipant) " .
                "right join `".$nameTables['nq_questions']."` AS nq_questions on nq_participations.IDQuestion = nq_questions.IDQuestion " .
                "where nq_questions.Active = 1 and " .
                "nq_participants.Actif = 1 and " .
                "nq_participants.currentUserId = '".$this->getCurrentUserid()."' and " .
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
        $sql = "select QuizName, UNIX_TIMESTAMP(VersionDate) as VersionDate, Password, Actif from `".$nameTables['nq_quizs']."` where IDQuiz = '".$this->getIdQuiz()."'";
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
			'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql = "update `".$nameTables['nq_quizs']."` set Actif = '".$this->getActif()."' where IDQuiz = '".$this->getIdQuiz()."'";
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('UPDATED_FAILED');
		}

	}	

	// qvalidate.php -> Update Participant info if final soumission
	function updateParticipantsDate()
	{
			
		$tblNameList = array(
			'nq_participants'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
        $sql = "update `".$nameTables['nq_participants']."` set ParticipationDate = now(), Final = 1 where IDParticipant = '".$this->getIdParticipant()."'";
		
		if ( claro_sql_query($sql) )
		{
			return true;
		}
		else
		{				
			return claro_failure::set_failure('UPDATED_FAILED');
		}
			
	}
	
	// qvalidate.php -> Get the IDQuestion
	function selectOneIdQuestion()
	{
			
		$tblNameList = array(
			'nq_questions', 'nq_quizs'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql =   "select IDQuestion from `".$nameTables['nq_questions']."` AS nq_questions, `".$nameTables['nq_quizs']."` AS nq_quizs where nq_quizs.QuizIdent = '".$this->getQuizIdent()."' and nq_quizs.QuizVersion = '".$this->getQuizVersion()."' and nq_quizs.IDQuiz = nq_questions.IDQuiz and nq_questions.NoQuestion = '".$this->getNoQuestion()."'";
		
		if ( false !== ($result = claro_sql_query_get_single_value($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}
	
	// qvalidate.php -> Get ReponseXML and Ponderation
	function selectReponseXMLandPonderation()
	{
			
		$tblNameList = array(
			'nq_questions'
		);
		
		$nameTables = get_module_course_tbl($tblNameList, claro_get_current_course_id());
		$sql = "select ReponseXML, Ponderation from `".$nameTables['nq_questions']."` where IDQuestion = '".$this->getIdQuestion()."'";
		
		if ( false !== ($result = claro_sql_query_get_single_row($sql)) )
		{
			return $result;
		}
		else
		{				
			return claro_failure::set_failure('SELECTED_FAILED');
		}
			
	}

}

?>