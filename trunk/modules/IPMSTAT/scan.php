<?php // $Id$
/**
 * CLAROLINE
 *
 *
 * @version 1.0 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLSTAT
 *
 * @author Christophe Gesché <moosh@claroline.net>
 *
 */
define('FORMAT_DISP_HTML','FORMAT_DISP_HTML'.__LINE__);
define('FORMAT_DISP_IMG','FORMAT_DISP_IMG'.__LINE__);
// this is script  is  write  for  internal work of IPM

$langStatsOfAllCourse = 'Stat de tous les cours confondus';
$langFile = "tracking";
$langToolName = "Calcul des stats cumulées des cours";
$displayFormat = FORMAT_DISP_HTML;
require '../../claroline/inc/claro_init_global.inc.php';

$nameTools = $langToolName;

include get_path('includePath') . '/lib/statsUtils.lib.inc.php';
include './lib/platform.stat.lib.php';
$tbl = claro_sql_get_tbl2(array('cours','stat_courses','stat_data_matrix','cours_user'));

// INSTALL Tables
// this code would be removed when module can install admin modules
include './connector/IPMSTAT.install.php';
$is_allowedToViewScan = claro_is_platform_admin();
$passe = toolStat::get_current_scan_id();
$courseToScanByStep = get_conf('courseByStep',40);
$pauseByLoop = get_conf('pauseByLoop',1);

$courseQty = claro_get_course_quantity();
$courseLeft = claro_get_course_not_scanned_count($passe);
$courseList = claro_get_course_not_scanned($passe);
$courseResult = array();
foreach($courseList as $thisCourse)
{
    $courseResult[$thisCourse['code']] = $thisCourse;
    $courseResult[$thisCourse['code']]['member_qty'] = 0;
    $courseResult[$thisCourse['code']]['tutor_qty']  = 0;
}
// display title
$titleTab['mainTitle'] = $nameTools;
$titleTab['subTitle'] = get_lang('Compute %stepQty  on %totalQty. Left : %leftQty For Scan id : %scanId',
array('%stepQty' =>$courseToScanByStep, '%totalQty' =>$courseQty, '%leftQty' =>$courseLeft, '%scanId' =>$passe));

$htmlHeadXtra[]='<META HTTP-EQUIV="Refresh" CONTENT="'.get_conf('scanAutoRefresh',7).';URL='.$_SERVER['PHP_SELF'].'">';
$menu[]=claro_html_cmd_link('index.php',get_lang('home'));
$menu[]=claro_html_cmd_link('results.php',get_lang('display results'));

