<?php
/**
 * Description :
 * établit la communication avec le serveur SOAP de Compilatio.net
 * appelle diverses méthodes concernant la gestion d'un document dans Compilatio.net
 *
 * Date: 31/03/09
 * @version:0.9
 *
 */

class compilatio
{
    /* Clef d'identification pour le compte Compilatio*/
    var $key  = null;
    /*Connexion au Webservice*/
    var $soapcli;
    
    /*Constructeur -> crée la connexion avec le webservice*/
    //MODIF 2009-03-19: passage des paramètres
    //MODIF 2009-04-08: si le client soap ne peut pas etre créé
    // alors l'erreur survenue est sauvegardée dans soapcli 
    // les fonctions appellant call sur cet object testent s'il s'agit bien d'un object
    // renvoyent donc l'erreur sauvegardée dans soapcli sinon 
    function compilatio($key,$urlsoap,$proxy_host,$proxy_port)
    {
        try
        {
            if(!empty($key))
            {
                $this->key = $key;
                
                if(!empty($urlsoap))
                {
                    if(!empty($proxy_host))
                    {
                        $param=array('trace'=>false,
                            'soap_version'=> SOAP_1_2,
                            'exceptions'=>true,
                            'proxy_host'=> '"' . $proxy_host . '"',
                            'proxy_port'=> $proxy_port);
                    }
                    else
                    {
                        $param=array('trace'=>false,
                        'soap_version'=>SOAP_1_2,
                        'exceptions'=>true);
                    }
                    $this->soapcli = new SoapClient($urlsoap,$param);
                }
                else{
                    
                    $this->soapcli = 'WS urlsoap not available' ;
                }
            }
            else
            {
                $this->soapcli ='API key not available';
            }
        }
        catch (SoapFault $fault)
        {
            $this->soapcli = "Error constructor compilatio " . $fault->faultcode ." " .$fault->faultstring ;
        }
        catch (Exception $e)
        {
            $this->soapcli = "Error constructor compilatio with urlsoap" . $urlsoap;
        }
    }

    /*Méthode qui permet le chargement de fichiers sur le compte compilatio*/
    function SendDoc($title,$description,$filename,$mimetype,$content)
    {
        try
        {
            if (!is_object($this->soapcli))
            {
                return("Error in constructor compilatio() " . $this->soapcli);
            }
            
            $idDocument = $this->soapcli->__call('addDocumentBase64',array($this->key,utf8_encode(urlencode($title)),utf8_encode(urlencode($description)),utf8_encode(urlencode($filename)),utf8_encode($mimetype),base64_encode($content)));
            return $idDocument;
        }
        catch (SoapFault $fault)
        {
            return("Erreur SendDoc()" . $fault->faultcode ." " .$fault->faultstring);
        }
    }
    
    /*Méthode qui récupère les informations d'un document donné*/
    function GetDoc($compi_hash)
    {
        try
        {
            if (!is_object($this->soapcli))
            {
                return("Error in constructor compilatio() " . $this->soapcli);
            }
            
            $param=array($this->key,$compi_hash);
            $idDocument = $this->soapcli->__call('getDocument',$param);
            return $idDocument;
        }
        catch (SoapFault $fault)
        {       
            return("Erreur GetDoc()" . $fault->faultcode ." " .$fault->faultstring);
        }
    }

    /*Méthode qui permet de récupéré l'url du rapport d'un document donné*/
    function GetReportUrl($compi_hash)
    {
        try
        {
            if (!is_object($this->soapcli))
            {
                return("Error in constructor compilatio() " . $this->soapcli);
            }
            
            $param=array($this->key,$compi_hash);
            $idDocument = $this->soapcli->__call('getDocumentReportUrl',$param);
            return $idDocument;
        }
        catch (SoapFault $fault)
        {
            return("Erreur  GetReportUrl()" . $fault->faultcode ." " .$fault->faultstring);
        }
    }
    
    /*Méthode qui permet de supprimé sur le compte compilatio un document donné*/
    function DelDoc($compi_hash)
    {
        try
        {
            if (!is_object($this->soapcli))
            {
                return("Error in constructor compilatio() " . $this->soapcli);
            }
            
            $param=array($this->key,$compi_hash);
            $this->soapcli->__call('deleteDocument',$param);
        }
        catch (SoapFault $fault)
        {
            return("Erreur  DelDoc()" . $fault->faultcode ." " .$fault->faultstring);
        }
    }

