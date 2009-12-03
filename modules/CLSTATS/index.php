<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 1.0-alpha $Revision$
 *
 * @copyright (c) 2001-2009 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLSTATS
 *
 * @author Dimitri Rambout <dim@claroline.net>
 *
 */

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

//SECURITY CHECK

if ( ! claro_is_user_authenticated() ) claro_disp_auth_form();
if ( ! claro_is_platform_admin() ) claro_die(get_lang('Not allowed'));

FromKernel::uses('utils/input.lib','utils/validator.lib','user.lib');
From::Module('CLSTATS')->uses('stats.lib','courselistiterator.lib', 'statsrenderer.lib');

ClaroBreadCrumbs::getInstance()->prepend( get_lang('Administration'), get_path('rootAdminWeb') );

$dialogBox = new DialogBox();

try
{
    $nameTools = get_lang('Platform statistics');
    
    $userInput = Claro_UserInput::getInstance();
  
    $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
        'view', 'rqStats', 'exStats', 'rqNewReport'
    ) ) );
    
    $cmd = $userInput->get( 'cmd','view' );
    $action = $userInput->get( 'action' );
    $bunchCourses = $userInput->get( 'bunchCourses', null );
    
    $out = '';
    
    $out .= claro_html_tool_title($nameTools);
    
    $cmdMenu = array();
    
    $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=view', get_lang( 'Home' ) );
    $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=rqStats', get_lang( 'Generate all stats' ) );
    $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=rqStats&action=bunch', get_lang( 'Generate stats for a bunch of courses' ) );
    $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=rqNewReport', get_lang( 'Generate a report' ) );
    
    $out .= claro_html_menu_horizontal( $cmdMenu );
    
    switch( $cmd )
    {
        case 'rqStats' :
        {
            if( $action == 'bunch' )
            {
                $out .= ClaroStatsRenderer::bunchCourses( Stats_CourseList::countPendingCourses() );
                break;
            }
            else
            {
                //Check if pending courses exist in DB
                if( $pendingCourses = Stats_CourseList::countPendingCourses() )
                {
                    $out .=  ClaroStatsRenderer::pendingCourses( $pendingCourses ); 
                }
                else
                {
                    $action = 'reset';
                }
            }
        }        
        case 'exStats' :
        {
            if( !is_null( $action ) )
            {
                switch( $action )
                {
                    case 'doPending' :
                    {
                        $reset = false;
                    }
                    break;
                    case 'bunchCourses' :
                    {
                        if( ! is_null( $bunchCourses ) && Stats_CourseList::countPendingCourses() == 0 )
                        {
                            $reset = true;
                        }
                        else
                        {
                            $reset = false;
                        }
                    }
                    break;
                    default :
                    {
                        $reset = true;
                    }
                }
                
                $out .= ClaroStatsRenderer::generateStats( $reset, $bunchCourses );
            }
        }
        break;
        case 'rqNewReport' :
        {
            //TODO check if last report == last stats (don't regenerate a report where anything change)
            $report = new Stats_Report();
            $reports = $report->loadFreshContent();
            $reportContent[ 'content' ] = $reports['report'];
            
             $date = time();
            
            $result_usage = $report->saveUsage( $reports['usageReport'], $date );
            $result = $report->save( $reportContent, $date );
            
            
            $out .= ClaroStatsRenderer::generateReport( ($result_usage && $result) ? true : false );
        }
        break;
        case 'view' :
        {
            if( isset( $_GET['report'] ) )
            {
                $reportId = (int) $_GET['report'];
            }
            elseif( isset( $_POST['report'] ) )
            {
                $reportId = (int) $_POST['report'];
            }
            else
            {
                $reportId = null;
            }
            
            $userInput->setValidator('display', new Claro_Validator_AllowedList( array(
                'details', 'summary'
            ) ) );
            
            $display = $userInput->get( 'display','summary' );
            
            
            
            $display = $userInput->get( 'display','summary' );
            
            $reports = Stats_ReportList::countReports();
            
            $thisReport = null;
            $report = new Stats_Report();
            if( !is_null( $reportId ) && $reportId )
            {
                $thisReport = $report->load( $reportId );
                $usageReport = $report->loadUsage( $reportId );
                $reportDate = $reportId;
            }
            else
            {
                //Load last report
                $lastReport = $reports->fetch();
                if( isset( $lastReport['date'] ) && $lastReport['date'] > 0 )
                {                    
                    $thisReport = $report->load( $lastReport['date'] );
                    $usageReport = $report->loadUsage( $lastReport['date'] );
                }
                $reportDate = $lastReport['date'];
            }
            
            $out .= ClaroStatsRenderer::view( $reports, $display, $thisReport, $reportDate, $usageReport );
        }
        break;
    }
    
    Claroline::getDisplay()->body->appendcontent( $out ); 
    
}
catch(Exception $e )
{
  if ( claro_debug_mode() )
  {
    $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
  }
  else
  {
    $dialogBox->error( $e->getMessage() );
  }
  
  Claroline::getDisplay()->body->appendcontent( $dialogBox->render() );
}

echo $claroline->display->render();

//Stats_CourseList::init( $dbCoursesPath );

?>