<?php
/*
Module COMPILATIO v1.6 pour Claroline 
Compilatio - www.compilatio.net
*/
/*
Librairie contenant les fonctions nécessaires au bon fonctionnement du module compilatio pour claroline.
Elle assure la récupération , la validation et l'affichages des informations des documents claroline/compilatio.
*/

/*Fonction qui vérifie si un documents est déjà chargé sur compilitatio ou non en comparant l'id du ducument à un array composé de tout les documents associé a cette assesment/travail*/
function IsInCompilatio($doc_id,$doc_array)
	{
	$is_in="no";
	if( !empty($doc_array) && is_array($doc_array) )
		{
		for($i=0;$i<sizeof($doc_array);$i++)
			{
			if ($doc_id==$doc_array[$i]['submission_id'])
				{
				//si on trouve l'id du document dans le array on stop le parcour du array et on retourne le hash
				$is_in=$doc_array[$i]['compilatio_id'];
				break;
				}
			}
		}
	//si le document est chargé sur compilatio on retourne son hash md5 compilatio sinon on retourne "no"
	return $is_in;
	}

/*Fonction qui retourne le statut d'un document sur compilatio à partir de son hash md5 compilatio*/
function GetCompiStat($compilatio_id)
	{
	
	if (get_conf('using_SSL'))
{
  $urlsoap='https://service.compilatio.net/webservices/CompilatioUserClient.wsdl';
}
else
{
  $urlsoap='http://service.compilatio.net/webservices/CompilatioUserClient2.wsdl';
}



$compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('soap_proxy_host'),get_conf('soap_proxy_port'));
	if(is_md5($compilatio_id))
		{
		// si $compilatio_id est bien un hash md5 on appel la fonction du webservice compialtio qui retourne le statut du document 
		
    $soap_res = $compilatio->GetDoc($compilatio_id);
    $status = '';
    if(isset($soap_res->documentStatus))
    {
      $status = $soap_res->documentStatus->status;
      return $status;
    }
    else
		{
			if (get_conf('using_debug')=='Yes') claro_die("! Error ! <br/> By getting document status<br>");
		}
    

		}
	else
		{
		// si le hash compilatio n'est pas un hash md5 valide quand par exemple il est "no" (cf : IsInCompilatio() ) On retourne un statut particulier
		return "NOT_IN_COMPILATIO";
		}
	}

