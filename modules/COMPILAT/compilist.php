<?php
/*
Module COMPILATIO v1.5.2 pour Claroline 
Par David Charbonnier - david@sixdegres.fr
Pour Six degrés - www.compilatio.net
*/
/*
Script d'affichage de la liste des documents associés à un assesment/travail
Adaptation du script workList.php inclus dans le module work de claroline
*/
//////////////////////////////////////////////////////////////////////////
//                          Identifier                                  //
//////////////////////////////////////////////////////////////////////////
$tlabelReq = 'COMPILAT';
//////////////////////////////////////////////////////////////////////////
//                          Includes                                    //
//////////////////////////////////////////////////////////////////////////

require '../../claroline/inc/claro_init_global.inc.php';

require_once 'lib/assignment.class.php';
require_once 'lib/compilatio.class.php';
require_once 'lib/compilatio.lib.php';
include_once get_path('incRepositorySys').'/lib/pager.lib.php';
include_once get_path('incRepositorySys') . '/lib/fileManage.lib.php';

//Gestion de l'authentifiaction CAS
include("lib/cas/check_cas.php");

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) claro_disp_auth_form(true);

$tbl_mdb_names = claro_sql_get_main_tbl();
$tbl_user                = $tbl_mdb_names['user'];
$tbl_rel_course_user     = $tbl_mdb_names['rel_course_user'];

$tbl_cdb_names = claro_sql_get_course_tbl();
$tbl_wrk_submission      = $tbl_cdb_names['wrk_submission'];

$tbl_compi_names = claro_sql_get_tbl('compilatio_docs');
$tbl_compi = $tbl_compi_names['compilatio_docs'];

// use viewMode
claro_set_display_mode_available(true);

//special settings for communication between claroline and compilatio
ini_set('soap.wsdl_cache_enabled', 0); 
ini_set('default_socket_timeout', '100');
set_time_limit(100);

/*--------------------------------------------------------------------
ASSIGNMENT INFORMATIONS
--------------------------------------------------------------------*/
//$req['assignmentId'] = ( isset($_REQUEST['assigId']));
$_REQUEST['assigId']=isset($_REQUEST['assigId']) ? (int) $_REQUEST['assigId'] : 0;
$req['assignmentId'] = $_REQUEST['assigId'];
$assignment = new Assignment();

if ( !$req['assignmentId'] || !$assignment->load($req['assignmentId']) )
{
    // we NEED to know in which assignment we are, so if assigId is not set
    // relocate the user to the previous page
    claro_redirect('entry.php');
    exit();
}

/*============================================================================
    Permissions
  ============================================================================*/

$assignmentIsVisible = (bool) ( $assignment->getVisibility() == 'VISIBLE' );
$is_allowedToEditAll = (bool) claro_is_allowed_to_edit();

if( !$assignmentIsVisible && !$is_allowedToEditAll )
{
    // if assignment is not visible and user is not course admin or upper
    claro_redirect('entry.php');
    exit();
}

//////////////////////////////////////////////////////////////////////////
//                          Business Logic                              //
//////////////////////////////////////////////////////////////////////////
/*Cleaning compilatio if some submissions has been deleted*/
Clean_compilatio($tbl_wrk_submission,claro_get_current_course_id(),$_REQUEST['assigId']);

/*récupération des quotas compilatio*/
if (get_conf('using_SSL'))
{
    $urlsoap='https://service.compilatio.net/webservices/CompilatioUserClient.wsdl';
}
else
{
    $urlsoap='http://service.compilatio.net/webservices/CompilatioUserClient2.wsdl'; 
}


$compilatio = new compilatio(get_conf('clef_compilatio'),$urlsoap,get_conf('proxy_host'),get_conf('proxy_port'));
/*$quotas=$compilatio->GetQuotas();
$use_space=number_format($quotas->usedSpace/1000000,2);
$total_space=$quotas->space/1000000;
$perc_space=$use_space/$total_space*100;
$perc_ana=$quotas->usedCredits/$quotas->credits*100;
*/

/* Fetch array of submissions */
if( $assignment->getAssignmentType() == 'INDIVIDUAL' )
{
    $sql = "SELECT user_id,title,authors,id,submitted_doc_path
            FROM `" . $tbl_wrk_submission
            ."` WHERE assignment_id=".$_REQUEST['assigId'] . " AND parent_id IS NULL";
}
else
{
    $sql = "SELECT user_id,group_id,title,authors,id,submitted_doc_path
            FROM `" . $tbl_wrk_submission
            ."` WHERE assignment_id=".$_REQUEST['assigId'] . " AND parent_id IS NULL";
}