    /*Méthode qui permet de lancer l'analyse d'un document donné*/
    function StartAnalyse($compi_hash)
    {
        try
        {
            if (!is_object($this->soapcli))
            {
                return("Error in constructor compilatio() " . $this->soapcli);
            }
            
            $param=array($this->key,$compi_hash);
            $this->soapcli->__call('startDocumentAnalyse',$param);
        }
        catch (SoapFault $fault)
        {
            return("Erreur  StartAnalyse()" . $fault->faultcode ." " .$fault->faultstring);
        }
    }
    
    /*Méthode qui permet de récupéré les quotas du compte compilatio*/
    function GetQuotas()
    {
        try
        {
            if (!is_object($this->soapcli))
            {
                return("Error in constructor compilatio() " . $this->soapcli);
            }
            
            $param=array($this->key);
            $resultat=$this->soapcli->__call('getAccountQuotas',$param);
            return $resultat;
        }
        catch (SoapFault $fault)
        {
            return("Erreur  GetQuotas()" . $fault->faultcode ." " .$fault->faultstring);
        }
    }
}

/**
 * Fonction qui extrait l'extension d'un fichier et dit si elle est gérée par Compilatio
*  return true si l'extension est supportée, false sinon
*  2009-03-18*/
function veriffiletype($nomFichier)
{
    $types = array("doc","docx","rtf","xls","xlsx","ppt","pptx","odt","pdf","txt","htm","html");
    $extension=substr($nomFichier, strrpos($nomFichier, ".")+1);
    $extension=strtolower($extension);
    
    if (in_array($extension, $types))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * Fonction  affichage de la barre de progression d'analyse version 3.1
 *
 * @param $statut du document
 * @param $pour
 * @param $chemin_images
 * @param $texte : array contenant les morceaux de texte nécessaires
 * @return unknown_type
 */
function getProgressionAnalyseDocv31($status,$pour=0,$chemin_images='',$texte='')
{
    $refreshretour = "<a href=\"javascript:window.location.reload(false);\"><img src=\"".$chemin_images."refresh.gif\" title=\"" . $texte['refresh'] . "\" alt=\"" . $texte['refresh'] . "\"/></a>";
    $debutretour="<table cellpadding=\"0\" cellspacing=\"0\" style=\"border:0px;margin:0px;padding:0px;\"><tr>";
    $debutretour.="<td width=\"15\" style=\"border:0px;margin:0px;padding:0px;\">&nbsp;</td>";
    $debutretour.="<td width=\"25\" valign=\"middle\" align=\"right\" style=\"border:0px;margin:0px;padding:0px;\">$refreshretour</td>";
    $debutretour.= "<td width=\"55\" style=\"border:0px;margin:0px;padding:0px;\">";

    $debutretour2="<table cellpadding=\"0\" cellspacing=\"0\" style=\"border:0px;margin:0px;padding:0px;\"><tr>";
    $debutretour2.="<td width=\"15\" valign=\"middle\" align=\"right\" style=\"border:0px;margin:0px;padding:0px;\">$refreshretour </td>";

    $finretour="</td></tr></table>";

    if($status=="ANALYSE_IN_QUEUE" )
    {
        return $debutretour . "<span style='font-size:11px'>".$texte['analysisinqueue']."</span>".$finretour;
    }
    
    if($status=="ANALYSE_PROCESSING" )
    {
        if($pour==100)
        {
            return $debutretour."<span style='font-size:11px'>".$texte['analysisinfinalization']."</span>".$finretour;
        }
        else
        {
            return $debutretour2."<td width=\"25\" align=\"right\" style=\"border:0px;margin:0px;padding:0px;\">$pour%</td><td width=\"55\" style=\"border:0px;margin:0px;padding:0px;\"><div style=\"background:transparent url(".$chemin_images."mini-jauge_fond.png) no-repeat scroll 0;height:12px;padding:0 0 0 2px;width:55px;\"><div style=\"background:transparent url(".$chemin_images."mini-jauge_gris.png) no-repeat scroll 0;height:12px;width:" . $pour/2 . "px;\"></div></div>".$finretour;
        }
    }
}

/**
 * Fonction d'affichage de la PomprankBar (% de plagiat) version 3.1
 * @param $pourcentagePompage
 * @param $seuil_faible
 * @param $seuil_eleve
 * @param $chemin_images
 * @param $texte : array contenant les morceaux de texte nécessaires
 * @return unknown_type
 */
function getPomprankBarv31($pourcentagePompage, $seuil_faible, $seuil_eleve, $chemin_images='',$texte='')
{
    $pourcentagePompage = round($pourcentagePompage);
    $pour = round((50*$pourcentagePompage)/100);
    
    $retour="<table cellpadding=\"0\" cellspacing=\"0\"><tr>";
    
    if($pourcentagePompage<$seuil_faible)
    {
        $retour.="<td width=\"15\" style=\"border:0px;margin:0px;padding:0px;\"><img src=\"".$chemin_images."mini-drapeau_vert.png\" title=\"" . $texte['result'] . "\" alt=\"faible\" width=\"15\" height=\"15\" /></td>";
        $retour.="<td width=\"25\" align=\"right\" style=\"border:0px;margin:0px;padding:0px;\">" . $pourcentagePompage . "%</td>";
        $retour.= "<td width=\"55\" style=\"border:0px;margin:0px;padding:0px;\"><div style=\"background:transparent url(".$chemin_images."mini-jauge_fond.png) no-repeat scroll 0;height:12px;padding:0 0 0 2px;width:55px;\"><div style=\"background:transparent url(".$chemin_images."mini-jauge_vert.png) no-repeat scroll 0;height:12px;width:" . $pour . "px\"></div></div></td>";
    }
    else if($pourcentagePompage>=$seuil_faible && $pourcentagePompage<$seuil_eleve)
    {
        $retour.="<td width=\"15\" style=\"border:0px;margin:0px;padding:0px;\"><img src=\"".$chemin_images."mini-drapeau_orange.png\" title=\"" . $texte['result'] . "\" alt=\"faible\" width=\"15\" height=\"15\" /></td>";
        $retour.="<td width=\"25\" align=\"right\" style=\"border:0px;margin:0px;padding:0px;\">" . $pourcentagePompage . "%</td>";
        $retour.= "<td width=\"55\" style=\"border:0px;margin:0px;padding:0px;\"><div style=\"background:transparent url(".$chemin_images."mini-jauge_fond.png) no-repeat scroll 0;height:12px;padding:0 0 0 2px;width:55px;\"><div style=\"background:transparent url(".$chemin_images."mini-jauge_orange.png) no-repeat scroll 0;height:12px;width:" . $pour . "px\"></div></div></td>";
    }
    else
    {
        $retour.="<td width=\"15\" style=\"border:0px;margin:0px;padding:0px;\"><img src=\"".$chemin_images."mini-drapeau_rouge.png\" title=\"" . $texte['result'] . "\" alt=\"faible\" width=\"15\" height=\"15\" /></td>";
        $retour.="<td width=\"25\" align=\"right\" style=\"border:0px;margin:0px;padding:0px;\">" . $pourcentagePompage . "%</td>";
        $retour.= "<td width=\"55\" style=\"border:0px;margin:0px;padding:0px;\"><div style=\"background:transparent url(".$chemin_images."mini-jauge_fond.png) no-repeat scroll 0;height:12px;padding:0 0 0 2px;width:55px;\"><div style=\"background:transparent url(".$chemin_images."mini-jauge_rouge.png) no-repeat scroll 0;height:12px;width:" . $pour . "px\"></div></div></td>";
    }
    
    $retour.="</tr></table>";

    return $retour;
}

function is_md5($hash)
{
    if(preg_match('`^[a-f0-9]{32}$`',$hash))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function typeMime($nomFichier)
{
    if(preg_match("@Opera(/| )([0-9].[0-9]{1,2})@", $_SERVER['HTTP_USER_AGENT'], $resultats))
    {
        $navigateur="Opera";
    }
    elseif(preg_match("@MSIE ([0-9].[0-9]{1,2})@", $_SERVER['HTTP_USER_AGENT'], $resultats))
    {
        $navigateur="Internet Explorer";
    }
    else
    {
        $navigateur="Mozilla";
    }
    
    $mime=parse_ini_file("mime.ini");
    $extension=substr($nomFichier, strrpos($nomFichier, ".")+1);
    
    if(array_key_exists($extension, $mime))
    {
        $type=$mime[$extension];
    }
    else
    {
        $type=($navigateur!="Mozilla") ? 'application/octetstream' : 'application/octet-stream';
    }
    
    return $type;
}