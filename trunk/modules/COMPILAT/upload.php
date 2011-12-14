<?php
/*
Module COMPILATIO v1.6 pour Claroline 
Compilatio - www.compilatio.net
*/
/*
Script de mise en ligne des documents defacon indépendante via un pop-up séparé 
permettant ainsi à l'utilisateur de pouvoir continuer à naviguer pdt l'upload des documents.
*/

/*Includes claroline et compilatio */
require '../../claroline/inc/claro_init_global.inc.php';
require_once './lib/assignment.class.php';
require_once './lib/compilatio.class.php';
require_once './lib/compilatio.lib.php';
include_once get_path('incRepositorySys') . '/lib/pager.lib.php';


/*Parametrage pour l'utilisation de soap*/
ini_set('soap.wsdl_cache_enabled', 0); 
ini_set('default_socket_timeout', '1000');

/* Claroline displaying tools */
//claro_set_display_mode_available(true);
$hide_banner=true;

/*Affichage forcé=>flush() d'un message pour faire patienter l'utilisateur strpad=>Debug IE6 */
$out = str_pad('<img src="img/compilatio-logo.gif" alt="compilatio" /><br />'
     . get_lang('Loading on compilatio').'<br /><p class="highlight">'
     . get_lang('Do not close window').'</p>',4096);
@ob_flush();
@flush();
/*Si on doit chargé plusieur documents*/
if(isset($_REQUEST['type']) && $_REQUEST['type']=="multi")
    {
    
    $docs=unserialize(stripslashes(urldecode($_REQUEST['doc'])));
    
    for($k=0;$k<sizeof($docs);$k++)
        {
        
        /*on modifie le serveur timeout pour l'envois de documents lourds*/
        set_time_limit(600);
        if(GetCompiStat($docs[$k])=="NOT_IN_COMPILATIO")
            {
            /*on recharge la fenetre parente pdt le chargement grace à flush() */
            $out .= "<script>".
                 "parent.window.opener.location.replace('compilist.php?assigId=".$_REQUEST['assigId']."');".
                 "</script>";
            @ob_flush();
            @flush();
            //SendDoc($doc[$k],$_REQUEST['assigId'],$_REQUEST['tab'],$_course['officialCode']);
            //$id_compi=SendDoc($doc[$k],$_REQUEST['assigId'],$_REQUEST['tab'],claro_get_current_course_id());
            
            /////////////////
            //insertion directe de la fonction SendDoc pour economiser la moitié de la mémoire
            //function SendDoc($doc_id,$assigId,$table,$courseCode)
            /////////////////
            $doc_id=$docs[$k];
            $assigId=$_REQUEST['assigId'];
            $table=$_REQUEST['tab'];
            $courseCode=claro_get_current_course_id();
            
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
    
        if (get_conf('mode_transport'))
    {
    //on utilise que SOAP pour le transfert des données
    $filename=$doc['submitted_doc_path'];
        
    $id_compi=$compilatio->SendDoc($doc['title'],'',$filename,$mime,file_get_contents($WrkUrl));
  }
    else
    {
    
    //le serveur compilatio récupère lui-même le fichier avec wget
    //de la forme username:password@http://somedomain.com/reg/remotefilename.tar.gz
    if (strlen(get_conf('wget_uri'))>2)
    {
    
        //on utilise l'url par défaut     
      $filename=ereg_replace('/$', '', get_conf('wget_uri')) . '/' . claro_get_course_path().'/'.'work/assig_'.$assigId.'/' . $doc['submitted_doc_path'];

      }
      else
      {
    $filename=ereg_replace("/$", "", $GLOBALS['rootWeb']) . $assignment->getAssigDirWeb().$doc['submitted_doc_path'];

    }
      if (strlen(get_conf('wget_login'))>2)
      {
    $filename=get_conf('wget_login') . ":" . get_conf('wget_password') . "@" . $filename;
    
    }
      

       $mime="text/plain";
       $id_compi=$compilatio->SendDoc($doc['title'],'',$filename,$mime,'get_url');
  }

    
    

    /*Si cela fonctionne on associe dans la BDD le document claroline au document compilatio*/
    $sql4="INSERT INTO `".$tbl_compi."`
    (submission_id,assignment_id,compilatio_id,course_code)
    VALUES (".$doc_id.",".$assigId.",'".$id_compi."','".$courseCode."')";
    //$out .= $id_compi;
  /*On vérifie que l'id document retourné est bien un hash_md5*/
    if(is_md5($id_compi))
        {
        //on ajoute le doucment dans la bdd
        claro_sql_query($sql4);
        }
    else
        {
    claro_die("! Erreur 1 ! <br/> Le code document retourné n'est pas un hash md5 valide!<br>");
        }
        
        
        //////////////
            
            
            
            
            
            
            
            
            if(is_md5($id_compi))
      {
       //l'upload s'est bien déroulé, on a un id_doc compilatio en retour
        AnaDoc($id_compi);
      }
      else
      {
        //il y a une erreur
        claro_die("! Erreur 2! <br/> Le code document retourné n'est pas un hash md5 valide!<br>");
      }
            }       
        }
    /*on ferme le pop-up à la fin du chargement*/
    $out .= "<script>".
         "parent.window.close();".
         "</script>";
    }
