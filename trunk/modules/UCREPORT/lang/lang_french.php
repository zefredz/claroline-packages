<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$_lang['Action failed'] = 'L\'opération a échoué';
$_lang['Activate'] = 'Activer';
$_lang['Active / inactive'] = 'Actif / inactif';
$_lang['Actualize'] = 'Actualiser';
$_lang['An error occured: the report has not been created!'] = 'Une erreur est survenue: le bulletin n\' a pu être créé!';
$_lang['An error occured: the report has not been deleted!'] = 'Une erreur est survenue: le bulletin n\' a  pu être supprimé!';
$_lang['Average'] = 'Moyenne';
$_lang['Back to the examination list'] = 'retour à la liste des examens';
$_lang['Back to the report list'] = 'Retour à la liste des bulletins';
$_lang['Choose a title'] = 'Choisissez un titre';
$_lang['Comment for'] = 'Commentaire pour';
$_lang['Comments'] = 'Commentaires';
$_lang['Comments without marks has been ignored!'] = 'Les commentaires sans notes ont été ignorés';
$_lang['Create a new report'] = 'Créer un nouveau bulletin';
$_lang['Create a new session'] = 'Créer un nouvel examen';
$_lang['Course managers have the right to publish reports where students can see each other scores?'] = 'Les gestionnaires de cours ont-ils le droit de permettre aux étudiants de voir leurs notes respectives?';
$_lang['Delete the examination?'] = 'Supprimer cet examen?';
$_lang['Do you really want to delete this report?'] = 'Etes-vous sûr de vouloir supprimer ce bulletin?';
$_lang['Manage plugins'] = 'Gérer les plugins';
$_lang['empty'] = 'vide';
$_lang['Error'] = 'Erreur';
$_lang['Examination'] = 'Examen';
$_lang['Examinations'] = 'Notes d\'examen';
$_lang['Examination list'] = 'Liste des examens';
$_lang['Examination Report']= 'Notes d\'Examen';
$_lang['Export to csv'] = 'Exporter vers csv';
$_lang['Export to MS-Excel xlsx file'] = 'Exporter vers MS-Excel xlm ( *.xlsx)';
$_lang['Export to pdf'] = 'Exporter en pdf';
$_lang['Gathering datas'] = 'Récupération des données';
$_lang['inactive'] = 'inactif';
$_lang['Items selection'] = 'Sélection des items à importer';
$_lang['Learningpath'] = 'Parcours pédagogique';
$_lang['Mark'] = 'Note';
$_lang['Max value'] = 'Note maximale';
$_lang['My examination results and comments'] = 'Les résultats commentés de mes examens';
$_lang['My report'] = 'Mon bulletin';
$_lang['No report available'] = 'Aucun bulletin disponible';
$_lang['No result at this time'] = 'Pas de résultats pour le moment';
$_lang['No session for this course yet'] = 'Aucune session d\'examen définie pour l\'instant';
$_lang['Number of marks'] = 'Nombre de notes attribuées';
$_lang['Plugin'] = 'Plugin';
$_lang['Plugin active : click to desactivate'] = 'Plugin actif : cliquez pour désactiver';
$_lang['Plugin inactive : click to activate'] = 'Plugin inactif : cliquez pour activer';
$_lang['Plugin management'] = 'Gestion des plugins';
$_lang['Private : click to open'] = 'Privé : cliquez pour rendre public';
$_lang['Public / private'] = 'Public / privé';
$_lang['Public : click to close'] = 'Public : cliquez pour rendre privé';
$_lang['Public report allowed'] = 'Autorisation des notes publiques';
$_lang['Publication date'] = 'Date de publication';
$_lang['Publish the report'] = 'Publier le bulletin';
$_lang['Report'] = 'Bulletin';
$_lang['Report list'] = 'Liste des bulletins';
$_lang['Reset'] = 'Rétablir';
$_lang['Reset scores'] = 'Tout remettre à zéro';
$_lang['Reset the examination?'] = 'Réinitialiser cet examen?';
$_lang['See my examination result details'] = 'Voir les commentaires de mes résultats';
$_lang['Select'] = 'Sélectionner';
$_lang['Session'] = 'Session';
$_lang['Session list'] = 'Liste des sessions';
$_lang['Student Report'] = 'Bulletin';
$_lang['Student\'s name'] = 'Nom de l\'étudiant';
$_lang['Success'] = 'L\'opération s\'est déroulée avec succès';
$_lang['The changes has been recorded'] = 'Les changements ont été éffectués avec succès';
$_lang['The examination %title has been created'] = 'L\'examen "title" a été crée';
$_lang['The examination has been reseted'] = 'Les notes ont étés réinitialisées avec succès';
$_lang['The report has been successfully created!'] = 'Le bulletin a été créé avec succès!';
$_lang['The report has beeen successfully deleted!'] = 'Le bulletin a été supprimé!';
$_lang['To import from'] = 'A importer de l\'outil';
$_lang['Weight'] = 'Pondération';
$_lang['weight'] = 'pondération';
$_lang['Weighted global score'] = 'Moyenne globale pondérée';
$_lang['Work'] = 'Travaux';
$_lang['wt.'] = 'pond.';
$_lang['You are not a course member'] = 'Vous n\'êtes pas inscrit à ce cours';
$_lang['You don\'t have score in this report'] = 'Vous n\'avez pas de note pour ce bulletin';
$_lang['You have no mark yet for this session'] = 'Vous n\'avez pas encore de note pour cet examen';
$_lang['You must give a score to add a comment'] = 'Vous devez d\'abord assigner une note avant d\'ajouter un commentaire';