/*Fonction qui créer le tableau des documents claroline et qui propose les actions possible sur ceux si en fonction de le statut*/
function Compi_list($assign_id,$doc_id,$status,$table,$compilatio_id)
{
    $id_multi=$compilatio_id; //utilisé pour la checkbox action mutliples
    $isAllowtoEdit=claro_is_allowed_to_edit();
    
    if (get_conf('using_SSL'))
    {
        $urlsoap='https://service.compilatio.net/webservices/CompilatioUserClient.wsdl';
    }
    else
    {
        $urlsoap='http://service.compilatio.net/webservices/CompilatioUserClient2.wsdl'; 
    }
    
    $compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('soap_proxy_host'),get_conf('soap_proxy_port'));
    
    //si le document est déjà sur compilatio on récupère les informations générales le concernant.
    if($status!="NOT_IN_COMPILATIO")
    {
        $soap_res = $compilatio->GetDoc($compilatio_id);
    }
    
    //si l'analyse est complète on propose le résultat de l'analyse sous forme de barre de progression , le liens vers le rapport ainsi que la possibilité de supprimé le document de compilatio
    if($status=="ANALYSE_COMPLETE")
    {
        if(get_conf('using_CAS')==FALSE)
        {
           $soap_res2=$compilatio->GetReportUrl($compilatio_id);
        }
        else
        {
            $soap_res2=$compilatio->GetReportUrl($compilatio_id,"",GetAuthID());
        }
        $texte['result'] = 'Resultat de l\'analyse anti-plagiat';
        //$chaine="<td><img src='icon.gif' alt='Compilatio.net - '/> ".get_lang('Analysis complet').compi_bar($soap_res->documentStatus->indice,0.5,10,35)." - <a href='".$soap_res2."' target='_blank'>".get_lang('View report')." </a>"."</td>";
        $chaine="<td><table style=\"border:0px;margin:0px;padding:0px;\"><tr><td style=\"border:0px;margin:0px;padding:0px;\">".getPomprankBarv31($soap_res->documentStatus->indice,10,35,"img/",$texte)." </td><td style=\"border:0px;margin:0px;padding:0px;\"> - <a href='".$soap_res2."' target='_blank'>".get_lang('View report')." </a></td></tr></table>"."</td>";
    }
    //si l'analyse est en cours on affiche une première barre qui indique l'avancement de l'analyse , une deuxième qui indique le taux actuel de plagiat et la possibilité de supprimer le document
    elseif($status=="ANALYSE_PROCESSING" && $isAllowtoEdit)
    {
        //$chaine="<td><img src='icon.gif' alt='Compilatio.net - '/>".get_lang('Analysis processing').compi_bar($soap_res->documentStatus->progression,0.5).' - '.compi_bar($soap_res->documentStatus->indice,0.5,10,35)." - <a href='javascript:window.location.reload( false );'><img src='img/refresh.gif' alt='Compilatio.net - '/>".get_lang('Refresh')."</a></td><td><a href='compilist.php?assigId=".$assign_id."&cmd=del&doc=".$compilatio_id."'><img src='img/trash.gif' alt='Corbeille '/> ".get_lang('Delete on Compilatio')." </a></td>";
        $texte['analysisinqueue']='Analyse en attente';
        $texte['analysisinfinalization']='Finalisation de l\'analyse';
        $texte['refresh']=get_lang('Refresh');
        
        $chaine="<td>".getProgressionAnalyseDocv31($status,$soap_res->documentStatus->progression,"img/",$texte)."</td>";
        //nouvelle version: on ne supprime plus les docs
        //$chaine.="<td><a href='compilist.php?assigId=".$assign_id."&cmd=del&doc=".$compilatio_id."'><img src='img/trash.gif' alt='Corbeille '/> ".get_lang('Delete on Compilatio')." </a></td>";
    }
    //si l'analyse n'est pas encore lancé on propose de lancer l'analyse en indiquant le nombre de crédits nécessaires à celle ci ou bien supprimer le document
    elseif($status=="ANALYSE_NOT_STARTED" && $isAllowtoEdit)
    {
        $chaine="<td>"."<a href='compilist.php?assigId=".$assign_id."&cmd=start&doc=".$compilatio_id."'><img src='icon.gif' alt='Compilatio '/> ".get_lang('Start analysis')." </a>".get_lang('for').$soap_res->documentStatus->cost.get_lang('credit')." </td>";
        //$chaine.="<td> <a href='compilist.php?assigId=".$assign_id."&cmd=del&doc=".$compilatio_id."'><img src='img/trash.gif' alt='Corbeille '/> ".get_lang('Delete on Compilatio')." </a></td>";
    }
    //compilatio intègre un système de file d'attente des analyse pour limité la surcharge des serveurs, quand l'analyse en en attente seul la suppression du document est proposé
    elseif($status=="ANALYSE_IN_QUEUE" && $isAllowtoEdit)
    {
        $chaine="<td><img src='icon.gif' alt='Compilatio.net - '/>".get_lang('Analysis cued')."</td>";
        //$chaine.="<td> <a href='compilist.php?assigId=".$assign_id."&cmd=del&doc=".$compilatio_id."'><img src='img/trash.gif' alt='Corbeille '/> ".get_lang('Delete on Compilatio')." </a></td>";
    }
    //si le document n'est pas chargé sur compilatio l'utilisateur ayant des droits peut le mettre en ligne
    elseif($status=="NOT_IN_COMPILATIO" && $isAllowtoEdit)
    {
        $chaine="<td>"."<a href='javascript:void(0)' onClick=\"MyWindow=window.open('uploadframe.php?doc=".$doc_id."&assigId=".$assign_id."&tab=".$table."','MyWindow','width=350,height=290,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes'); return false;\"><img src='img/ajouter.gif' alt='' /> ".get_lang('Start analysis')." </a> ".get_lang('On compilatio')."</td>";
        //$chaine.="<td>&nbsp;</td>";
    }
    //si le document n'a pas la bonne extension pour être géré par Compilatio
    elseif($status=="BAD_FILETYPE" && $isAllowtoEdit)
    {
        $chaine="<td>".get_lang('Bad filetype')."</td>";
        //$chaine.="<td>&nbsp;</td>";
    }
    //si le document est trop gros pour être géré par Compilatio
    elseif($status=="BAD_FILESIZE" && $isAllowtoEdit)
    {
        $chaine="<td>".get_lang('Too big file (10Mb Max)')."</td>";
        // $chaine.="<td>&nbsp;</td>";
    }
    //s'il n'y a pas de fichier
    elseif($status=="NO_FILE" && $isAllowtoEdit)
    {
        $chaine="<td>".get_lang('No file to analyze')."</td>";
        //$chaine.="<td>&nbsp;</td>";
    }
    //pour les utilisateurs ayant le droit d'accéder à la page mais qui ne sont pas concerné par les documents ou n'ayant pas les droits nécessaires pour voir l'etat sur compilatio
    else
    {
        $chaine="<td><img src='icon.gif' alt='Compilatio.net - '/>".get_lang('Document not analysed yet')."</td>";
    }
    
    //si l'utisateur a des droits sur le documents alors il a accès à la check box d'actions multiples sur les documents associés à ce travail.	
    if(! isset( $id_multi ) )
    {
        //dans ces cas la, on ne fait rien
        $chaine.="<td>&nbsp;</td>";
    }
    elseif ($status=="NOT_IN_COMPILATIO")
    {
        $chaine.="<td><input type='checkbox' name='mutli_docs[]' value='".$id_multi."'></td>";
    }
    else
    {
        $chaine.="<td>&nbsp;</td>";
    }
  
  /*
  if(($status!="BAD_FILETYPE" && $status!="BAD_FILESIZE" && $isAllowtoEdit) || ($status=="NOT_IN_COMPILATIO" && $isAllowtoEdit)) 
		{
		$chaine.="<td><input type='checkbox' name='mutli_docs[]' value='".$id_multi."'></td>";
		$chaine.=$status;
		}
		*/
	return $chaine;
	}

