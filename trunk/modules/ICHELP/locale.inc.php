<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.4 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

/* utilisateur concern�s par les diff�rentes "issueCategories":
  0 = utilisateurs non authentifi�s
  1 = utilisateurs non authentifi�s qui se d�clarent membres de l'UCL
  2 = utilisateurs authentifi�s
  3 = utilisateurs (authentifi�s ou non) qui se d�clarent gestionnaire d'un cours
  4 = tous les utilisateurs
  */
$checkList = array(
    'accountCreation' => array(
        'issueCategory' => 1,
        'description' => 'Je ne me suis jamais connect� sur iCampus et j\'aimerais y avoir acc�s.',
        'mailTpl' => 'accountcreation' ),
    
    'firstAccessProblem' => array(
        'issueCategory' => 1,
        'description' => 'J\'ai activ� mon compte global, pourtant je n\'arrive pas � entrer sur iCampus',
        'mailTpl' => 'accessproblem' ),
    
    'alumni' => array(
        'issueCategory' => 0,
        'description' => 'Je suis un(e) ancien(ne) �tudiant(e), et j\'aimerais conserver mon acc�s � iCampus',
        'mailTpl' => 'alumni' ),
    
    'accessProblem' => array(
        'issueCategory' => 0,
        'description' => 'J\'ai d�j� utilis� iCampus, mais je n\'arrive plus � m\' y connecter',
        'mailTpl' => 'accessproblem' ),
    
    'passwordLost' => array(
        'issueCategory' => 0,
        'description' => 'J\'ai perdu mon mot de passe',
        'mailTpl' => 'passwordlost' ),
    
    'courseNotFound' => array(
        'issueCategory' => 2,
        'description' => 'Je n\'arrive pas � trouver le cours que je cherche',
        'mailTpl' => 'coursesearch' ),
    
    'courseDisappeared' => array(
        'issueCategory' => 2,
        'description' => 'Des cours ont disparu de ma liste de cours',
        'mailTpl' => 'coursesearch' ),
    
    'moreSpaceNeeded' => array(
        'issueCategory' => 3,
        'description' => 'Il n\'y a plus assez d\'espace pour les documents de mon cours',
        'mailTpl' => 'spacerequest' ),
    
    'creatorStatus' => array(
        'issueCategory' => 3,
        'description' => 'J\'aimerais cr�er un cours, mais je ne sais pas comment faire',
        'mailTpl' => 'coursecreate' ),
    
    'managerAdd' => array(
        'issueCategory' => 3,
        'description' => 'J\'aimerais ajouter un co-gestionnaire � mon cours',
        'mailTpl' => 'addmanager' ),
    
    'managerBecome' => array(
        'issueCategory' => 3,
        'description' => 'J\'aimerais devenir gestionnaire d\'un cours',
        'mailTpl' => 'managerbecome' ),
    
    'addExtUser' => array(
        'issueCategory' => 3,
        'description' => 'J\'aimerais inscrire � mon cours des utilisateurs ext�rieurs � l\'UCL',
        'mailTpl' => 'externaluser' ),
    
    'managerChange' => array(
        'issueCategory' => 3,
        'description' => 'Un cours doit changer de titulaire',
        'mailTpl' => 'managerchange' ),
    
    'bugReport' => array(
        'issueCategory' => 4,
        'description' => 'Je voudrais vous faire part d\'un bug ou d\'un comportement �trange de l\'application',
        'mailTpl' => null ),
    
    'podcastProblem' => array(
        'issueCategory' => 4,
        'description' => 'Les vid�os ne s\'affichent pas correctement',
        'mailTpl' => null ),
    
    'useQuestion' => array(
        'issueCategory' => 4,
        'description' => 'Je me pose des questions au sujet de l\'utilisation de certains outils',
        'mailTpl' => null ),
    
    'pedagogicalHelp' => array(
        'issueCategory' => 3,
        'description' => 'Je sollicite un accompagnement p�dagogique',
        'mailTpl' => null ),
    
    'other' => array(
        'issueCategory' => 4,
        'description' => 'Autre...',
        'mailTpl' => null )
);

$header = "Bonjour,\n\n";

$footer = "\n\nBien � vous,\n"
    . "L'�quipe iCampus\n\n"
    . "=================================================================\n"
    . "Attention : ceci est une premi�re r�ponse envoy�e automatiquement\n"
    . "par le syst�me sur base des donn�es que vous nous avez envoy�es.\n"
    . "Une r�ponse personnalis�e vous parviendra tr�s prochainement.\n"
    . "=================================================================\n\n"
    . "--\n"
    . "Consultez aussi notre manuel iCampus en ligne !\n"
    . "http://blogs.uclouvain.be/aideicampus/\n\n"
    . "------------- iCampus ----------------\n"
    . "mail: icampus@uclouvain.be\n"
    . "UCL-IPM, 54 Grand-rue, bte L1.06.01,\n"
    . "1348 Louvain-la Neuve, Belgique\n"
    . "http://www.uclouvain.be/ipm\n"
    . "http://icampus.uclouvain.be\n"
    . "--------------------------------------";

$defaultUserData = array(
    'userId' => null,
    'firstName' => null,
    'lastName' => null,
    'mail' => null,
    'username' => null,
    'officialCode' => null,
    'jsEnabled' => null );