// HELP FILE
$_lang['blockReportHelp'] = '<h1>Outil bulletin : manuel de l\'enseignant</h1>
<strong>Note:</strong> Les captures d\'écran accompagnant ce manuel en ligne sont en anglais.<br />
Chaque référence à une commande de l\'interface sera indiquée en <strong>gras</strong> et en français, suivie de sa traduction correspondante en anglais (telle qu\'elle apparaît sur les captures) en <em>[italique et entre crochets]</em>.

<h2>Introduction</h2>
L\'objet de l\'outil "Bulletin" est :
<ul>
    <li>d\'encoder des notes d\'examen et de les communiquer à vos étudiants, éventuellement accompagnés de commentaires</li>
    <li>d\'agréger les notes en provenance des autres outils de Claroline, de leur assigner des pondérations, et de calculer automatiquement les résultats et les moyennes.</li>
    <li>d\'exporter ces résultats sous différents formats (xlxs, csv, pdf)
    <li>de les communiquer à vos étudiants sous la forme de bulletins accessibles via leurs bureaux</li>
</ul>

<h2>Gestion des plugins</h2>
L\'import des résultats s\'effectue au travers d\'un système de plugins.<br />
Par défaut, l\'importation vers les outils suivants est disponible: Travaux, Exercices, Parcours pédagogique et ... Examen.<br />
Si certains imports ne vous intéresse pas, ils sont toutefois désactivables, afin de ne pas encombrer inutilement l\'interface.<br /><br />
On accède à la gestion des plugins en cliquant sur le bouton <strong>Gérer les plugins</strong><em>[Plugin management]</em> dans la page d\'accueil de l\'outil.<br />
Les plugins peuvent être activés ou désactivés en cliquant simplement sur la petite icône représentant une pièce de puzzle.<br />
<img src="../../module/UCREPORT/img/help/plugin_manage.png" alt="interface de gestion des plugins" /><br />
L\'icône est jaune lorsque le plugin est activé, grise dans le cas contraire.

<h2>Notes d\'examen</h2>
Si vous désirer introduire des notes d\'examen dans vos bulletins, il faudra préalablement les introduire dans la section dédiée au sein de l\'outil.<br />
Il s\'agit en fait d\'un outil à part entière, intégré dans "Bulletin".<br /><br />
Pour y accéder, cliquez sur le bouton <strong>Notes d\'examen</strong><em>[Examinations]</em> présent sur la page d\'accueil de l\'outil.<br />
<img src="../../module/UCREPORT/img/help/tool_entry.png" alt="page d\'accueil de l\'outil Bulletin" />

<h3>Créer un nouvel examen</h3>
La première étape consiste à créer une "session" d\'examen en cliquant sur le bouton <strong>Créer un nouvel examen</strong><em>[Create a new session]</em>.<br />
Un formulaire apparaîtra alors vous demandant de lui donner un nom, et de fixer sa note maximale. Par défaut, celle-ci vaut 20.<br />
<img src="../../module/UCREPORT/img/help/exam_create.png" alt="examen créé" />

