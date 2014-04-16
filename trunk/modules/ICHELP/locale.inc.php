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
        'description' => 'Je ne me suis jamais connect� sur iCampus et j\'aimerais y avoir acc�s',
        'mailTpl' => 'accountcreation' ),
    
    'firstAccessProblem' => array(
        'category' => 0,
        'profile' => 1,
        'description' => 'J\'ai activ� mon compte global, pourtant je n\'arrive pas � entrer sur iCampus',
        'mailTpl' => 'accessproblem' ),
    
    'accessProblem' => array(
        'category' => 0,
        'profile' => 0,
        'description' => 'J\'ai d�j� utilis� iCampus, mais je n\'arrive plus � m\' y connecter',
        'mailTpl' => 'accessproblem' ),
    
    'passwordLost' => array(
        'category' => 0,
        'profile' => 0,
        'description' => 'J\'ai perdu mon identifiant et/ou mon mot de passe',
        'mailTpl' => 'passwordlost' ),
    
    'alumni' => array(
        'category' => 0,
        'profile' => 0,
        'description' => 'Je suis un(e) ancien(ne) �tudiant(e), et j\'aimerais conserver mon acc�s � iCampus',
        'mailTpl' => 'alumni' ),
    
    'msgNotReceived' => array(
        'category' => 1,
        'profile' => 4,
        'description' => 'Certains messages ne me parviennent pas',
        'mailTpl' => 'msgnotreceived' ),
    
    'msgNotSent' => array(
        'category' => 1,
        'profile' => 4,
        'description' => 'Je ne parviens pas � envoyer un message',
        'mailTpl' => 'msgnotsent' ),
    
    'msgNotDeleted' => array(
        'category' => 1,
        'profile' => 4,
        'description' => 'Je ne parviens pas � effacer un message qui se trouve dans la corbeille',
        'mailTpl' => 'msgnotdeleted' ),
    
    'courseCreate' => array(
        'category' => 2,
        'profile' => 3,
        'description' => 'J\'aimerais cr�er un cours, mais je ne sais pas comment faire',
        'mailTpl' => 'coursecreate' ),
    
    'creatorStatus' => array(
        'category' => 2,
        'profile' => 3,
        'description' => 'Je suis membre du personnel UCL et je souhaite obtenir le statut de cr�ateur de cours',
        'mailTpl' => 'coursecreate' ),
    
    'managerChange' => array(
        'category' => 3,
        'profile' => 3,
        'description' => 'Un cours doit changer de titulaire',
        'mailTpl' => 'managerchange' ),
    
    'managerAdd' => array(
        'category' => 3,
        'profile' => 3,
        'description' => 'J\'aimerais ajouter un co-gestionnaire � mon cours',
        'mailTpl' => 'manageradd' ),
    
    'managerBecome' => array(
        'category' => 3,
        'profile' => 3,
        'description' => 'J\'aimerais devenir gestionnaire d\'un cours',
        'mailTpl' => 'managerbecome' ),
    
    'externalUser' => array(
        'category' => 3,
        'profile' => 3,
        'description' => 'J\'aimerais inscrire des utilisateurs externe � l\'UCL � mon cours (professeurs ou �tudiants)',
        'mailTpl' => 'externaluser' ),
    
    'courseNotFound' => array(
        'category' => 3,
        'profile' => 4,
        'description' => 'Je n\'arrive pas � trouver le cours que je cherche',
        'mailTpl' => 'coursesearch' ),
    
    'keyRequired' => array(
        'category' => 3,
        'profile' => 4,
        'description' => 'Je n\'arrive pas � m\'inscrire � un cours car une cl� m\'est demand�e',
        'mailTpl' => 'keyrequired' ),
    
    'courseEnroll' => array(
        'category' => 3,
        'profile' => 4,
        'description' => 'Je n\'arrive pas � m\'inscrire � un cours pour une raison qui m\'est inconnue',
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
        'description' => 'Je me suis inscrit(e) par erreur dans un groupe et je souhaite me d�sinscrire',
        'mailTpl' => 'courseunsubscribe' ),
    
    'subscriptionChange' => array(
        'category' => 4,
        'profile' => 4,
        'description' => 'Je souhaite modifier une inscription � une plage horaire',
        'mailTpl' => 'subscriptionchange' ),
    
    'workSubmit' => array(
        'category' => 4,
        'profile' => 4,
        'description' => 'Je ne parviens pas � remettre un travil sur iCampus',
        'mailTpl' => 'worksubmit' ),
    
    'useQuestion' => array(
        'category' => 99,
        'profile' => 4,
        'description' => 'Je me pose des questions au sujet de l\'utilisation de certains outils',
        'mailTpl' => null ),
    
    'podcastProblem' => array(
        'category' => 99,
        'profile' => 4,
        'description' => 'Les vid�os ne s\'affichent pas correctement',
        'mailTpl' => null ),
    
    'bugReport' => array(
        'category' => 99,
        'profile' => 4,
        'description' => 'Je voudrais vous faire part d\'un bug ou d\'un comportement �trange de l\'application',
        'mailTpl' => null ),
    
    'pedagogicalHelp' => array(
        'category' => 99,
        'profile' => 3,
        'description' => 'Je sollicite un accompagnement p�dagogique',
        'mailTpl' => null ),
    
    'other' => array(
        'category' => 99,
        'profile' => 4,
        'description' => 'Aucune des propositions ci-dessus ne correspond � mon probl�me',
        'mailTpl' => null )
);

$profileList = array(
    0 => 'utilisateurs non authentifi�s',
    1 => 'utilisateurs non authentifi�s qui se d�clarent membres de l\'UCL',
    2 => 'utilisateurs authentifi�s',
    3 => 'utilisateurs (authentifi�s ou non) qui se d�clarent gestionnaire d\'un cours',
    4 => 'tous les utilisateurs'
);

$categoryList = array(
    0 => 'Probl�me d\'acc�s � iCampus',
    1 => 'Probl�me avec ma messagerie iCampus',
    2 => 'Probl�me lors de la cr�ation d\'un espace de cours',
    3 => 'Probl�me d\'inscription � un espace de cours',
    4 => 'Probl�me au sein d\'un espace de cours',
    99 => 'Autre probl�me'
);

$addedFields = array(
    4 => array(
        'type' => 'text',
        'label' => 'Entrez le code du cours concern�',
        'name' => 'courseCode',
        'required' => 0 ),
    99 => array(
        'type' => 'textarea',
        'label' => 'D�crivez votre probl�me',
        'name' => 'issueDescription',
        'required' => 1 )
);

$header = "Bonjour,\n\n";

$validator = "\n\nCe mail r�pond-il � votre question?\n"
    . "Si c'est le cas, merci de nous le signaler en cliquant sur ce lien :\n"
    . getModuleRoot( 'ICHELP' ) . '/validate.php?ticketId=' . $ticket->get( 'ticketId' )
    . "\n\n";
    
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