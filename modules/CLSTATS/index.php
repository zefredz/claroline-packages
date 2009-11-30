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
    
    $out = '';
    
    $out .= claro_html_tool_title($nameTools);
    
    $cmdMenu = array();
    
    $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=view', get_lang( 'Home' ) );
    $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=rqStats', get_lang( 'Generate stats' ) );
    $cmdMenu[] = claro_html_cmd_link( 'index.php?cmd=rqNewReport', get_lang( 'Generate a report' ) );
    
    $out .= claro_html_menu_horizontal( $cmdMenu );
    
    switch( $cmd )
    {
        case 'rqStats' :
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
                    default :
                    {
                        $reset = true;
                    }
                }
                
                $out .= ClaroStatsRenderer::generateStats( $reset );
            }
        }
        break;
        case 'rqNewReport' :
        {
            //TODO check if last report == last stats (don't regenerate a report where anything change)
            $report = new Stats_Report();
            $reportContent[ 'content' ] = $report->loadFreshContent();
            
            $result = $report->save( $reportContent );
            
            $out .= ClaroStatsRenderer::generateReport( $result );
        }
        break;
        case 'view' :
        {
            $reports = Stats_ReportList::countReports();
            
            //Load last report
            $lastReport = $reports->fetch();
            if( isset( $lastReport['date'] ) && $lastReport['date'] > 0 )
            {
                $report = new Stats_Report();
                $thisReport = $report->load( $lastReport['date'] );
            }
            else
            {
                $thisReport = null;
            }
            
            $out .= ClaroStatsRenderer::view( $reports, $thisReport, $lastReport['date'] );
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