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

/* utilisateur concernés par les différentes "issueCategories":
  0 = utilisateurs non authentifiés
  1 = utilisateurs non authentifiés qui se déclarent membres de l'UCL
  2 = utilisateurs authentifiés
  3 = utilisateurs (authentifiés ou non) qui se déclarent gestionnaire d'un cours
  4 = tous les utilisateurs
  */
$checkList = array(
    'accountCreation' => array(
        'issueCategory' => 1,
        'description' => 'Je ne me suis jamais connecté sur iCampus et j\'aimerais y avoir accès.',
        'mailTpl' => 'accountcreation' ),
    
    'firstAccessProblem' => array(
        'issueCategory' => 1,
        'description' => 'J\'ai activé mon compte global, pourtant je n\'arrive pas à entrer sur iCampus',
        'mailTpl' => 'accessproblem' ),
    
    'alumni' => array(
        'issueCategory' => 0,
        'description' => 'Je suis un(e) ancien(ne) étudiant(e), et j\'aimerais conserver mon accès à iCampus',
        'mailTpl' => 'alumni' ),
    
    'accessProblem' => array(
        'issueCategory' => 0,
        'description' => 'J\'ai déjà utilisé iCampus, mais je n\'arrive plus à m\' y connecter',
        'mailTpl' => 'accessproblem' ),
    
    'passwordLost' => array(
        'issueCategory' => 0,
        'description' => 'J\'ai perdu mon mot de passe',
        'mailTpl' => 'passwordlost' ),
    
    'courseNotFound' => array(
        'issueCategory' => 2,
        'description' => 'Je n\'arrive pas à trouver le cours que je cherche',
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
        'description' => 'J\'aimerais créer un cours, mais je ne sais pas comment faire',
        'mailTpl' => 'coursecreate' ),
    
    'managerAdd' => array(
        'issueCategory' => 3,
        'description' => 'J\'aimerais ajouter un co-gestionnaire à mon cours',
        'mailTpl' => 'addmanager' ),
    
    'managerBecome' => array(
        'issueCategory' => 3,
        'description' => 'J\'aimerais devenir gestionnaire d\'un cours',
        'mailTpl' => 'managerbecome' ),
    
    'addExtUser' => array(
        'issueCategory' => 3,
        'description' => 'J\'aimerais inscrire à mon cours des utilisateurs extérieurs à l\'UCL',
        'mailTpl' => 'externaluser' ),
    
    'managerChange' => array(
        'issueCategory' => 3,
        'description' => 'Un cours doit changer de titulaire',
        'mailTpl' => 'managerchange' ),
    
    'bugReport' => array(
        'issueCategory' => 4,
        'description' => 'Je voudrais vous faire part d\'un bug ou d\'un comportement étrange de l\'application',
        'mailTpl' => null ),
    
    'podcastProblem' => array(
        'issueCategory' => 4,
        'description' => 'Les vidéos ne s\'affichent pas correctement',
        'mailTpl' => null ),
    
    'useQuestion' => array(
        'issueCategory' => 4,
        'description' => 'Je me pose des questions au sujet de l\'utilisation de certains outils',
        'mailTpl' => null ),
    
    'pedagogicalHelp' => array(
        'issueCategory' => 3,
        'description' => 'Je sollicite un accompagnement pédagogique',
        'mailTpl' => null ),
    
    'other' => array(
        'issueCategory' => 4,
        'description' => 'Autre...',
        'mailTpl' => null )
);

$header = "Bonjour,\n\n";

$footer = "\n\nBien à vous,\n"
    . "L'équipe iCampus\n\n"
    . "=================================================================\n"
    . "Attention : ceci est une première réponse envoyée automatiquement\n"
    . "par le système sur base des données que vous nous avez envoyées.\n"
    . "Une réponse personnalisée vous parviendra très prochainement.\n"
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