<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 1.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$checkList = array(
    'accountCreation' => array(
        'category' => 0,
        'profile' => 0,
        'description' => 'Je ne me suis jamais connecté sur iCampus et j\'aimerais y avoir accès',
        'mailTpl' => 'accountcreation' ),
    
    'firstAccessProblem' => array(
        'category' => 0,
        'profile' => 1,
        'description' => 'J\'ai activé mon compte global, pourtant je n\'arrive pas à entrer sur iCampus',
        'mailTpl' => 'accessproblem' ),
    
    'accessProblem' => array(
        'category' => 0,
        'profile' => 0,
        'description' => 'J\'ai déjà utilisé iCampus, mais je n\'arrive plus à m\' y connecter',
        'mailTpl' => 'accessproblem' ),
    
    'passwordLost' => array(
        'category' => 0,
        'profile' => 0,
        'description' => 'J\'ai perdu mon identifiant et/ou mon mot de passe',
        'mailTpl' => 'passwordlost' ),
    
    'alumni' => array(
        'category' => 0,
        'profile' => 0,
        'description' => 'Je suis un(e) ancien(ne) étudiant(e), et j\'aimerais conserver mon accès à iCampus',
        'mailTpl' => 'alumni' ),
    
    'msgNotReceived' => array(
        'category' => 1,
        'profile' => 4,
        'description' => 'Certains messages ne me parviennent pas',
        'mailTpl' => 'msgnotreceived' ),
    
    'msgNotSent' => array(
        'category' => 1,
        'profile' => 4,
        'description' => 'Je ne parviens pas à envoyer un message',
        'mailTpl' => 'msgnotsent' ),
    
    'msgNotDeleted' => array(
        'category' => 1,
        'profile' => 4,
        'description' => 'Je ne parviens pas à effacer un message qui se trouve dans la corbeille',
        'mailTpl' => 'msgnotdeleted' ),
    
    'courseCreate' => array(
        'category' => 2,
        'profile' => 3,
        'description' => 'J\'aimerais créer un cours, mais je ne sais pas comment faire',
        'mailTpl' => 'coursecreate' ),
    
    'creatorStatus' => array(
        'category' => 2,
        'profile' => 3,
        'description' => 'Je suis membre du personnel UCL et je souhaite obtenir le statut de créateur de cours',
        'mailTpl' => 'coursecreate' ),
    
    'managerChange' => array(
        'category' => 3,
        'profile' => 3,
        'description' => 'Un cours doit changer de titulaire',
        'mailTpl' => 'managerchange' ),
    
    'managerAdd' => array(
        'category' => 3,
        'profile' => 3,
        'description' => 'J\'aimerais ajouter un co-gestionnaire à mon cours',
        'mailTpl' => 'manageradd' ),
    
    'managerBecome' => array(
        'category' => 3,
        'profile' => 3,
        'description' => 'J\'aimerais devenir gestionnaire d\'un cours',
        'mailTpl' => 'managerbecome' ),
    
    'externalUser' => array(
        'category' => 3,
        'profile' => 3,
        'description' => 'J\'aimerais inscrire des utilisateurs externe à l\'UCL à mon cours (professeurs ou étudiants)',
        'mailTpl' => 'externaluser' ),
    
    'courseNotFound' => array(
        'category' => 3,
        'profile' => 4,
        'description' => 'Je n\'arrive pas à trouver le cours que je cherche',
        'mailTpl' => 'coursesearch' ),
    
    'keyRequired' => array(
        'category' => 3,
        'profile' => 4,
        'description' => 'Je n\'arrive pas à m\'inscrire à un cours car une clé m\'est demandée',
        'mailTpl' => 'keyrequired' ),
    
    'courseEnroll' => array(
        'category' => 3,
        'profile' => 4,
        'description' => 'Je n\'arrive pas à m\'inscrire à un cours pour une raison qui m\'est inconnue',
        'mailTpl' => 'courseenroll' ),
    
    'courseDisappeared' => array(
        'category' => 3,
        'profile' => 4,
        'description' => 'Des cours ont disparu de ma liste de cours',
        'mailTpl' => 'coursesearch' ),
    
    'moreSpaceNeeded' => array(
        'category' => 4,
        'profile' => 3,
        'description' => 'Il n\'y a plus assez d\'espace pour les documents de mon cours',
        'mailTpl' => 'spacerequest' ),
    
    'moreSpaceNeeded' => array(
        'category' => 4,
        'profile' => 3,
        'description' => 'Il n\'y a plus assez d\'espace pour les travaux',
        'mailTpl' => 'spacerequest' ),
    
    'moreSpaceNeeded' => array(
        'category' => 4,
        'profile' => 3,
        'description' => 'Il n\'y a plus assez d\'espace pour les groupes',
        'mailTpl' => 'spacerequest' ),
    
    'courseDelete' => array(
        'category' => 4,
        'profile' => 3,
        'description' => 'Je voudrais supprimer mon cours de la plateforme',
        'mailTpl' => 'courseDelete' ),
    
    'courseUnsubscribe' => array(
        'category' => 4,
        'profile' => 4,
        'description' => 'Je me suis inscrit(e) par erreur dans un groupe et je souhaite me désinscrire',
        'mailTpl' => 'courseunsubscribe' ),
    
    'subscriptionChange' => array(
        'category' => 4,
        'profile' => 4,
        'description' => 'Je souhaite modifier une inscription à une plage horaire',
        'mailTpl' => 'subscriptionchange' ),
    
    'workSubmit' => array(
        'category' => 4,
        'profile' => 4,
        'description' => 'Je ne parviens pas à remettre un travil sur iCampus',
        'mailTpl' => 'worksubmit' ),
    
    'useQuestion' => array(
        'category' => 99,
        'profile' => 4,
        'description' => 'Je me pose des questions au sujet de l\'utilisation de certains outils',
        'mailTpl' => null ),
    
    'podcastProblem' => array(
        'category' => 99,
        'profile' => 4,
        'description' => 'Les vidéos ne s\'affichent pas correctement',
        'mailTpl' => null ),
    
    'bugReport' => array(
        'category' => 99,
        'profile' => 4,
        'description' => 'Je voudrais vous faire part d\'un bug ou d\'un comportement étrange de l\'application',
        'mailTpl' => null ),
    
    'pedagogicalHelp' => array(
        'category' => 99,
        'profile' => 3,
        'description' => 'Je sollicite un accompagnement pédagogique',
        'mailTpl' => null ),
    
    'other' => array(
        'category' => 99,
        'profile' => 4,
        'description' => 'Aucune des propositions ci-dessus ne correspond à mon problème',
        'mailTpl' => null )
);

