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
$_lang['An error occured: the report has not been created!'] = 'Une erreur est survenue: le bulletin n\' a pu �tre cr��!';
$_lang['An error occured: the report has not been deleted!'] = 'Une erreur est survenue: le bulletin n\' a  pu �tre supprim�!';
$_lang['An error occured: the visibility change failed!'] = 'Une erreur est survenue: la visibilit� n\'a pu �tre chang�e!';
$_lang['Access denied'] = 'Acc�s non autoris�!';
$_lang['Average'] = 'Moyenne';
$_lang['Average score'] = 'Moyenne';
$_lang['Back to the examination list'] = 'retour � la liste des examens';
$_lang['Back to the report'] = 'Retour au bulletin';
$_lang['Back to the report list'] = 'Retour � la liste des bulletins';
$_lang['Choose a title'] = 'Choisissez un titre';
$_lang['Comment for'] = 'Commentaire pour';
$_lang['Comments'] = 'Commentaires';
$_lang['Create a new report'] = 'Cr�er un nouveau bulletin';
$_lang['Create a new session'] = 'Cr�er un nouvel examen';
$_lang['Gathering datas'] = 'R�cup�ration des donn�es';
$_lang['Do you really want to delete this report?'] = 'Etes-vous s�r de vouloir supprimer ce bulletin?';
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
$_lang['Generate the preview'] = 'G�n�rer l\'aper�u';
$_lang['incomplete'] = 'incomplet';
$_lang['Items selection'] = 'S�lection des items � importer';
$_lang['Max value'] = 'Note maximale';
$_lang['My examination results and comments'] = 'Les r�sultats comment�s de mes examens';
$_lang['My report'] = 'Mon bulletin';
$_lang['No report available'] = 'Aucun bulletin disponible';
$_lang['No result at this time'] = 'Pas de r�sultats pour le moment';
$_lang['Number of marks'] = 'Nombre de notes attribu�es';
$_lang['Proportional weight'] = 'Pond�ration relative';
$_lang['Publication date'] = 'Date de publication';
$_lang['Publish the report'] = 'Publier le bulletin';
$_lang['Report'] = 'Bulletin';
$_lang['Report list'] = 'Liste des bulletins';
$_lang['Report settings'] = 'Param�tres du bulletin';
$_lang['Reset'] = 'R�tablir';
$_lang['Reset scores'] = 'Tout remettre � z�ro';
$_lang['See my examination result details'] = 'Voir les commentaires de mes r�sultats';
$_lang['Select'] = 'S�lectionner';
$_lang['Student Report'] = 'Bulletin';
$_lang['The changes has been recorded'] = 'Les changements ont �t� �ffectu�s avec succ�s';
$_lang['The examination could not be created'] = 'L\'examen n\'a pas pu �tre cr�e';
$_lang['The examination could not be deleted'] = 'L\'examen n\'a pas pu �tre supprim�';
$_lang['The examination %title has been created'] = 'L\'examen %title a �t� cr�e';
$_lang['The examination has been reseted'] = 'Les notes ont �t�s r�initialis�es avec succ�s';
$_lang['The examination have been successfully deleted!'] = 'L\'examen a �t� supprim�';
$_lang['The report has been successfully created!'] = 'Le bulletin a �t� cr�� avec succ�s!';
$_lang['The report has beeen successfully deleted!'] = 'Le bulletin a �t� supprim�!';
$_lang['To import from'] = 'A importer de l\'outil';
$_lang['Weight'] = 'Pond�ration';
$_lang['weight'] = 'pond�ration';
$_lang['wt.'] = 'pond.';
$_lang['Weighted global score'] = 'Moyenne globale pond�r�e';
$_lang['You don\'t have score in this report'] = 'Vous n\'avez pas de note pour ce bulletin';
$_lang['You must give a score to add a comment'] = 'Vous devez d\'abord assigner une note avant d\'ajouter un commentaire';
$_lang['Your modifications have been successfully saved!'] = 'Vos modifications ont �t� enregistr�es avec succ�s';

