<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     ICMONIT 1.0.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICMONIT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses( 'course_user.lib' );
From::Module( 'ICMONIT' )->uses( 'assetlist.lib' , 'storedreport.lib' , 'userreport.lib' );
CssLoader::getInstance()->load( 'report' , 'screen' );
language::load_module_translation( 'ICMONIT' );

$myReport = new UserReport( claro_get_current_user_id() );
$reportList = $myReport->getUserReportList();

$output = '<table class="claroTable emphaseLine" style="width: 100%;">' . "\n"
        . '    <thead>' . "\n"
        . '        <tr class="headerX">' . "\n"
        . '            <th>' . get_lang( 'Course code' ) . '</th>' . "\n"
        . '            <th>' . get_lang( 'Course' ) . '</th>' . "\n"
        . '            <th>' . get_lang( 'Report') . '</th>' . "\n"
        . '            <th>' . get_lang( 'Publication date') . '</th>' . "\n"
        . '            <th>' . get_lang( 'Average score' ) . '</th>' . "\n"
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
                .  '            <td><a href="' . claro_htmlspecialchars( get_module_url( 'ICMONIT' ) . '/report.php?cidReq=' . $report[ 'course_code' ] ) . '">' . claro_utf8_encode( $report[ 'course_title' ] ) . '</a></td>' . "\n"
                .  '            <td><a href="' . claro_htmlspecialchars( get_module_url( 'ICMONIT' ) . '/report.php?cmd=rqView&cidReq=' . $report[ 'course_code' ] . '&id=' . $id ) . '">' . $report[ 'title' ] . '</a></td>' . "\n"
                .  '            <td align="center">'. $report[ 'date' ] . '</td>' . "\n"
                .  '            <td align="center">'. $finalScore . '</td>' . "\n"
                .  '        </tr>' . "\n";
    }
}

$output .= '    </tbody>' . "\n"
        .  '</table>';

echo $output;
