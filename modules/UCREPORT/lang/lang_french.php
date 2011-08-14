<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$_lang['Activate'] = 'Activer';
$_lang['Add a mark'] = 'Ajouter une note';
$_lang['Additional mark'] = 'Note additionnelle';
$_lang['An error occured: the report has not been created!'] = 'Une erreur est survenue: le bulletin n\' a pu être créé!';
$_lang['An error occured: the report has not been deleted!'] = 'Une erreur est survenue: le bulletin n\' a  pu être supprimé!';
$_lang['An error occured: the visibility change failed!'] = 'Une erreur est survenue: la visibilité n\'a pu être changée!';
$_lang['Access denied'] = 'Accès non autorisé!';
$_lang['Average'] = 'Moyenne';
$_lang['Average score'] = 'Moyenne';
$_lang['Back to the examination list'] = 'retour à la liste des examens';
$_lang['Back to the report'] = 'Retour au bulletin';
$_lang['Back to the report list'] = 'Retour à la liste des bulletins';
$_lang['Choose a title'] = 'Choisissez un titre';
$_lang['Comment for'] = 'Commentaire pour';
$_lang['Comments'] = 'Commentaires';
$_lang['Create a new report'] = 'Créer un nouveau bulletin';
$_lang['Create a new session'] = 'Créer un nouvel examen';
$_lang['Gathering datas'] = 'Récupération des données';
$_lang['Do you really want to delete this report?'] = 'Etes-vous sûr de vouloir supprimer ce bulletin?';
$_lang['empty'] = 'vide';
$_lang['Edit examination scores'] = 'Modifier les notes d\'examen';
$_lang['Error while saving the modifications'] = 'Une erreur s\'est produite durant l\'enregistrement de vos modifications';
$_lang['Examination'] = 'Note d\'examen';
$_lang['Examinations'] = 'Notes d\'examen';
$_lang['Examination list'] = 'Liste des examens';
$_lang['Examination Report']= 'Notes d\'Examen';
$_lang['Examination scores'] = 'Notes d\'examen';
$_lang['Export to csv'] = 'Exporter vers csv';
$_lang['Export to MS-Excel xlsx file'] = 'Exporter vers MS-Excel xlm ( *.xlsx)';
$_lang['Export to pdf'] = 'Exporter en pdf';
$_lang['Generate the preview'] = 'Générer l\'aperçu';
$_lang['incomplete'] = 'incomplet';
$_lang['Items selection'] = 'Sélection des items à importer';
$_lang['Max value'] = 'Note maximale';
$_lang['My examination results and comments'] = 'Les résultats commentés de mes examens';
$_lang['My report'] = 'Mon bulletin';
$_lang['No report available'] = 'Aucun bulletin disponible';
$_lang['No result at this time'] = 'Pas de résultats pour le moment';
$_lang['Number of marks'] = 'Nombre de notes attribuées';
$_lang['Proportional weight'] = 'Pondération relative';
$_lang['Publication date'] = 'Date de publication';
$_lang['Publish the report'] = 'Publier le bulletin';
$_lang['Report'] = 'Bulletin';
$_lang['Report list'] = 'Liste des bulletins';
$_lang['Report settings'] = 'Paramètres du bulletin';
$_lang['Reset'] = 'Rétablir';
$_lang['Reset scores'] = 'Tout remettre à zéro';
$_lang['See my examination result details'] = 'Voir les commentaires de mes résultats';
$_lang['Select'] = 'Sélectionner';
$_lang['Student Report'] = 'Bulletin';
$_lang['The changes has been recorded'] = 'Les changements ont été éffectués avec succès';
$_lang['The examination could not be created'] = 'L\'examen n\'a pas pu être crée';
$_lang['The examination could not be deleted'] = 'L\'examen n\'a pas pu être supprimé';
$_lang['The examination %title has been created'] = 'L\'examen %title a été crée';
$_lang['The examination has been reseted'] = 'Les notes ont étés réinitialisées avec succès';
$_lang['The examination have been successfully deleted!'] = 'L\'examen a été supprimé';
$_lang['The report has been successfully created!'] = 'Le bulletin a été créé avec succès!';
$_lang['The report has beeen successfully deleted!'] = 'Le bulletin a été supprimé!';
$_lang['To import from'] = 'A importer de l\'outil';
$_lang['Weight'] = 'Pondération';
$_lang['weight'] = 'pondération';
$_lang['wt.'] = 'pond.';
$_lang['Weighted global score'] = 'Moyenne globale pondérée';
$_lang['You don\'t have score in this report'] = 'Vous n\'avez pas de note pour ce bulletin';
$_lang['You must give a score to add a comment'] = 'Vous devez d\'abord assigner une note avant d\'ajouter un commentaire';
$_lang['Your modifications have been successfully saved!'] = 'Vos modifications ont été enregistrées avec succès';