<h3>Encodage des notes</h3>
Une fois le formulaire validé, un message vous indique - si tout s\'est bien passé - que votre bulletin a été créé.<br />
Vous êtes alors redirigé vers la page vous permettant d\'encoder les notes de votre examen.<br />
<img src="../../module/UCREPORT/img/help/exam_created.png" alt="formulaire d\'édition d\'un examen" /><br />
Pour chaque membre de votre cours, vous pouvez attribuer une note et ajouter un éventuel commentaire.<br />
<img src="../../module/UCREPORT/img/help/exam_edit.png" alt="formulaire d\'édition d\'un examen" /><br />
Cliquez sur <strong>OK</strong> pour validez vos notes.<br />
Vous restez toutefois sur la même page, car contrairement à un bulletin (qui une fois publié ne peut plus être modifié), il vous est loisible de changer les notes d\'un examen à tout moment.<br /><br />
Si vous revenez à la liste des examens, vous verrez que votre "session" nouvellement créée y figure bien.<br />
<img src="../../module/UCREPORT/img/help/exam_list.png" alt="liste des examens" /><br />
A partir de cette liste, vous pouvez, comme dans la plupart des outils de Claroline, modifier la visibilité de cet élément... encore le supprimer.

<h3>Accès des étudiants à leurs notes</h3>
Vos étudiants auront accès à ses notes via l\'outil "Bulletin" de votre cours en cliquant sur <strong>Notes d\'examen</strong><em>[Examinations]</em> de l\'outil.<br />
Il n\'aura toutefois pas accès aux notes de ses condisciples dans cette page.<br />
<img src="../../module/UCREPORT/img/help/exam_student.png" alt="examen: vue étudiant" />

<h2>Créer un bulletin</h2>
La création d\'un bulletin s\'effectue en plusieurs étapes.<br />
Commencez par cliquer sur <strong>Créer un nouveau bulletin</strong><em>[Create a new report]</em>.

<h3>Etape 1 : Sélection des items et encodage des pondérations</h3>
La première étape consiste à sélectionner les items pertinents pour votre bulletin et leur assigner une pondération.<br />
La page qui s\'affiche alors dresse la liste de tous les items détéctés par le système.<br />
<img src="../../module/UCREPORT/img/help/result_import.png" alt="interface de sélection des items à importer" /><br />
Vous sélectionnez les items à importer en cochant leurs cases correspondantes dans la colonne <strong>Sélectionner</strong><em>[Select]</em>.<br />
Vous remarquerez que par défaut, certains sont déjà sélectionnés. L\'outil se base en effet sur leur visibilité pour opérer une présélection.<br />
La colonne <strong>Pondération</strong><em>[Weight]</em> vous permet de définir la pondération de chaque élément.<br /><br />
Par défaut, la pondération de chaque item est de 100.<br />
Introduisez la valeur numérique de votre choix dans chacun de ces champs.<br />
Le système calculera les pondérations proportionnellement à ces valeurs.<br /><br />
<em>Par exemple,<br />
&nbsp;&nbsp;&nbsp;&nbsp;dans le cas de quatre items pour lesquels vous introduisez ces valeurs: 50, 200, 100 et 150,<br />
&nbsp;&nbsp;&nbsp;&nbsp;vous obtiendrez respectivement les pondérations suivantes: 10%, 40%, 20% et 30%.</em><br /><br />
Une fois cela fait, validez en cliquant sur <strong>Importer</strong><em>[Import]</em>.

