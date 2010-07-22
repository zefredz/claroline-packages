<?php // $Id$
/**
 * Claroline Poll Tool
 *
 * @version     UCREPORT 0.8.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'UCREPORT';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses( 'utils/input.lib' , 'utils/validator.lib' , 'display/layout.lib' , 'thirdparty/tcpdf/tcpdf' , 'fileUpload.lib' );
From::Module( 'UCREPORT' )->uses( 'report.lib' , 'report2csv.lib' );

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) claro_disp_auth_form( true );

$dialogBox = new DialogBox();
$pageTitle = array( 'mainTitle' => get_lang( 'Report' ) );

$userInput = Claro_UserInput::getInstance();

if ( claro_is_course_member() || claro_is_allowed_to_edit() )
{
    if ( claro_is_allowed_to_edit() )
    {
        $userInput->setValidator( 'cmd' ,
                                  new Claro_Validator_AllowedList( array( 'rqShowList',
                                                                          'rqShowReport',
                                                                          'rqCreateReport',
                                                                          'rqEditReport',
                                                                          'rqDeleteReport',
                                                                          'exCreateReport',
                                                                          'exEditReport',
                                                                          'exDeleteReport',
                                                                          'exChangeVisibility',
                                                                          'exExport2xml',
                                                                          'exExport2csv',
                                                                          'exExport2pdf' )
                                )
        );
    }
    else
    {
        $userInput->setValidator( 'cmd' ,
                                  new Claro_Validator_AllowedList( array( 'rqShowList',
                                                                          'rqShowReport',
                                                                          'exExport2pdf' )
                                )
        );
    }

    $cmd = $userInput->get( 'cmd', 'rqShowList' );
    $reportId = $userInput->get( 'reportId' );
    
    $report = new Report( claro_get_current_course_id() , $reportId );
    
    // CONTROLLER
    switch( $cmd )
    {
        case 'rqShowList' :
        case 'rqShowReport' :
        case 'rqCreateReport':
        case 'rqEditReport' :
        case 'rqDeleteReport':
        case 'exExport2xml' :
        case 'exExport2csv' :
        case 'exExport2pdf' :
        {
            break;
        }
        
        case 'exEditReport' :
        {
            $weightList = $userInput->get( 'weight' );
            $activeList = $userInput->get( 'active' );
            
            foreach( array_keys( $report->getAssignmentDataList() ) as $assignmentId )
            {
                $weight = abs( (int)$weightList[ $assignmentId ] );
                $active = isset( $activeList[ $assignmentId ] );
                $report->setWeight( $assignmentId , $weight );
                $report->setActive( $assignmentId , $active );
            }
            
            $report_saved = $report->saveAssignmentList();
            break;
        }
        
        case 'exCreateReport' :
        {
            $title = $userInput->get( 'title' );
            $report->setTitle( $title );
            $report_saved = $report->saveReport();
            $reportId = $report->getId();
            break;
        }
        
        case 'exDeleteReport' :
        {
            $report_deleted = $report->delete();
            break;
        }
        
        case 'exChangeVisibility' :
        {
            $visibility = $userInput->get( 'visibility' );
            $visibility_changed = $report->changeVisibility( $reportId , $visibility );
            break;
        }
        
        default :
        {
            throw new Exception( 'bad command' );
        }
    }
    
    // VIEW
    CssLoader::getInstance()->load( 'ucreport' , 'screen' );
    
    $userList = $report->getUserList();
    $assignmentDataList = $report->getAssignmentDataList();
    $reportDataList = $report->getReportDataList();
    $averageScore = $report->getAverageScore();
    
    switch( $cmd )
    {
        case 'rqShowList' :
        case 'rqDeleteReport' :
        case 'exDeleteReport' :
        case 'exChangeVisibility' :
        {
            $pageTitle[ 'subTitle' ] = get_lang( 'Report list' );
            
            $reportView = new PhpTemplate( dirname( __FILE__ ) . '/templates/reportlist.tpl.php' );
            $reportView->assign( 'reportList' , Report::getReportList( claro_is_allowed_to_edit() ) );
            
            if ( $cmd == 'rqDeleteReport' )
            {
                $question = new PhpTemplate( dirname( __FILE__ ) . '/templates/question.tpl.php' );
                $question->assign( 'msg' , get_lang( 'Do you really want to delete this report?' ) );
                $question->assign( 'urlAction' , 'exDeleteReport' );
                $question->assign( 'reportId' , $reportId );
                $dialogBox->question( $question->render() );
            }
            elseif ( $cmd == 'exdeleteReport' )
            {
                if ( $report_deleted )
                {
                    $dialogBox->success( get_lang( 'The report has beeen successfully deleted!' ) );
                }
                else
                {
                    $dialogBox->error( '<strong>' . get_lang( 'An error occured: the report has not been deleted!' . '</strong>' ) );
                }
            }
            elseif ( $cmd == 'exChangeVisibility' )
            {
                if ( $visibility_changed )
                {
                    $dialogBox->success( get_lang( 'The visibility has been changed.' ) );
                }
                else
                {
                    $dialogBox->error( '<strong>' . get_lang( 'An error occured: the visibility change failed!' ) . '</strong>' );
                }
            }
            break;
        }
        
        case 'rqShowReport' :
        case 'rqCreateReport' :
        case 'exCreateReport' :
        {
            if ( $reportId )
            {
                $pageTitle[ 'subTitle' ] = $report->getTitle();
            }
            else
            {
                $pageTitle[ 'subTitle' ] = get_lang( 'Current datas' );
            }
            
            $reportView = new PhpTemplate( dirname( __FILE__ ) . '/templates/report.tpl.php' );
            $reportView->assign( 'reportId' , $reportId );
            $reportView->assign( 'reportDataList' , $reportDataList );
            $reportView->assign( 'userList' , $userList );
            $reportView->assign( 'assignmentDataList' , $assignmentDataList );
            $reportView->assign( 'averageScore' , $averageScore );
            
            if ( $cmd == 'rqCreateReport' )
            {
                $form = new PhpTemplate( dirname( __FILE__ ) . '/templates/form.tpl.php');
                $dialogBox->form( $form->render() );
            }
            
            if ( $cmd == 'exCreateReport' )
            {
                if ( $report_saved )
                {
                    $dialogBox->success( get_lang( 'The report has been successfully created!' ) );
                }
                else
                {
                    $dialogBox->error( '<strong>' . get_lang( 'An error occured: the report has not been created!' ) . '</strong>' );
                }
            }
            break;
        }
        
        case 'rqEditReport' :
        case 'exEditReport' :
        {
            $pageTitle[ 'subTitle' ] = get_lang( 'Report settings' );
            
            $reportView = new PhpTemplate( dirname( __FILE__ ) . '/templates/editreport.tpl.php' );
            $reportView->assign( 'assignmentDataList' , $assignmentDataList );
            
            if ( $cmd == 'exEditReport' )
            {
                if ( $report_saved )
                {
                    $dialogBox->success( get_lang( 'Your modifications have been successfully saved!' ) );
                }
                else
                {
                    $dialogBox->error( '<strong>' . get_lang( 'Error while saving the modifications' ) . '</strong>' );
                }
            }
            break;
        }
        
        case 'exExport2xml' :
        {
            $reportXml = new PhpTemplate( dirname( __FILE__ ) . '/templates/excel.xml.tpl.php' );
            $reportXml->assign( 'reportDataList' , $reportDataList );
            $reportXml->assign( 'userList' , $userList );
            $reportXml->assign( 'assignmentDataList' , $assignmentDataList );
            $reportXml->assign( 'courseData' , claro_get_current_course_data() );
            $reportXml->assign( 'userData' , claro_get_current_user_data() );
            $reportXml->assign( 'averageScore' , $averageScore );
            header("Content-type: application/xml");
            header('Content-Disposition: attachment; filename="report'
                   . claro_get_current_course_id()
                   . '.xlsx"');
            echo claro_utf8_encode( $reportXml->render() );
            exit;
        }
        
        case 'exExport2csv' :
        {
            $csv = new Report2Csv();
            $csv->loadDataList( $report );
            header("Content-type: application/csv");
            header('Content-Disposition: attachment; filename="report'
                   . claro_get_current_course_id()
                   . '.csv"');
            echo claro_utf8_encode( $csv->export() );
            exit;
        }
        
        case 'exExport2pdf' :
        {
            $reportPdf = new PhpTemplate( dirname( __FILE__ ) . '/templates/report.pdf.tpl.php' );
            $reportPdf->assign( 'reportDataList' , $reportDataList );
            $reportPdf->assign( 'userList' , $userList );
            $reportPdf->assign( 'assignmentDataList' , $assignmentDataList );
            $reportPdf->assign( 'averageScore' , $averageScore );
            $reportPdf->assign( 'courseData' , claro_get_current_course_data() );
            
            $pdf = new TCPDF( 'L' , 'mm' , 'A4' , true , 'UTF-8' , false);
            $pdf->setTitle( claro_utf8_encode( 'Report_' . claro_get_current_course_id() ) );
            $pdf->SetSubject( claro_utf8_encode( 'Report_' . claro_get_current_course_id() ) );
            
            $pdf->AddPage();
            $pdf->writeHTML( claro_utf8_encode( $reportPdf->render() ) );
            
            $pdf->Output( 'Report_' . claro_get_current_course_id() . '.pdf' , 'D' );
            exit;
        }
        
        default :
        {
            throw new Exception( 'bad command' );
        }
    }
    
    ClaroBreadCrumbs::getInstance()->append( get_lang( 'Student Report' )
                                           , htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] ) ) );
    Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle )
                                                            . $dialogBox->render()
                                                            . $reportView->render() );
}
else
{
    $dialogBox->error( get_lang( 'Access denied' ) );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();