/*Fonction qui envois un document claroline sur compilatio*/
function SendDoc($doc_id,$assigId,$table,$courseCode)
	{
	$tbl_compi_names = claro_sql_get_tbl('compilatio_docs');
	$tbl_compi = $tbl_compi_names['compilatio_docs'];
	
	/*On récupère les infos du documents pour le mettre en ligne*/
	$sql3="SELECT * FROM `".$table."` WHERE assignment_id=".$assigId." AND id=".$doc_id;
	$results3=claro_sql_query_fetch_all($sql3);
	/*On charge le document sur compilatio via le webservice*/
	$doc=$results3[0];
	$assignment = new Assignment();
	$assignment->load($doc['assignment_id']);
	$WrkUrl =$assignment->getassigdirsys() . $doc['submitted_doc_path'];
	$mime=typeMime($WrkUrl);


if (get_conf('using_SSL'))
{
  $urlsoap='https://service.compilatio.net/webservices/CompilatioUserClient.wsdl';
}
else
{
  $urlsoap='http://service.compilatio.net/webservices/CompilatioUserClient2.wsdl'; 
}


$compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('soap_proxy_host'),get_conf('soap_proxy_port'));
	//$compilatio = new compilatio();
	$id_compi=$compilatio->SendDoc($doc['title'],'',$doc['submitted_doc_path'],$mime,file_get_contents($WrkUrl));
	/*Si cela fonctionne on associe dans la BDD le document claroline au document compilatio*/
	$sql4="INSERT INTO `".$tbl_compi."`
	(submission_id,assignment_id,compilatio_id,course_code)
	VALUES (".$doc_id.",".$assigId.",'".$id_compi."','".$courseCode."')";
	/*On vérifie que l'id document retourné est bien un hash_md5*/
	if(is_md5($id_compi))
		{
		//on ajoute le doucment dans la bdd
		claro_sql_query($sql4);
		return ($id_compi);
		}
	else
		{
		return false;
    //claro_die("! Erreur ! <br/> Le code document retourné n'est pas un hash md5 valide!<br>");
		}
	
	}

//Fonction qui supprime un document à partir de son hash compilatio via le webservice
function SupprDoc($id_compi)
	{
	$tbl_compi_names = claro_sql_get_tbl('compilatio_docs');
	$tbl_compi = $tbl_compi_names['compilatio_docs'];
	$sql5="DELETE FROM `".$tbl_compi."` WHERE compilatio_id='".$id_compi."'";
	
	if (get_conf('using_SSL'))
{
  $urlsoap='https://service.compilatio.net/webservices/CompilatioUserClient.wsdl';
}
else
{
  $urlsoap='http://service.compilatio.net/webservices/CompilatioUserClient2.wsdl'; 
}


$compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('soap_proxy_host'),get_conf('soap_proxy_port'));
  
  //$compilatio = new compilatio();
	if(!$compilatio->DelDoc($id_compi))
		{
		claro_sql_query($sql5);
		}
	else
		{
		claro_die("! Error ! <br/> While deleting document on your Compilatio account<br>");
		}
	}

