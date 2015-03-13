<?php // $Id$

/**
 * Moodle Resource Exporter
 *
 * @version     MOODLEEX 1.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2015 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     MOODLEEX
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$_lang[ 'Moodle resource exporter' ] = 'Export vers Moodle';
$_lang[ 'Excercises exporter' ] = 'Exporter les exercices';
$_lang[ 'Export failed' ] = 'L\'export à échoué';
$_lang[ 'Download all course\'s documents in a single zip file' ] = 'Exporter tous les documents du cours dans un fichier "zip"';

$_lang[ 'Items' ] = 'Eléments';
$_lang[ 'Videos' ] = 'Vidéos';
$_lang[ 'Documents and links' ] = 'Documents et liens';

$_lang[ 'Questions from quiz "%quizTitle"' ] = 'Questions du test intitulé "%quizTitle"';

$_lang[ '[Module introduction text] %warning' ] = '<span class="globalDesc">Pour l\'instant, le module d\'export permet la récupération des documents et liens, exercices et vidéos du cours. Nous continuons à améliorer cet outil pour qu\'il vous fournisse au minimum l\'archivage des données des autres outils.</span>';
$_lang[ '[Documents export] %warning' ] = 'Sur base de l\'archive des documents et liens fournie via le lien ci-dessous, vous pouvez réimporter vos documents avec leur structure dans MoodleUCL.<br /><a href="http://blogs.uclouvain.be/aideicampus/manuel-enseignant/migrer-vers-moodleucl/migrer-un-cours-vers-moodleucl#migrer-les-documents-et-liens">La migration des documents et liens en détail</a>.';
$_lang[ '[Exercices export] %warning' ] = 'Pour chaque exercice iCampus, le module d\'export vous fournit un fichier xml qui reprend toutes les questions de l\'exercice. Vous devez importer ce fichier dans la banque de questions de votre cours MoodleUCL. Les questions d\'un même exercice sont importées dans une nouvelle catégorie portant le nom de l\'exercice. Vous pouvez ainsi facilement recomposer vos tests MoodleUCL en sélectionnant les questions de la catégorie dédiée.<br /><a href="http://blogs.uclouvain.be/aideicampus/manuel-enseignant/migrer-vers-moodleucl/migrer-un-cours-vers-moodleucl/#migrer-les-exercices">La migration des exercices en détail</a>';
$_lang[ '[Videos export] %warning' ] = 'Dans MoodleUCL, nous vous conseillons plutôt d\'intégrer les vidéos une à une au fil des sections du cours. C\'est pourquoi le module d\'export vous propose les liens directs vers les vidéos individuelles de votre flux.<br /><a href="http://blogs.uclouvain.be/aideicampus/manuel-enseignant/migrer-vers-moodleucl/migrer-un-cours-vers-moodleucl/#migrer-les-videos-du-video-podcat-reader">La migration des vidéos en détail</a>';
$_lang[ '[Additionnal infos 1] %warning' ] = '<span class="infoTitle">Problème de migration ?</span> En cas de problème lors de la migration d\'un cours, contactez l\'adresse <a href="mailto://migration-icampus@uclouvain.be">migration-icampus@uclouvain.be</a>.';
$_lang[ '[Additionnal infos 2] %warning' ] = '<span class="infoTitle">Désactivation des cours migrés :</span>Si vous migrez un cours vers MoodleUCL, veillez à <a href="mailto://icampus@uclouvain.be">nous contacter</a> pour nous demander la désactivation de votre cours sur iCampus. Cela évitera les confusions auprès des étudiants! ;-)';