/*Si on ne charge qu'un seul document*/
else
    {


      /////////////////
            //insertion directe de la fonction SendDoc pour economiser la moitié de la mémoire
            //function SendDoc($doc_id,$assigId,$table,$courseCode)
            /////////////////
            $doc_id=$_REQUEST['doc'];
            $assigId=$_REQUEST['assigId'];
            $table=$_REQUEST['tab'];
            $courseCode=claro_get_current_course_id();
            
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



    if (get_conf('mode_transport'))
    {
    //on utilise que SOAP pour le transfert des données
    $filename=$doc['submitted_doc_path'];
        
    $id_compi=$compilatio->SendDoc($doc['title'],'',$filename,$mime,file_get_contents($WrkUrl));
  }
    else
    {
    
    //le serveur compilatio récupère lui-même le fichier avec wget
    //de la forme username:password@http://somedomain.com/reg/remotefilename.tar.gz
    if (strlen(get_conf('wget_uri'))>2)
    {
    
    
       $filename=ereg_replace('/$', '', get_conf('wget_uri')) . '/' . claro_get_course_path().'/'.'work/assig_'.$assigId.'/' . $doc['submitted_doc_path'];

        //on utilise l'url par défaut     
      }
      else
      {
    $filename=ereg_replace("/$", "", $GLOBALS['rootWeb']) . $assignment->getAssigDirWeb().$doc['submitted_doc_path'];

    }
      if (strlen(get_conf('wget_login'))>2)
      {
    $filename=get_conf('wget_login') . ":" . get_conf('wget_password') . "@" . $filename;
    
    }
      
       $mime="text/plain";
       $id_compi=$compilatio->SendDoc($doc['title'],'',$filename,$mime,'get_url');
  }


    /*Si cela fonctionne on associe dans la BDD le document claroline au document compilatio*/
    $sql4="INSERT INTO `".$tbl_compi."`
    (submission_id,assignment_id,compilatio_id,course_code)
    VALUES (".$doc_id.",".$assigId.",'".$id_compi."','".$courseCode."')";
    /*On vérifie que l'id document retourné est bien un hash_md5*/
    if(is_md5($id_compi))
        {
        //on ajoute le doucment dans la bdd
        claro_sql_query($sql4);
        }
    else
        {
    claro_die("! Erreur 3! <br/> Le code document retourné n'est pas un hash md5 valide!<br>" . $id_compi);
        }
        
        
        //////////////
    
    
    
    
    
    
    
    if(is_md5($id_compi))
    {
       //l'upload s'est bien déroulé, on a un id_doc compilatio en retour
    AnaDoc($id_compi);
  }
  else
  {
    claro_die("Une erreur s'est produite lors du chargement du document sur Compilatio. Aucun identifiant n'a été renvoyé<br>" . $id_compi);
  }
    /*On actualise la page parent et on ferme le pop-up*/
    $out .= "<script>".
    "parent.window.opener.location.reload();".
    "parent.window.close();".
    "</script>";
    }

Claroline::getInstance()->display->body->appendContent( $out );
echo Claroline::getInstance()->display->render();