//Fonction qui lance l'analyse d'un document à partir de son hash compilatio
function AnaDoc($id_compi)
	{
	if (get_conf('using_SSL'))
{
  $urlsoap='https://service.compilatio.net/webservices/CompilatioUserClient.wsdl';
}
else
{
  $urlsoap='http://service.compilatio.net/webservices/CompilatioUserClient2.wsdl'; 
}


$compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('soap_proxy_host'),get_conf('soap_proxy_port'));
	//$compilatio = new compilatio();
	$soap_res = $compilatio->StartAnalyse($id_compi);
	}

/*Fonction qui synchronise les documents compilatio et claroline ex: supprimer les documents compilatio n'existant plus sur claro*/
function Clean_compilatio($tbl_wrk_submission,$course,$ass)
	{
	$tbl_compi_names = claro_sql_get_tbl('compilatio_docs');
	$tbl_compi = $tbl_compi_names['compilatio_docs'];
	
	if (get_conf('using_SSL'))
{
  $urlsoap='https://service.compilatio.net/webservices/CompilatioUserClient.wsdl';
}
else
{
  $urlsoap='http://service.compilatio.net/webservices/CompilatioUserClient2.wsdl'; 
}


$compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('soap_proxy_host'),get_conf('soap_proxy_port'));
  
  //$compilatio = new compilatio();
	//On récupère la liste des documents associé entre claroline et compilatio
	$req="SELECT compilatio_id,submission_id FROM `".$tbl_compi."` WHERE assignment_id=".$ass." AND course_code='".$course."'";
	//On récupère la liste des documents se trouvant réelement sur claroline
	$req2="SELECT id FROM `".$tbl_wrk_submission."` WHERE assignment_id=".$ass;
	$id_array=claro_sql_query_fetch_all($req);
	$id_array2=claro_sql_query_fetch_all($req2);
	//On compare les deux liste
	foreach($id_array as $check)
		{
		for($i=0;$i<sizeof($id_array2);$i++)
			{
			if($id_array2[$i]['id']==$check['submission_id'])
				{
				$is_in=$check['submission_id'];
				break;
				}
			}
		// Si un document compilatio est associé avec un document n'existant plus on le supprime sur compilatio et dans la bdd
		if($is_in!=$check['submission_id'])
			{
			
			   $sql5="DELETE FROM `".$tbl_compi."` WHERE compilatio_id='".$check['compilatio_id']."'";
				claro_sql_query($sql5);
			$compilatio->DelDoc($check['compilatio_id']);
			/*if($compilatio->DelDoc($check['compilatio_id']))
				{
				$sql5="DELETE FROM `".$tbl_compi."` WHERE compilatio_id='".$check['compilatio_id']."'";
				claro_sql_query($sql5);
				}
			else
				{
				claro_die("! Error ! <br/> While cleaning compilatio account from deleted claroline documents<br>");
				}*/
				
				
				
			}
		}
	}

/*Fonction qui récupère et vérifie le type mime d'un fichier soumis*/
/*function typeMime($nomFichier)
	{
	if(preg_match("@Opera(/| )([0-9].[0-9]{1,2})@", $_SERVER['HTTP_USER_AGENT'], $resultats))
	$navigateur="Opera";
	elseif(preg_match("@MSIE ([0-9].[0-9]{1,2})@", $_SERVER['HTTP_USER_AGENT'], $resultats))
	$navigateur="Internet Explorer";
	else $navigateur="Mozilla";
	$mime=parse_ini_file("mime.ini");
	$extension=substr($nomFichier, strrpos($nomFichier, ".")+1);
	//echo $extension;
	//print_r($mime);
	if(array_key_exists(strval($extension), $mime)) $type=$mime[$extension];
	else $type=($navigateur!="Mozilla") ? 'application/octetstream' : 'application/octet-stream';
	return $type;
	}
*/