$_lang['blockReportHelp'] = '<h2>Aide de l\'outil "Bulletin"</h2>
<h3>Description succincte:</h3>
<h4>Cet outil:</h4>
<ul>
    <li>agrège les notes en provenance de l\'outil "travaux"</li>
    <li>permet de sélectionner les travaux que l\'on veut voir figurer dans le bulletin</li>
    <li>permet d\'ajouter (si on le désire) une note d\'examen, ainsi qu\'un commentaire</li>
    <li>permet d\'assigner des pondérations aux travaux (ainsi qu\'à l\'examen)</li>
    <li>calcule les moyennes (moyennes des étudiants dans chaque travail, moyenne pondérée de chaque étudiant, moyenne globale)</li>
    <li>permet l\'export en MS-XML (lisible avec MS-Office >= 2003 et OpenOffice), csv et pdf</li>
    <li>permet de générer plusieurs bulletins "officiels" (càd que l\'on veut rendre accessibles aux étudiants)</li>
</ul>

<h4>Les bulletins générés:</h4>
<ul>
    <li>sont accessibles aux étudiants via les sites de cours ET via leurs bureaux respectifs</li>
    <li>ne permettent aux étudiants que l\'accès à leur propres notes</li>
    <li>peuvent être également exportés</li>
    <li>peuvent être rendus "invisibles" (càd non accessibles aux étudiants)</li>
</ul>

<h3>Usage:</h3>

<h4>Pour "publier" un bulletin:</h4>

<p><strong>1.</strong>. Lorsque l\'on clique sur l\'icône de l\'outil bulletin (Student Report), on arrive sur la liste des bulletins publiés.<br />
Au début, cette liste est vide.</p>

<p><strong>2.</strong>. Cliquez sur "Créer un nouveau bulletin/Create a new report".</p>

<p><strong>3.</strong> Vous arrivez à la liste des travaux présents dans le cours.<br />
    C\'est ici que vous choisissez les travaux que vous voulez faire figurer dans le bulletin.<br />
    Par défaut, seuls les travaux "visibles" sont activés.<br />
    Il y a aussi une ligne (en rose) correspondant à l\'examen: activez-là si vous voulez ajouter une note d\'examen à votre bulletin.<br />
    C\'est aussi ici que vous déterminez les pondérations des différents travaux:</p>
    <ul>
       <li>vous constaterez que toutes les valeurs de la colonne "pondération" sont à 100 par défaut.</li>
       <li>vous pouvez modifier cette valeur proportionnellement au poids que vous voulez lui assigner</li>
       <li>la pondération exprimée en % est automatiquement calculée dans la colonne "pondération relative" (il faut cliquer sur "OK" pour cela).</li>
    </ul>

<p><strong>4.</strong> Cet outil vous permet d\'ajouter également des notes supplémentaires à votre bulletin. Par exemple, une note d\'examen...<br />
    Pour ce faire, cliquez sur "ajouter une note supplémentaire". Un petit formulaire vous demandant de choisir un titre pour cette nouvelle note apparaîtra alors...<br />
    Une fois le titre validé, une nouvelle ligne, surlignée en rose, d\affichera dans la liste des travaux.<br />
    Vous pourrez encoder (et modifier) ces notes en cliquant sur le petit crayon correspondant : cela donne accès à un formulaire permettant d\'assigner une note d\'examen à chaque étudiant.<br />
    Vous pouvez aussi ajouter un commentaire.<br />
    Le bouton "Tout remettre à zéro/Reset scores" réinitialise le formulaire. Vous perdez alors tout ce que vous y avez inscrit. Mais rassurez-vous, les données contenues dans les bulletins déjà publiés seront conservées.<br />
    La commande "Paramètres du bulletin/Report settings" permet de revenir à la page décrite en 3<br />
    Si vous voulez supprimer une note, cliquer sur la pettie croix rouge...<br />
</p>

<p><strong>5.</strong> Une fois cela fait, vous pouvez cliquer sur "générer l\'aperçu/Generate the preview" pour que l\'outil rapatrie les données de l\'outil "travaux" et calcule les moyennes.<br />
    L\'aperçu se présente comme un tableau: chaque travail correspond à une colonne, chaque rangée à un étudiant.<br />
    L\'entête du tableau comporte donc le nom des travaux et un rappel de leur pondérations respectives.<br />
    La rangée qui suit affiche les moyennes pour chaque travail.<br />
    Les cases pour lesquelles il manque des notes sont marquées "vide/empty".<br />
    La dernière colonne affiche les moyennes globales pondérés.<br />
    La moyenne globale n\'est calculée que s\'il y a une note pour chaque travail.<br />
</p>

<p><strong>5bis.</strong> La colonne "activer" permet de déterminer quels étudiants verront ses notes publiées.<br />
    Par défaut, il s\'agit des étudiants pour lesquels les notes ont été attribuées pour chaque colonne.<br />
    Pour "activer" ou "désactiver" un étudiant, il suffit de cliquer sur le petit oeil correspondant<br />
    En "activant" un étudiant, les notes inexistantes sont automatiquement converties en 0 (zéro)<br />
</p>

<p><strong>5ter.</strong> A ce stade vous pouvez exporter le résultats dans trois formats différents: MS-XML, csv et pdf.<br />
    Il suffit pour cela de cliquer sur les trois dernières icônes situées dans la barre des commandes juste au dessus du tableau.<br />
    Les exports csv et pdf sont bien connus au sein de Claroline mais le format MS-XML mérite quelques explications:</p>
    <ul>
        <li>Il s\'agit d\'un format introduit par Microsoft pour la suite Office 2003</li>
        <li>Ce format n\'est donc lisible que pour les versions 2003 ET supérieures de MS-Office</li>
        <li>Il est également lisible par OpenOffice (toutes versions)</li>
        <li>L\'avantage de ce format sur le csv est que les relations entre les cellules sont sauvegardées: vous pouvez donc modifier le fichier au sein de votre tableur favori, les moyennes seront automatiquement recalculées.</li>
    </ul>

<p><strong>6.</strong> Si vous êtes satisfait du résultat, vous pouvez "publier" votre bulletin en cliquant sur "Générer le bulletin".<br />
    Un petit formulaire s\'affiche alors vous demandant de lui donner un nom.<br />
    Sinon, vous pouvez revenir à l\'étape 3 en cliquant sur "paramètres du bulletin/report settings".<br />
    Si vous avez activé les notes d\'examen vous pouvez aussi les modifier en cliquant sur "Modifier les notes d\'examen".<br />
</p>

<p><strong>7.</strong> Si tout ce passe bien, une boîte de dialogue vous signale que votre bulletin a été créé.<br />
    Cliquez alors sur "Retour à la liste des bulletins/Back to report list" pour vérifier qu\'il est bien là.<br />
    Vous pouvez créer autant de bulletins que vous voulez.<br />
    Vous pouvez les supprimer<br />
    Vous pouvez changer leur visibilité (par défaut ils sont visibles)<br />
    Si vous cliquez sur le bulletin, vous verrez que celui-ci n\'affiche que les étudiants qui ont reçu une note partout. Cela permet de réserver la publication de tel ou tel bulletin à tel ou tel étudiant.<br />
    Les données d\'un bulletin publié peuvent également être exportées dans les trois formats décrits plus haut, mais elles ne peuvent pas être modifiées.<br />
    Dans leurs sites de cours, les étudiants peuvent accéder à leurs notes en cliquant sur "Student Report".<br />
    Ils sont également avertis de la disponibilité des bulletins via leurs bureaux.<br />
    Le connecteur du bureau affiche le lien vers les différents bulletins, mais affiche également leur moyennes pondérées.<br />
    Les étudiants n\'ont accès qu\'à leur propres notes, et à elles seules.<br />
    Les étudiant peuvent exporter leur note en pdf uniquement.
</p>';