$_lang['blockReportHelp'] = '<h2>Aide de l\'outil "Bulletin"</h2>
<h3>Description succincte:</h3>
<h4>Cet outil:</h4>
<ul>
    <li>agr�ge les notes en provenance de l\'outil "travaux"</li>
    <li>permet de s�lectionner les travaux que l\'on veut voir figurer dans le bulletin</li>
    <li>permet d\'ajouter (si on le d�sire) une note d\'examen, ainsi qu\'un commentaire</li>
    <li>permet d\'assigner des pond�rations aux travaux (ainsi qu\'� l\'examen)</li>
    <li>calcule les moyennes (moyennes des �tudiants dans chaque travail, moyenne pond�r�e de chaque �tudiant, moyenne globale)</li>
    <li>permet l\'export en MS-XML (lisible avec MS-Office >= 2003 et OpenOffice), csv et pdf</li>
    <li>permet de g�n�rer plusieurs bulletins "officiels" (c�d que l\'on veut rendre accessibles aux �tudiants)</li>
</ul>

<h4>Les bulletins g�n�r�s:</h4>
<ul>
    <li>sont accessibles aux �tudiants via les sites de cours ET via leurs bureaux respectifs</li>
    <li>ne permettent aux �tudiants que l\'acc�s � leur propres notes</li>
    <li>peuvent �tre �galement export�s</li>
    <li>peuvent �tre rendus "invisibles" (c�d non accessibles aux �tudiants)</li>
</ul>

<h3>Usage:</h3>

<h4>Pour "publier" un bulletin:</h4>

<p><strong>1.</strong>. Lorsque l\'on clique sur l\'ic�ne de l\'outil bulletin (Student Report), on arrive sur la liste des bulletins publi�s.<br />
Au d�but, cette liste est vide.</p>

<p><strong>2.</strong>. Cliquez sur "Cr�er un nouveau bulletin/Create a new report".</p>

<p><strong>3.</strong> Vous arrivez � la liste des travaux pr�sents dans le cours.<br />
    C\'est ici que vous choisissez les travaux que vous voulez faire figurer dans le bulletin.<br />
    Par d�faut, seuls les travaux "visibles" sont activ�s.<br />
    Il y a aussi une ligne (en rose) correspondant � l\'examen: activez-l� si vous voulez ajouter une note d\'examen � votre bulletin.<br />
    C\'est aussi ici que vous d�terminez les pond�rations des diff�rents travaux:</p>
    <ul>
       <li>vous constaterez que toutes les valeurs de la colonne "pond�ration" sont � 100 par d�faut.</li>
       <li>vous pouvez modifier cette valeur proportionnellement au poids que vous voulez lui assigner</li>
       <li>la pond�ration exprim�e en % est automatiquement calcul�e dans la colonne "pond�ration relative" (il faut cliquer sur "OK" pour cela).</li>
    </ul>

<p><strong>4.</strong> Cet outil vous permet d\'ajouter �galement des notes suppl�mentaires � votre bulletin. Par exemple, une note d\'examen...<br />
    Pour ce faire, cliquez sur "ajouter une note suppl�mentaire". Un petit formulaire vous demandant de choisir un titre pour cette nouvelle note appara�tra alors...<br />
    Une fois le titre valid�, une nouvelle ligne, surlign�e en rose, d\affichera dans la liste des travaux.<br />
    Vous pourrez encoder (et modifier) ces notes en cliquant sur le petit crayon correspondant : cela donne acc�s � un formulaire permettant d\'assigner une note d\'examen � chaque �tudiant.<br />
    Vous pouvez aussi ajouter un commentaire.<br />
    Le bouton "Tout remettre � z�ro/Reset scores" r�initialise le formulaire. Vous perdez alors tout ce que vous y avez inscrit. Mais rassurez-vous, les donn�es contenues dans les bulletins d�j� publi�s seront conserv�es.<br />
    La commande "Param�tres du bulletin/Report settings" permet de revenir � la page d�crite en 3<br />
    Si vous voulez supprimer une note, cliquer sur la pettie croix rouge...<br />
</p>

<p><strong>5.</strong> Une fois cela fait, vous pouvez cliquer sur "g�n�rer l\'aper�u/Generate the preview" pour que l\'outil rapatrie les donn�es de l\'outil "travaux" et calcule les moyennes.<br />
    L\'aper�u se pr�sente comme un tableau: chaque travail correspond � une colonne, chaque rang�e � un �tudiant.<br />
    L\'ent�te du tableau comporte donc le nom des travaux et un rappel de leur pond�rations respectives.<br />
    La rang�e qui suit affiche les moyennes pour chaque travail.<br />
    Les cases pour lesquelles il manque des notes sont marqu�es "vide/empty".<br />
    La derni�re colonne affiche les moyennes globales pond�r�s.<br />
    La moyenne globale n\'est calcul�e que s\'il y a une note pour chaque travail.<br />
</p>

<p><strong>5bis.</strong> La colonne "activer" permet de d�terminer quels �tudiants verront ses notes publi�es.<br />
    Par d�faut, il s\'agit des �tudiants pour lesquels les notes ont �t� attribu�es pour chaque colonne.<br />
    Pour "activer" ou "d�sactiver" un �tudiant, il suffit de cliquer sur le petit oeil correspondant<br />
    En "activant" un �tudiant, les notes inexistantes sont automatiquement converties en 0 (z�ro)<br />
</p>

<p><strong>5ter.</strong> A ce stade vous pouvez exporter le r�sultats dans trois formats diff�rents: MS-XML, csv et pdf.<br />
    Il suffit pour cela de cliquer sur les trois derni�res ic�nes situ�es dans la barre des commandes juste au dessus du tableau.<br />
    Les exports csv et pdf sont bien connus au sein de Claroline mais le format MS-XML m�rite quelques explications:</p>
    <ul>
        <li>Il s\'agit d\'un format introduit par Microsoft pour la suite Office 2003</li>
        <li>Ce format n\'est donc lisible que pour les versions 2003 ET sup�rieures de MS-Office</li>
        <li>Il est �galement lisible par OpenOffice (toutes versions)</li>
        <li>L\'avantage de ce format sur le csv est que les relations entre les cellules sont sauvegard�es: vous pouvez donc modifier le fichier au sein de votre tableur favori, les moyennes seront automatiquement recalcul�es.</li>
    </ul>

<p><strong>6.</strong> Si vous �tes satisfait du r�sultat, vous pouvez "publier" votre bulletin en cliquant sur "G�n�rer le bulletin".<br />
    Un petit formulaire s\'affiche alors vous demandant de lui donner un nom.<br />
    Sinon, vous pouvez revenir � l\'�tape 3 en cliquant sur "param�tres du bulletin/report settings".<br />
    Si vous avez activ� les notes d\'examen vous pouvez aussi les modifier en cliquant sur "Modifier les notes d\'examen".<br />
</p>

<p><strong>7.</strong> Si tout ce passe bien, une bo�te de dialogue vous signale que votre bulletin a �t� cr��.<br />
    Cliquez alors sur "Retour � la liste des bulletins/Back to report list" pour v�rifier qu\'il est bien l�.<br />
    Vous pouvez cr�er autant de bulletins que vous voulez.<br />
    Vous pouvez les supprimer<br />
    Vous pouvez changer leur visibilit� (par d�faut ils sont visibles)<br />
    Si vous cliquez sur le bulletin, vous verrez que celui-ci n\'affiche que les �tudiants qui ont re�u une note partout. Cela permet de r�server la publication de tel ou tel bulletin � tel ou tel �tudiant.<br />
    Les donn�es d\'un bulletin publi� peuvent �galement �tre export�es dans les trois formats d�crits plus haut, mais elles ne peuvent pas �tre modifi�es.<br />
    Dans leurs sites de cours, les �tudiants peuvent acc�der � leurs notes en cliquant sur "Student Report".<br />
    Ils sont �galement avertis de la disponibilit� des bulletins via leurs bureaux.<br />
    Le connecteur du bureau affiche le lien vers les diff�rents bulletins, mais affiche �galement leur moyennes pond�r�es.<br />
    Les �tudiants n\'ont acc�s qu\'� leur propres notes, et � elles seules.<br />
    Les �tudiant peuvent exporter leur note en pdf uniquement.
</p>';