/*Fonction d'affichage graphique sous forme de barre d'un % avec gestion multicolore par palier => évolution de claro_html_progress_bar*/
function compi_bar ($percent, $factor, $seuil_faible="no", $seuil_eleve="no")
	{
	$title=$percent." %";
    if($seuil_faible=="no" && $seuil_eleve=="no")
		{
		$img_path='grey/';
		}
	elseif($percent<$seuil_faible)
		{
		$img_path='green/';
		}
	elseif($percent>=$seuil_faible && $percent<$seuil_eleve)
		{
		$img_path='orange/';
		}
	elseif($percent>$seuil_eleve)
		{
		$img_path='red/';
		}
	$maxSize  = $factor * 100; //pixels
    $barwidth = $factor * $percent ;
    // display progress bar
    // origin of the bar
    $percentBar = '<img src="img/'.$img_path.'bar_1.gif" width="1" height="12" alt="" title="'.$title.'" />';

    if($percent != 0)
    $percentBar .= '<img src="img/'.$img_path.'bar_1u.gif" width="' . $barwidth . '" height="12" alt=""  title="'.$title.'" />';
    // display 100% bar

    if($percent!= 100 && $percent != 0)
    $percentBar .= '<img src="img/'.$img_path.'bar_1u.gif" width="1" height="12" alt=""  title="'.$title.'" />';

    if($percent != 100)
    $percentBar .= '<img src="img/'.$img_path.'bar_1r.gif" width="' . ($maxSize - $barwidth) . '" height="12" alt=""  title="'.$title.'" />';
    // end of the bar
    $percentBar .=  '<img src="img/'.$img_path.'bar_1.gif" width="1" height="12" alt=""  title="'.$title.'" />';

    return $percentBar;
	}

//fonction qui vérifie que $hash est bien un hash md5 valid
/*function is_md5($hash)
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
*/
//Fonction qui récupère l'id compilatio du serveur CAS, vérifie sa validité et le concatene avec l'url du rapport	
function GetAuthID()
	{
	$tbl_compi_auth_names = claro_sql_get_tbl('compilatio_auth_serv');
	$tbl_compi_auth = $tbl_compi_auth_names['compilatio_auth_serv'];
	
	//On récupère les informations Cas stockées en BDD
	$req="SELECT * FROM `".$tbl_compi_auth."`";
	$array=claro_sql_query_fetch_all($req);
	if (get_conf('using_SSL'))
{
  $urlsoap='https://service.compilatio.net/webservices/CompilatioUserClient.wsdl';
}
else
{
  $urlsoap='http://service.compilatio.net/webservices/CompilatioUserClient2.wsdl'; 
}


$compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('soap_proxy_host'),get_conf('soap_proxy_port'));
  //$compilatio = new compilatio();
	$id_serv=$array[0]['id_auth_serv'];
	
	//Si l'id du serveur n'existe pas on créer une nouvelle association
	if(!is_md5($array[0]['id_auth_serv']))
		{
		//on vide la table de bdd au cas ou...
		$req2="DELETE FROM `".$tbl_compi_auth."`";
		$array=claro_sql_query($req2);
		//on récupère un nouvelle identifiant auth_serv via le webservice
		$soap_res = $compilatio->AddAuthServ("cas",get_conf('version_CAS'),get_conf('host_CAS'),get_conf('port_CAS'),get_conf('uri_CAS'));
		//on associe l'id récupéré au paramètre CAs actuel du module et on stock dans la Bdd
		$req3="INSERT INTO `".$tbl_compi_auth."` (id_auth_serv,version_auth_serv,host_auth_serv,port_auth_serv,uri_auth_serv) VALUES('$soap_res','".get_conf('version_CAS')."','".get_conf('host_CAS')."','".get_conf('port_CAS')."','".get_conf('uri_CAS')."')";
		$array=claro_sql_query($req3);
		$id_serv=$soap_res;
		}
	//Si les infos existent mais ne correspondent pas au paramètre du module 
	if($array[0]['version_auth_serv']!=get_conf('version_CAS') || $array[0]['host_auth_serv']!=get_conf('host_CAS') || $array[0]['uri_auth_serv']!=get_conf('uri_CAS') || $array[0]['port_auth_serv']!=get_conf('port_CAS') )
		{
		//On supprime l'authServ sur compilatio via le webservice
		$compilatio->DelAuthServ($array[0]['id_auth_serv']);
		//On vide la bdd des informations périmées
		$req2="DELETE FROM `".$tbl_compi_auth."`";
		$array=claro_sql_query($req2);
		//on récupère un nouvelle identifiant auth_serv via le webservice
		$soap_res=$compilatio->AddAuthServ("cas",get_conf('version_CAS'),get_conf('host_CAS'),get_conf('port_CAS'),get_conf('uri_CAS'));
		//on associe l'id récupéré au paramètre CAs actuel du module et on stock dans la Bdd
		$req3="INSERT INTO `".$tbl_compi_auth."` (id_auth_serv,version_auth_serv,host_auth_serv,port_auth_serv,uri_auth_serv) VALUES('$soap_res','".get_conf('version_CAS')."','".get_conf('host_CAS')."','".get_conf('port_CAS')."','".get_conf('uri_CAS')."')";
		$array=claro_sql_query($req3);
		$id_serv=$soap_res;
		}
	return $id_serv;
	}

?>