$profileList = array(
    0 => 'utilisateurs non authentifiés',
    1 => 'utilisateurs non authentifiés qui se déclarent membres de l\'UCL',
    2 => 'utilisateurs authentifiés',
    3 => 'utilisateurs (authentifiés ou non) qui se déclarent gestionnaire d\'un cours',
    4 => 'tous les utilisateurs'
);

$categoryList = array(
    0 => 'Problème d\'accès à iCampus',
    1 => 'Problème avec ma messagerie iCampus',
    2 => 'Problème lors de la création d\'un espace de cours',
    3 => 'Problème d\'inscription à un espace de cours',
    4 => 'Problème au sein d\'un espace de cours',
    99 => 'Autre problème'
);

$addedFields = array(
    4 => array(
        'type' => 'text',
        'label' => 'Entrez le code du cours concerné',
        'name' => 'courseCode',
        'required' => 0 ),
    99 => array(
        'type' => 'textarea',
        'label' => 'Décrivez votre problème',
        'name' => 'issueDescription',
        'required' => 1 )
);

$header = "Bonjour,\n\n";

$validator = "\n\nCe mail répond-il à votre question?\n"
    . "Si c'est le cas, merci de nous le signaler en cliquant sur ce lien :\n"
    . getModuleRoot( 'ICHELP' ) . '/validate.php?ticketId=' . $ticket->get( 'ticketId' )
    . "\n\n";
    
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