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

$checkList = array(
    'accountCreation' => array(
        'issueCategory' => 0,
        'description' => 'Je ne me suis jamais connecté sur iCampus et je ne sais pas comment entrer',
        'mailTpl' => '' ),
    
    'firstAccessProblem' => array(
        'issueCategory' => 0,
        'description' => 'J\'ai activé mon compte global, pourtant je n\'arrive pas à entrer sur iCampus',
        'mailTpl' => '' ),
    
    'accessProblem' => array(
        'issueCategory' => 1,
        'description' => 'J\'ai déjà un compte sur iCampus, mais je n\'arrive plus à me connecter',
        'mailTpl' => '' ),
    
    'courseNotFound' => array(
        'issueCategory' => 1,
        'description' => 'Je n\'arrive pas à trouver le cours que je cherche',
        'mailTpl' => '' ),
    
    'courseDisappeared' => array(
        'issueCategory' => 1,
        'description' => 'Des cours ont disparus de ma liste de cours',
        'mailTpl' => '' ),
    
    'moreSpaceNeeded' => array(
        'issueCategory' => 2,
        'description' => 'Il n\'y a plus assez d\'espace pour les documents de mon cours',
        'mailTpl' => '' ),
    
    'creatorStatus' => array(
        'issueCategory' => 2,
        'description' => 'J\'aimerais créer un cours, mais je ne sais pas comment faire',
        'mailTpl' => '' ),
    
    'managerAdd' => array(
        'issueCategory' => 2,
        'description' => 'J\'aimerais ajouter un co-gestionnaire à mon cours',
        'mailTpl' => '' ),
    
    'managerBecome' => array(
        'issueCategory' => 2,
        'description' => 'J\'aimerais devenir gestionnaire d\'un cours',
        'mailTpl' => '' ),
    
    'addExtUser' => array(
        'issueCategory' => 2,
        'description' => 'J\'aimerais inscrire à mon cours des utilisateurs extérieurs à l\'UCL',
        'mailTpl' => '' ),
    
    'managerChange' => array(
        'issueCategory' => 2,
        'description' => 'Un cours doit changer de titulaire',
        'mailTpl' => '' ),
    
    'bugReport' => array(
        'issueCategory' => 1,
        'description' => 'Je voudrais vous faire part d\'un bug ou d\'un comportement étrange de l\'application',
        'mailTpl' => '' ),
    
    'podcastProblem' => array(
        'issueCategory' => 1,
        'description' => 'Les vidéos ne s\'affichent pas correctement',
        'mailTpl' => '' ),
    
    'useQuestion' => array(
        'issueCategory' => 1,
        'description' => 'Je me pose des questions au sujet de l\'utilisation de certains outils',
        'mailTpl' => null ),
    
    'pedagogicalHelp' => array(
        'issueCategory' => 2,
        'description' => 'Je sollicite un accompagnement pédagogique',
        'mailTpl' => null ),
    
    'other' => array(
        'issueCategory' => 1,
        'description' => 'Autre...',
        'mailTpl' => null )
);

$header = "=================================================================\n"
    . "Attention : ceci est une première réponse envoyée automatiquement\n"
    . "par le système sur base des données que vous nous avez envoyées.\n"
    . "=================================================================\n\n"
    . "Bonjour,\n\n";

$footer = "Bien à vous,\n"
    . "L'équipe iCampus\n\n"
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