if(!$is_allowedToEditAll)
{
    $sql.=" AND user_id=".claro_get_current_user_id();
}

$results=claro_sql_query_fetch_all($sql);
/* Fetch array of documents that are already associated between claroline & compilatio */
/*$sql2 = "SELECT submission_id,compilatio_id
    FROM `".$tbl_compi."`
    WHERE assignment_id=".$_REQUEST['assigId']." AND course_code='".$_course['officialCode']."'";
*/
$sql2 = "SELECT submission_id,compilatio_id
    FROM `".$tbl_compi."`
    WHERE assignment_id=".$_REQUEST['assigId']." AND course_code='".claro_get_current_course_id()."'";

$results2=claro_sql_query_fetch_all($sql2); 

//print_r($results2);

$cmd = ( isset($_REQUEST['cmd']) )?$_REQUEST['cmd']:'';

if ($is_allowedToEditAll)
{
    if ($cmd=='start')
    {
        if ( isset($_REQUEST['doc']) )
        {
            AnaDoc($_REQUEST['doc']);
        }
        
        claro_redirect('compilist.php?assigId='.$_REQUEST['assigId']);
    }
    elseif($cmd=='del')
    {
        if (isset($_REQUEST['doc']))
        {
            SupprDoc($_REQUEST['doc']);
            claro_redirect('compilist.php?assigId='.$_REQUEST['assigId']);
        }
    }
    elseif($cmd=='multi')
    {
        if(isset($_REQUEST['action']) && isset($_REQUEST['multi_docs']))
        {
            if($_REQUEST['action']=="suppr")
            {
                foreach ($_REQUEST['multi_docs'] as $choix)
                {
                    if(is_md5($choix))
                    {
                        SupprDoc($choix);
                    }
                }
                
                claro_redirect('compilist.php?assigId='.$_REQUEST['assigId']);
            }
            elseif($_REQUEST['action']=="upload")
            {
                $up=$_REQUEST['multi_docs'];
            }
            elseif($_REQUEST['action']=="analyse")
            {
                foreach ($_REQUEST['multi_docs'] as $choix)
                {
                    if(GetCompiStat($choix)=="ANALYSE_NOT_STARTED")
                    {
                        AnaDoc($choix);
                    }
                }
                
                claro_redirect('compilist.php?assigId='.$_REQUEST['assigId']);
            }
        }
        else
        {
            $_REQUEST[ 'cmd' ] = $cmd = null;
        }
    }
}

//////////////////////////////////////////////////////////////////////////
//                          Display                                     //
//////////////////////////////////////////////////////////////////////////

/*--------------------------------------------------------------------
                            HEADER
  --------------------------------------------------------------------*/
$noQUERY_STRING = true;
$nameTools = get_lang('Compilatio')." - ".get_lang('Anti Plagiarism Tool');

/*--------------------------------------------------------------------
                    TOOL TITLE
    --------------------------------------------------------------------*/
$out = claro_html_tool_title( $nameTools
                            . " - "
                            . get_lang('Assignment')
                            . " : "
                            .$assignment->getTitle() );

/*--------------------------------------------------------------------
                            ASSIGNMENT LIST
    --------------------------------------------------------------------*/

/*Formulaire permettant de gérer les actions multiple*/

$out .= '<script language=\'javascript\'>' . "\n"
    . '    function check_all(Etat)' . "\n"
    . '    {' . "\n"
    . '         var balises=document.getElementById("action_multi")[\'multi_docs[]\'];' . "\n"
    . '         for(i=0; i<balises.length; i++) {' . "\n"
    . '         if (balises[i].type == \'checkbox\')' . "\n"
    . '         {' . "\n"
    . '              balises[i].checked = Etat;' . "\n"
    . '         }' . "\n"
    . '    }' . "\n"
    . '}' . "\n"
    . '    function analyse_checked()' . "\n"
    . '    {' . "\n"
    . '         var balises=document.getElementById("action_multi");' . "\n"
    . '         for(i=0; i<balises.length; i++) {' . "\n"
    . '         if (balises[i].type == \'checkbox\' && balises[i].checked == true)' . "\n"
    . '         {' . "\n"
    . '              document.action_multi.action.value=\'upload\';' . "\n"
    . '              document.action_multi.submit();' . "\n"
    . '              exit();' . "\n"
    . '         }' . "\n"
    . '    }' . "\n"
    . '    alert( "' . get_lang( 'Nothing checked!' ) . '" );' . "\n"
    . '}' . "\n"
    . '</script>' . "\n"
    . '<div id="div_chck"><form name="action_multi" id="action_multi" action="" method="POST">' . "\n"
    . '    <input type="hidden" name="action" value="">' . "\n"
    . '    <input type="hidden" name="cmd" value="multi">' . "\n"
    . '    <table class="claroTable emphaseLine" width="100%">' . "\n"
    . '        <thead>' . "\n"
    . '            <tr class="headerX">' . "\n";