<h3>Etape 2 : Sélection des étudiants et ajustement des notes</h3>
La page suivante affiche alors les résultats de chaque étudiant, ainsi que sa moyenne, en tenant compte des pondérations choisies.<br />
<img src="../../module/UCREPORT/img/help/result_edit.png" alt="interface d\'édition du bulletin" /><br />
Les moyennes ne sont calculées que pour les étudiants possédant une note dans TOUS les items.<br />
Les étudiants ayant des notes manquantes sont dits "désactivés": cela veut dire qu\'ils ne figureront pas dans le bulletin une fois publié.<br />
Ceux-ci sont facilement identifiables, car pour eux:<br />
&nbsp;&nbsp;&nbsp;&nbsp;d\'une part le "petit oeil" dans la colonne <strong>Activer</strong><em>[Activate]</em> est fermé,<br />
&nbsp;&nbsp;&nbsp;&nbsp;d\'autre part, la colonne des résultats finaux indique <strong>inactif</strong><em>[inactive]</em> en grisé<br /><br />
Un étudiant peut être activé ou désactivé en cliquant sur le petit oeil correspondant.<br /><br />
Lors de l\'activation, les note manquantes sont converties en 0 (zéro).<br />
A chaque changement, les moyennes sont automatiquement recalculées.<br /><br />
A ce stade, vous avez la possibilité de modifier les notes.<br />
Par exemple, pour attribuer une note à un étudiant ayant fournit un travail en retard et qui, aux yeux du système, ne possède pas de résultat.<br /><br />
Vos modifications ne seront prises en compte qu\'après avoir cliqué sur <strong>Actualiser</strong><em>[Actualize]</em><br /><br />
Vous pouvez également exporter les données qui s\'affichent dans trois formats différents: MS-Excel 2007 (*.xlsx), CSV et PDF.

<h3>Etape 3 : Publication du bulletin</h3>
Une fois toutes les notes attribuées, vous pouvez maintenant "publier" le bulletin en cliquant sur <strong>Publier le bulletin</strong><em>[Publish the report]</em>.<br />
Un formulaire apparaît alors, vous demandant de lui choisir un titre:<br />
<img src="../../module/UCREPORT/img/help/report_create.png" alt="formulaire de soumission du titre du bulletin" /><br />
Une fois le bulletin publié, vous êtes redirigé vers l\'accueil de l\'outil, affichant la liste des bulletins disponibles.<br />
<img src="../../module/UCREPORT/img/help/report_created.png" alt="le bulletin a été publié" />

<h2>Les bulletins publiés</h2>
Une fois publié, un bulletin ne peut plus être modifié, mais seulement consulté. C\'est pourquoi on parle de "publication".<br />
Vous pouvez en revanche le supprimer ou le rendre "invisible" pour les étudiants via la liste de la page d\'accueil de l\'outil.<br />
<img src="../../module/UCREPORT/img/help/report_list.png" alt="liste des bulletins" /><br />
Pour consulter un bulletin, il suffit de cliquer sur son nom dans cette même liste.<br /><br />
L\'affichage du bulletin se présente ainsi:<br />
<img src="../../module/UCREPORT/img/help/report_view.png" alt="bulletin publié" /><br />
De cette page vous pouvez toujours exporter les données affichées dans les trois mêmes formats que lors de l\'édition.

<h3>Confidentialité du bulletin</h3>
Outre la visibilité du bulletin, l\'outil prévoit d\'autoriser ou non l\'accès des étudiants aux notes de leurs condisciples.<br />
Lorsque cette option est activée, la confidentialité du bulletin est modifiable via l\'icône de la colonne <strong>Public / Privé</strong><em>[Public / Private]</em> de la liste des bulletins.<br/>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="../../web/img/user.png" alt="personnage seul" /> signifie que les étudiants n\'ont accès qu\'à leur propres notes.<br/>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="../../web/img/group.png" alt="deux personnages" /> signifie que toutes les notes sont accessibles à tous les membres de la classe.<br /><br />
<strong>Attention : </strong>Afin de satisfaire à la politique de confidentialité de certains site d\'e-Learning, cette option peut être désactivée par l\'administrateur de la plateforme.<br/>
Il est donc possible que n\'y ayez pas accès. Dans ce cas la colonne n\'est pas visible, et les résultats sont alors "privés".

<h3>Accès des étudiants aux bulletins publiés</h3>
Les étudiants peuvent bien entendu consulter les bulletins publiés (sous les conditions de confidentialité définies par l\'outil) via la page d\'accueil de l\'outil de votre site de cours.<br /><br />
Mais ils peuvent également y accéder via leur bureau qui agère tous les bulletins les concernant.<br />
<img src="../../module/UCREPORT/img/help/desktop_portlet.png" alt="portlet du bureau" /><br />
Les étudiants peuvent exporter les résultats en PDF uniquement.';