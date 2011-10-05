/*
Module COMPILATIO v1.6 pour Claroline 
Compilatio - www.compilatio.net
*/
/* Suppression de la table qui stock les associations entre documents compilatio et claroline */
DROP TABLE IF EXISTS `__CL_MAIN__compilatio_docs`;
/* Suppression de la table qui stock les inoformations sur le serveur CAS pour compilatio */
DROP TABLE IF EXISTS `__CL_MAIN__compilatio_auth_serv`;