if($displayFormat == FORMAT_DISP_HTML)
{
    include  get_path('includePath') . '/claro_init_header.inc.php';

    echo claro_html_tool_title($titleTab)
    .    claro_html_menu_horizontal($menu)
    ;
}
if (count($courseResult))
{
    /*
    //-- total number of user in the course
    $sql = "SELECT
                code_cours,
                count( * ) qty,
                sum(IF (`tutor`=1, 1, 0) ) qty_tut
            FROM `" . $tbl ['cours_user'] . "`
            WHERE code_cours IN ('".implode("', '", array_keys($courseResult)) ."')
            GROUP BY code_cours";

    $res = claro_sql_query($sql);
    echo '<br>compute user qty (by status)';
    while (($course_userdata = mysql_fetch_array($res,MYSQL_ASSOC) ))
    {
        if ($course_userdata['statut']=='1')
        $courseResult[$course_userdata['code_cours']]['course_admin_qty'] = $course_userdata['qty'];
        else $courseResult[$course_userdata['code_cours']]['course_student_qty'] = $course_userdata['qty'];
        $courseResult[$course_userdata['code_cours']]['member_qty'] += $course_userdata['qty'];
        $courseResult[$course_userdata['code_cours']]['tutor_qty'] += $course_userdata['qty_tut'];
    }
*/
    printf('<hr>Scan %d courses <br>',count($courseResult));
    $toolStat = new toolStat();
    foreach ($courseResult as $code_cours => $courseData)
    {
        global $dbGlu,$courseTablePrefix;

        $currentCourseDbNameGlu = $courseTablePrefix . $courseData['dbName'] . $dbGlu; // use in all queries
        $currentCourseDir       = $coursesRepositorySys . $courseData['path'] . '/document';

        $ctblname = claro_sql_get_course_tbl($currentCourseDbNameGlu);



        /*
        $sql = "SELECT count( `access_id` ) access_qty , `access_tid` `access_tool`
            FROM `" . $ctbl_track_e_access . "`
            GROUP BY `access_tid` ";

        $courseDataList = claro_sql_query_fetch_all($sql);
        foreach($courseDataList as $courseData)
        {
            $courseResult[$code_cours]['access_qty'.$courseData['access_tool']] = $courseData['access_qty'];
        }


        $sql = "SELECT count( `access_id` ) access_qty_365  , `access_tid` `access_tool`
            FROM `" . $ctbl_track_e_access . "`
            GROUP BY `access_tid` ";

        $courseDataList = claro_sql_query_fetch_all($sql);
        foreach($courseDataList as $courseData)
        {
            $courseResult[$code_cours]['access_qty_365'.$courseData['access_tool']] = $courseData['access_qty_365'];
        }

        $sql = "SELECT count( `access_id` ) access_qty , `access_tid` `access_tool`
            FROM `" . $ctbl_track_e_access . "`
            GROUP BY `access_tid` ";

        $courseDataList = claro_sql_query_fetch_all($sql);
        foreach($courseDataList as $courseData)
        {
            $courseResult[$code_cours]['access_qty'.$courseData['access_tool']] = $courseData['access_qty'];
        }

        $sql="SELECT  tl.access                        access,
                   IFNULL(ct.script_url,'ext')         url,
                   ct.claro_label                      label

            FROM      `" . $ctbl_tool . "`             tl
            LEFT JOIN `" . $tbl ['tool'] . "` ct
            ON        ct.id = tl.tool_id";

        $courseToolDataList = claro_sql_query_fetch_all($sql);
        foreach($courseToolDataList as $courseToolData)
        {
            $courseResult[$code_cours][trim($courseToolData['label'],'_').'_access'] = $courseToolData['access'];
            $courseResult[$code_cours][trim($courseToolData['label'],'_').'_url'] = $courseToolData['url'];
        }
*/


  /*      // Groups
        $ctbl = claro_sql_get_tbl2( 'group_team'
                                 , array(CLARO_CONTEXT_TOOLLABEL => 'CLGRP', CLARO_CONTEXT_COURSE => $code_cours));

        $sql = "SELECT count(id) qty FROM `".$ctbl['group_team']."` ";
        $courseResult[$code_cours]['group_qty'] = claro_sql_query_get_single_value($sql);
*/
        /*
        $sql = "SELECT count(id) qty FROM `".$ctbl['tool_intro']."` ";
        $courseResult[$code_cours]['intro'] = claro_sql_query_get_single_value($sql);
        */
/*
        // toolList
        $ctbl = claro_sql_get_tbl2( 'tool_list'
                                 , array(CLARO_CONTEXT_COURSE => $code_cours));

        $sql = "SELECT count(id) qty FROM `".$ctbl['tool']."` ";
        $courseResult[$code_cours]['tool_qty'] = claro_sql_query_get_single_value($sql);
*/
        // announcements
        $ctbl = claro_sql_get_tbl2( 'announcement'
                                 , array(CLARO_CONTEXT_TOOLLABEL => 'CLANN', CLARO_CONTEXT_COURSE => $code_cours));

        $sql = "SELECT count(id) qty FROM `".$ctbl['announcement']."` ";
        $courseResult[$code_cours]['CLANN_QTY'] = claro_sql_query_get_single_value($sql);

        // agenda
        $ctbl = claro_sql_get_tbl2( 'calendar_event'
                                 , array(CLARO_CONTEXT_TOOLLABEL => 'CLCAL',
                                         CLARO_CONTEXT_COURSE => $code_cours));
        $sql = "SELECT count(id) qty FROM `".$ctbl['calendar_event']."` ";
        $courseResult[$code_cours]['CLCAL_QTY'] = claro_sql_query_get_single_value($sql);

        // description
        $ctbl = claro_sql_get_tbl2( 'course_description'
                                 , array(CLARO_CONTEXT_TOOLLABEL => 'CLDSC',
                                         CLARO_CONTEXT_COURSE => $code_cours));
        $sql = "SELECT count(id) qty FROM `".$ctbl['course_description']."` ";
        $courseResult[$code_cours]['CLDSC_QTY'] = claro_sql_query_get_single_value($sql);

        $ctbl = claro_sql_get_tbl2( 'document' , array(CLARO_CONTEXT_TOOLLABEL => 'CLDOC', CLARO_CONTEXT_COURSE => $code_cours));

        // documents
        $sql = "SELECT count(id) qty FROM `".$ctbl['document']."` ";
        $courseResult[$code_cours]['CLDOC_QTY'] = claro_sql_query_get_single_value($sql);

        $sql = "SELECT count(id) qty FROM `".$ctbl['document']."` WHERE visibility ='v'";
        $courseResult[$code_cours]['CLDOC_VISIBLE_QTY'] = claro_sql_query_get_single_value($sql);
        $courseResult[$code_cours]['CLDOC'.'_files'] = dircount($currentCourseDir);

        // discussion
        $ctbl = claro_sql_get_tbl2( array('bb_posts','bb_topics','bb_forums',)
                                 , array(CLARO_CONTEXT_TOOLLABEL => 'CLFRM', CLARO_CONTEXT_COURSE => $code_cours));


        // forum
        $sql = "SELECT count(forum_id) qty FROM `".$ctbl['bb_forums']."` ";
        $courseResult[$code_cours]['CLFRM_QTY'] = claro_sql_query_get_single_value($sql);



        $sql = "SELECT count(post_id) qty FROM `".$ctbl['bb_posts']."` ";
        $courseResult[$code_cours]['CLFRM'.'_post_qty'] = claro_sql_query_get_single_value($sql);

        $sql = "SELECT count(topic_id) qty FROM `".$ctbl['bb_topics']."` ";
        $courseResult[$code_cours]['CLFRM'.'TOPIC_QTY'] = claro_sql_query_get_single_value($sql);
/*
        // lp
        $sql = "SELECT count(learnPath_id) qty FROM `".$ctbl_lp_learnPath."` ";
        $courseResult[$code_cours]['CLLNP'.'_LP_QTY'] = claro_sql_query_get_single_value($sql);


        $sql = "SELECT count(module_id) qty FROM `".$ctbl_lp_module."` ";
        $courseResult[$code_cours]['CLLNP'.'_MOD_QTY'] = claro_sql_query_get_single_value($sql);

        $sql = "SELECT count(asset_id) qty FROM `".$ctbl_lp_asset."` ";
        $courseResult[$code_cours]['CLLNP'.'_A7_QTY'] = claro_sql_query_get_single_value($sql);

*/
        // quizz
        $ctbl = claro_sql_get_tbl2( array('qwz_question','qwz_exercise',)
                                 , array(CLARO_CONTEXT_TOOLLABEL => 'CLQWZ', CLARO_CONTEXT_COURSE => $code_cours));

        $sql = "SELECT count(id) qty FROM `".$ctbl['qwz_question']."` ";
        $courseResult[$code_cours]['CLQWZ'.'_ASK_QTY'] = claro_sql_query_get_single_value($sql);

        $sql = "SELECT count(id) qty FROM `".$ctbl['qwz_exercise']."` ";
        $courseResult[$code_cours]['CLQWZ'.'_QWZ_QTY'] = claro_sql_query_get_single_value($sql);
/*
        // user_info
        $sql = "SELECT count(id) qty FROM `".$ctbl_userinfo_content."` ";
        $courseResult[$code_cours]['CLUI'.'_blocfill'] = claro_sql_query_get_single_value($sql);

        $sql = "SELECT count(id) qty FROM `".$ctbl_userinfo_def."` ";
        $courseResult[$code_cours]['CLUI'.'_bloc'] = claro_sql_query_get_single_value($sql);
*/
        // works

        $ctbl = claro_sql_get_tbl2( array('wrk_assignment','wrk_submission')
                                 , array(CLARO_CONTEXT_TOOLLABEL => 'CLCAL',
                                         CLARO_CONTEXT_COURSE => $code_cours));

        $sql = "SELECT count(id) qty FROM `".$ctbl['wrk_assignment']."` ";

        $courseResult[$code_cours]['CLWRK_ASS_QTY'] = claro_sql_query_get_single_value($sql);

        $sql = "SELECT count(id) qty FROM `".$ctbl['wrk_submission']."` ";

        $courseResult[$code_cours]['CLWRK_sub_QTY'] = claro_sql_query_get_single_value($sql);



        if ($toolStat->write_stat($code_cours, $courseResult[$code_cours], $passe))
        if($displayFormat == FORMAT_DISP_HTML)
            echo "$code_cours updated<BR>" ;
        sleep($pauseByLoop);
        flush();
    }
}

if($displayFormat == FORMAT_DISP_HTML)
include  get_path('includePath') . '/claro_init_footer.inc.php';
?>