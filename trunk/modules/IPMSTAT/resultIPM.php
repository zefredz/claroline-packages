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


// STAT to CSV

// this is script  is  write  for  internal work of IPM

$langStatsOfAllCourse = 'Stat de tous les cours confondus';
$langFile = "tracking";
$langToolName = 'statclaro';
$dispDetail = false;
$outputFormat = 'html';
require '../../claroline/inc/claro_init_global.inc.php';
include './lib/platform.stat.lib.php';

$nameTools = $langToolName;

$tbl = claro_sql_get_tbl2(array('cours','stat_courses','stat_data_matrix','cours_user'));
$tbl ['course'] = $tbl ['cours'];

$nameTools = $langToolName;

include  get_path('includePath') . '/lib/statsUtils.lib.inc.php';

// INSTALL Tables
// this code would be removed when module can install admin modules
include './connector/IPMSTAT.install.php';
$is_allowedToTrack = claro_is_platform_admin();

// display title
$titleTab['mainTitle'] = $nameTools;
$titleTab['subTitle'] = $langStatsOfAllCourse;
$passe = toolStat::get_current_scan_id();
$courseLeft = claro_get_course_not_scanned_count($passe);

$csvArroundText = '"';
$csvSeparator = $csvArroundText . ',' . $csvArroundText;

if ($outputFormat=='html')
{
    $menu[]=claro_html_cmd_link('index.php',get_lang('home'));
    $menu[]=claro_html_cmd_link('scan.php',get_lang('Run scan'));


    include( get_path('incRepositorySys') . '/claro_init_header.inc.php');
    echo claro_html_tool_title($titleTab)
    .    claro_html_menu_horizontal($menu)
    ;
}
else
{
    header("Content-Type: application/csv-tab-delimited-table");
    header("Content-disposition: filename=table.csv");
}
//echo $csvArroundText . str_replace("\\",'',implode(($statGrid['colList']),$csvSeparator)).$csvArroundText."\n";


if ($courseLeft > 0)
{
    echo claro_html_message_box('Scan not finished or outdated, please scan again');
}
else
{

    //$statGridTitle = toolStat::read_stat_column_names();
    $statDigestDataGrid = new claro_datagrid();
    $statDigestDataGrid->colTitleList = array('content'=>'content','qty'=>'count');

    /** CVS EXPORT
foreach ($statGrid as $course => $stats)
{
    echo $csvArroundText . str_replace("\\",'',implode(($stats),$csvSeparator)) . $csvArroundText . "\n";
}
*/

    $valueList = array(
        'CLANN_QTY',
        'CLDOC_QTY',
        'CLCAL_QTY',
        'CLFRM_QTY',
        'CLDSC_QTY',
        );

    $valueList = toolStat::read_stat_column_names();

    foreach ($valueList as $value)
    {
        if (substr($value,-3,3)!='QTY') continue;
        echo claro_html_tool_title($value);
        $tbl = claro_sql_get_tbl2( array('stat_courses','stat_data_matrix'));
        $sql = "SELECT content, count(course_code) qty
                FROM  `" . $tbl ['stat_data_matrix'] . "` "
        . "where valueName = '".$value."' "
        . " GROUP BY content"
        . " ORDER BY content"
        ;

        $statGrid = claro_sql_query_fetch_all_rows($sql);
        $statGrid = collapse_result($statGrid);

        if ($outputFormat=='html')
        {
            $statDigestDataGrid->set_grid($statGrid);
            echo $statDigestDataGrid->render();
        }
    }
}

?>