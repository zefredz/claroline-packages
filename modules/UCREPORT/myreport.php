<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 0.9.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

From::Module( 'UCREPORT' )->uses( 'report.lib' , 'userreport.lib' );
CssLoader::getInstance()->load( 'report' , 'screen' );

$myReport = new UserReport( claro_get_current_user_id() );
$reportList = $myReport->getUserReportList();

$output = '<table class="claroTable emphaseLine" style="width: 100%;">' . "\n"
        . '    <thead>' . "\n"
        . '        <tr class="headerX">' . "\n"
        . '            <th>' . get_lang( 'Course code' ) . '</th>' . "\n"
        . '            <th>' . get_lang( 'Course' ) . '</th>' . "\n"
        . '            <th>' . get_lang( 'Report') . '</th>' . "\n"
        . '            <th>' . get_lang( 'Publication date') . '</th>' . "\n"
        . '            <th>' . get_lang( 'Your average score' ) . '</th>' . "\n"
        . '        </tr>' . "\n"
        . '    </thead>' . "\n"
        . '    <tbody>' . "\n";

if( empty( $reportList ) )
{
    $output .= '        <tr>' . "\n"
            .  '<td colspan="5" style="color: #888888; text-align: center; font-style: italic;">' . get_lang( 'No report available' ) . '</td>' . "\n"
            .  '</tr>';
}
else
{
    foreach( $reportList as $id => $report )
    {
        $finalScore = $report[ 'final_score' ]
                    ? $report[ 'final_score' ]
                    : '<span style="color: #888888; text-align: center; font-style: italic;">' . get_lang( 'incomplete' ) . '</span>';
        $output .= '        <tr>' . "\n"
                .  '            <td>' . $report[ 'course_code' ] . '</td>' . "\n"
                .  '            <td><a href="' . htmlspecialchars( get_module_url( 'UCREPORT' ) . '/index.php?cidReq=' . $report[ 'course_code' ] ) . '">' . claro_utf8_encode( $report[ 'course_title' ] ) . '</a></td>' . "\n"
                .  '            <td><a href="' . htmlspecialchars( get_module_url( 'UCREPORT' ) . '/index.php?cmd=rqShowReport&cidReq=' . $report[ 'course_code' ] . '&reportId=' . $id ) . '">' . $report[ 'title' ] . '</a></td>' . "\n"
                .  '            <td>'. $report[ 'date' ] . '</td>' . "\n"
                .  '            <td>'. $finalScore . '</td>' . "\n"
                .  '        </tr>' . "\n";
    }
}

$output .= '    </tbody>' . "\n"
        .  '</table>';

echo $output;