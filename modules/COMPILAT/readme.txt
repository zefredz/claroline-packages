Documentation Technique

==================================================

Module COMPILATIO v1.5.2 pour Claroline 
Par David Charbonnier - david@sixdegres.fr
Pour Sixdgrés - www.compilatio.net
Testé sur Claroline 1.8.11

==================================================
I] Spécifications techniques

Le module nécessite PHP5 ou supérieur et les librairie suivante : php_openssl, php_soap et php_curl.
Le module nécessite MySQL 4.3 ou supérieur.
Le module nécessite Apache 1.3 ou supérieur.


==================================================
II] Arboresecence

/compilatio/
   +conf/
      +def/
         COMPILAT.def.conf.inc.php
   +img/
      +green/
         bar_1.gif
         bar_1r.gif
         bar_1u.gif
      +grey/
         bar_1.gif
         bar_1r.gif
         bar_1u.gif
      +orange/
         bar_1.gif
         bar_1r.gif
         bar_1u.gif
      +red/
         bar_1.gif
         bar_1r.gif
         bar_1u.gif
      ajouter.gif
      compilatio-logo.gif
      trash.gif
   +lang/
      lang_french.php
   +lib/
      +cas
	 CAS.php
	 client.php
      assignment.class.php
      compilatio.class.php
      compilatio.lib.php
      mime.ini
   +setup/
      install.php
      uninstall.php
   compilist.php
   entry.php
   icon.gif
   manifest.xml
   upload.php
   readme.txt

==================================================
III] Principe de fonctionnement

Le module fonctionne à partir de deux scripts d'affichage :
	- entry.php qui affiche la liste des travaux pour un cours
	- compilist.php qui affiche la liste des soumissions sous forme de tableau pour un travail et propose les actions compilatio en fonction de l'etat du document

Pour traiter les actions compilatio on fait appel à une librairie de fonctions et une classe:
	- La librairie compilatio.lib.php qui traite les demandes d'actions et la validation des données
	- La classe compilatio.class.php qui traite les interactions entre le module et le webservice tout les appels à cette classe sont effectuée dans la librairie compilatio.lib.php
	- Les fichier CAS.php et client.php sont utilisés par la librairie compilatio.lib.php pour géré l'authentification CAS dans le module.
	- Le fichier mime.ini contient des information sur les types mime des fichiers afin de permettre la validation des formats de fichiers que l'ont soumet à compilatio dans la librairie.

==================================================
IV] Liste des méthodes


----------------------------------------
compilatio.class.php



compilatio() = Constructeur, on créer la connexion avec le webservice 
		
	
md5hash SendDoc(string title,string description,string filename,string mimetype,string content) = Méthode qui permet le chargement de fichiers sur le compte compilatio
	
	title = Titre du ducument sous compilatio
	description = Description du document sous compilatio
	filename = Nom du fichier (nom.extension)
	mimetype = Type mime du fichier 
	content	= Contenu du fichier converti en string pour permettre le transfert via soap 	


md5hash GetDoc(md5hash compi_hash) = Méthode qui récupère les informations d'un document donné

	compi_hash = Id du document sous compilatio		


md5hash GetReportUrl(md5hash compi_hash) = Méthode qui permet de récupéré l'url du rapport d'un document donné
	
	compi_hash = Id du document sous compilatio	


void DelDoc(md5hash compi_hash) = Méthode qui permet de supprimé sur le compte compilatio un document donné
		
	compi_hash = Id du document sous compilatio


void StartAnalyse(md5hash compi_hash) = Méthode qui permet de lancer l'analyse d'un document donné
		
	compi_hash = Id du document sous compilatio
	

Array GetQuotas() = Méthode qui permet de récupéré les quotas du compte compilatio
	

md5hash AddAuthServ(string type,string version,string host,string port,string uri) = Méthode qui permet l'ajout d'un serveur d'authentification SSO

	type = type de serveur d'authentification (dans notre cas "cas") 
	version = version du protocole d'authentification
	host = adresse du serveur d'authentification
	port = port utilisé par le serveur d'authentification
	uri = chemin vers le script d'authentification


void DelAuthServ(md5hash id) = Méthode qui permet la suppression d'un serveur d'authentification SSO
	id = Id du serveur d'authentification sous compilatio

----------------------------------------
compilatio.lib.php


string IsInCompilatio(int doc_id,array doc_array) = Fonction qui vérifie si un documents est déjà chargé sur compilitatio ou non en comparant l'id du ducument à un array composé de tout les documents associé a cette assesment/travail

	doc_id = code claroline du document
	array doc_array = tableau de l'ensemble des documents chargés sur compilatio pour le cours courant


string GetCompiStat(md5hash compilatio_id) = Fonction qui retourne le statut d'un document sur compilatio à partir de son hash md5 compilatio

	compilatio_id = Id du document sous compilatio


string Compi_list(int assign_id,int doc_id,string status,string table,md5hash compilatio_id) = Fonction qui créer le tableau des documents claroline et qui propose les actions possible sur ceux si en fonction de le statut

	assign_id = code claroline du travail/assignment
	doc_id = code claroline du document
	status = etat actuel du document vis à vis de compilatio (analysé,en cours d'analyse,en attente...etc..)
	table = table claroline contenant la liste des soumissions pour un cours
	compilatio_id = Id du document sous compilatio 


void SendDoc(int doc_id,int assigId,string table,string courseCode) = Fonction qui envois un document claroline sur compilatio

	doc_id = code claroline du document
	assigId = code claroline du travail/assignment
	table = table claroline contenant l'ensemble des informations sur le document à charger.
	courseCode = code claroline identifiant le cours courant


void SupprDoc(md5hash id_compi) = Fonction qui supprime un document à partir de son hash compilatio via le webservice

	id_compi = Id du document sous compilatio


void AnaDoc(md5hash id_compi) = Fonction qui lance l'analyse d'un document à partir de son hash compilatio

	id_compi = Id du document sous compilatio
	

void Clean_compilatio(string tbl_wrk_submission,string course,int ass) = Fonction qui synchronise les documents compilatio et claroline ex: supprimer les documents compilatio n'existant plus sur claro

	tbl_wrk_submission = table claroline contenant la liste des soumissions pour un travail/assignement
	course = code claroline identifiant le cours courant
	ass = code claroline du travail/assignment


string typeMime(string nomFichier) = Fonction qui récupère et vérifie le type mime d'un fichier soumis

	nomFichier = nom du fichier dont ont veux vérifier le type mime 



string compi_bar (int percent,int factor,int seuil_faible,int seuil_eleve) = Fonction d'affichage graphique sous forme de barre d'un % avec gestion multicolore par palier => évolution de claro_html_progress_bar

	percent = pourcentage de remplissage de la barre
	factor = dimenssion la longeur de la barre de progression (factor * 100px)
	seuil_faible = % palier bas
	seuil_eleve = % palier haut


boolean is_md5(string hash) = Fonction qui vérifie que $hash est bien un hash md5 valid

	hash = chaine pourlaquelle on souhaite vérifier la validité md5

	
md5hash GetAuthID() = Fonction qui récupère l'id compilatio du serveur CAS, vérifie sa validité et le concatene avec l'url du rapport
	