$out .= '<th>'.get_lang('Assignments').'</th>'
.    '<th>'.get_lang('Author(s)').'</th>'
.    '<th colspan=3>'.get_lang('Compilatio').'</th>'
.    '</tr>'
.    '</thead>'
.    '<tbody>';

if( isset( $up ) && ! empty( $up ) )
{
    $out .= "<script>".
         "window.open('uploadframe.php?type=multi&doc=".serialize($up)."&assigId=".$_REQUEST['assigId']."&tab=".$tbl_wrk_submission."','MyWindow','width=350,height=290,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes');".
         "</script>";
}

if( !empty($results) && is_array($results) && !isset($_REQUEST['cmd']) )
{
    foreach( $results as $result )
    {
        $docId = $result[ 'id' ];
        $is_in=IsInCompilatio($docId,$results2);
        
        if(strlen($result['title'])>40)
        {
            $title_ass=substr($result['title'],0,38)." (...)";
        }
        else
        {
            $title_ass=$result['title'];
        }
        
        $out .= '<tr>'
            .'<td><a href='.get_path('clarolineRepositoryWeb').'work/user_work.php?authId='.$result['user_id'].'&assigId='.$_REQUEST['assigId'].'>'.$title_ass.'</td>'
            .'<td>'
            .$result['authors'].'</td>';
            /*On vérifie l'etat du document sur compilatio pour proposé l'action qui convient*/
        $filePath = $assignment->getassigdirsys() . $result['submitted_doc_path'];
        
        if ( ! is_file( $filePath ) )
        {
            // s'il n'y a pas de fichier
            $compiliste=Compi_list($_REQUEST['assigId'],$docId,"NO_FILE",$tbl_wrk_submission,$is_in);
        }
        elseif ( claro_get_file_size( $filePath ) > 10000000 )
        {
            //si le fichier est trop gros->erreur
            $compiliste=Compi_list($_REQUEST['assigId'],$docId,"BAD_FILESIZE",$tbl_wrk_submission,$is_in);
        }
        elseif(!veriffiletype($result['submitted_doc_path']))
        {
            //si le fichier n'est pas du bon type->erreur
            $compiliste=Compi_list($_REQUEST['assigId'],$docId,"BAD_FILETYPE",$tbl_wrk_submission,$is_in);
        }
        else
        {
            $compiliste=Compi_list($_REQUEST['assigId'],$docId,GetCompiStat($is_in),$tbl_wrk_submission,$is_in);
        }
        
        $out .= $compiliste;
        $out .= '</tr>';
    }
}

$out .= '<tbody>'
.    '</table>'
.    '</form></div>';
 
if ($is_allowedToEditAll)
{
    /*Gestion multi doc*/
$out .= "<div align='right'>"
.    get_lang('To all')." : "
.    "<a href='javascript:void(0)' onclick='if(check_all(true)) return false;'>".get_lang('check')."</a> - "
.    "<a href='javascript:void(0)' onclick='if(check_all(false)) return false;'>".get_lang('uncheck')."</a>"
.    "</div>"
.    "<div align='right'>"
.    get_lang('Do to selected items')." : "
.    "<a href='javascript:void(0)' onclick='analyse_checked();'><img src='img/ajouter.gif' alt='Ajouter '/>".get_lang('Start analysis')."</a>"
.    "</div>"
.    "<div class='spacer'>&nbsp;</div>";
    /*affichage du quotas compilatio*/
    /*$out .= '<table class="claroTable emphaseLine" width="100%">' . "\n"
    .    '<thead>' . "\n"
    .    '<tr class="headerX">' . "\n"
    .    '<th>'.get_lang('Compilatio quotas')." : ".get_lang('Used space').$use_space." Mo ".compi_bar($perc_space,0.5)." ".get_lang('of')." ".$total_space." Mo - ".get_lang('Analysis credits').$quotas->usedCredits." ".compi_bar($perc_ana,0.5)." ".get_lang('of')." ".$quotas->credits." </th>"
    .    '</thead>'
    .    '</table>';*/
}

Claroline::getInstance()->display->body->appendContent( $out );
echo Claroline::getInstance()->display->render();