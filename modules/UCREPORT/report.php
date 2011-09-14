<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.2.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'UCREPORT';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses( 'utils/input.lib'
                , 'utils/validator.lib'
                , 'display/layout.lib'
                , 'thirdparty/tcpdf/tcpdf'
                , 'fileUpload.lib'
                , 'course_user.lib' );
From::Module( 'UCREPORT' )->uses( 'agregator.lib'
                                 ,'assetlist.lib'
                                , 'examination.lib'
                                , 'examinationlist.lib'
                                , 'pluginloader.lib'
                                , 'reportgenerator.lib'
                                , 'reportlist.lib'
                                , 'reportplugin.lib'
                                , 'reportview.lib'
                                , 'csvreportview.lib'
                                , 'selector.lib'
                                , 'storedreport.lib' );

$nameTools = get_lang( 'Student Report' );

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) claro_disp_auth_form( true );

$userInput = Claro_UserInput::getInstance();
$is_allowed_to_edit = claro_is_allowed_to_edit();

if ( $is_allowed_to_edit )
{
    $userInput->setValidator( 'cmd' ,
        new Claro_Validator_AllowedList( array( 'rqShowList',
                                                'rqView',
                                                'rqCreate',
                                                'exGenerate',
                                                'exActivate',
                                                'exReset',
                                                'rqPublish',
                                                'exPublish',
                                                'rqDelete',
                                                'exDelete',
                                                'exMkVisible',
                                                'exMkInvisible',
                                                'exExport2xml',
                                                'exExport2csv',
                                                'exExport2pdf',
                                                'exReport2xml',
                                                'exReport2csv',
                                                'exReport2pdf' ) ) );
}
elseif ( claro_is_course_member() )
{
    $userInput->setValidator( 'cmd' ,
        new Claro_Validator_AllowedList( array( 'rqShowList',
                                                'rqView',
                                                'exReport2pdf' ) ) );
}
else
{
    $userInput->setvalidator( 'cmd' ,
        new Claro_Validator_AllowedList( array( 'rqShowList' ) ) );
}

$dialogBox = new DialogBox();

try
{
    $cmd = $userInput->get( 'cmd', 'rqShowList' );
    $id = $userInput->get( 'id' );
    $userId = claro_get_current_user_id();
    $is_allowed_to_edit = claro_is_allowed_to_edit();
    
    if ( ! $id )
    {
        $pluginLoader = new PluginLoader( 'lib/plugins/' );
        $pluginLoader->loadPlugins();
    }
    
    $reportList = new ReportList();
    
    // CONTROLLER
    switch( $cmd )
    {
        case 'rqView':
        case 'rqDelete':
        case 'exReport2xml':
        case 'exReport2csv':
        case 'exReport2pdf':
        {
            $report = new StoredReport( $id , claro_get_current_course_id() );
            break;
        }
        
        case 'rqShowList':
        {
            unset( $_SESSION[ 'item_list' ] );
            unset( $_SESSION[ 'user_list' ] );
            break;
        }
        
        case 'rqCreate':
        {
            $selector = new Selector( $pluginLoader->getPLuginList() );
            break;
        }
        
        case 'exGenerate':
        case 'exActivate':
        case 'exReset':
        case 'rqPublish':
        case 'exPublish':
        case 'exExport2xml':
        case 'exExport2csv':
        case 'exExport2pdf':
        {
            $active = $userInput->get( 'active' );
            $userToActivate = $userInput->get( 'userId' );
            $itemList = $userInput->get( 'item' );
            $title = $userInput->get( 'title' );
            
            if ( $itemList )
            {
                $_SESSION[ 'item_list' ] = $itemList;
            }
            elseif ( isset( $_SESSION[ 'item_list' ] ) )
            {
                $itemList = $_SESSION[ 'item_list' ];
            }
            
            if ( isset( $_SESSION[ 'user_list' ] ) && $cmd != 'exReset' )
            {
                $userList = $_SESSION[ 'user_list' ];
            }
            else
            {
                $userList = claro_get_course_user_list();
            }
            
            $reset = $cmd == 'exReset'
                  || ! isset( $_SESSION[ 'item_list' ] )
                  || ! isset( $_SESSION[ 'user_list' ] );
            
            $report = new Agregator( $pluginLoader->getPluginList() , $userList , $itemList , $reset );
            
            if ( $userToActivate )
            {
                $report->setUserActive( $userToActivate , $active );
                $report->load();
                $_SESSION[ 'user_list' ] = $report->getUserList();
            }
            
            if ( $title )
            {
                $reportGenerator = new ReportGenerator( claro_get_current_course_id() , $report->export() );
                $reportGenerator->setTitle( $title );
                $id = $reportGenerator->save();
                $execution_ok = (boolean)$id;
            }
            break;
        }
        
        case 'exDelete' :
        {
            $execution_ok = $reportList->delete( $id );
            break;
        }
        
        case 'exMkVisible':
        case 'exMkInvisible':
        {
            $is_visible = $cmd == 'exMkVisible';
            $execution_ok = $reportList->setVisibility( $id , $is_visible );
            break;
        }
        
        default :
        {
            throw new Exception( 'bad command' );
        }
    }
    
    // VIEW
    CssLoader::getInstance()->load( 'ucreport' , 'screen' );
    $pageTitle = array( 'mainTitle' => $nameTools );
    $comment = isset( $userList[ claro_get_current_user_id() ][ 'comment' ] )
             ? $userList[ claro_get_current_user_id() ][ 'comment' ]
             : false;
    $cmdList = array();
    
    switch( $cmd )
    {
        case 'rqShowList':
        case 'rqDelete':
        case 'exDelete':
        case 'exMkVisible':
        case 'exMkInvisible':
        case 'exPublish':
        {
            $pageTitle[ 'subTitle' ] = get_lang( 'Report list' );
            
            $reportView = new ModuleTemplate( 'UCREPORT' , 'report_list.tpl.php' );
            $reportView->assign( 'reportList' , $reportList->getList() );
            $reportView->assign( 'is_allowed_to_edit' , $is_allowed_to_edit );
            
            if ( $cmd == 'rqDelete' )
            {
                $dialog = 'question';
                $message = get_lang( 'Do you really want to delete this report?' );
                $urlAction = 'exDelete';
                $urlCancel = 'rqShowList';
                $xid = array( 'id' => $id );
            }
            
            if ( $cmd == 'exDelete' )
            {
                $dialog = 'status';
                $message = $execution_ok ? get_lang( 'The report has beeen successfully deleted!' )
                                         : '<strong>' . get_lang( 'An error occured: the report has not been deleted!' ) . '</strong>';
            }
            
            if ( $cmd == 'exPublish' )
            {
                $dialog = 'status';
                $message = $execution_ok ? get_lang( 'The report has been successfully created!' )
                                         : '<strong>' . get_lang( 'An error occured: the report has not been created!' ) . '</strong>';
            }
            
            if ( $is_allowed_to_edit )
            {
                $cmdList[] = array( 'img'  => 'new_exam',
                                    'name' => get_lang( 'Create a new report' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqCreate') ) );
            }
            
            $cmdList[] = array( 'img'  => 'exam',
                    'name' => get_lang( 'Examinations' ),
                    'url'  => 'examination.php' );
            break;
        }
        
        case 'rqCreate':
        {
            $pageTitle[ 'subTitle' ] = get_lang( 'Items selection' );
            $reportView = new ModuleTemplate( 'UCREPORT' , 'selector.tpl.php' );
            $reportView->assign( 'itemList' , $selector->getItemList() );
            
            $cmdList[] = array( 'img'  => 'go_left',
                    'name' => get_lang( 'Back to the report list' ),
                    'url'  => 'report.php' );
            break;
        }
        
        case 'rqView':
        case 'exGenerate':
        case 'exActivate':
        case 'exReset':
        case 'rqPublish':
        {
            if ( $id )
            {
                $pageTitle[ 'subTitle' ] = $report->getTitle();
            }
            else
            {
                $pageTitle[ 'subTitle' ] = get_lang( 'Gathering datas' );
            }
            
            $reportView = new ModuleTemplate( 'UCREPORT' , 'report.tpl.php' );
            $reportView->assign( 'id' , (int)$id );
            $reportView->assign( 'datas' , $report->export() );
            
            if ( $cmd == 'rqPublish' )
            {
                $dialog = 'form';
                $message = get_lang( 'Choose a title' );
                $urlAction = 'exPublish';
                $urlCancel = 'exGenerate';
                $xid = array( 'title' => 'text' );
            }
            
            $cmdList[] = array( 'img'  => 'go_left',
                    'name' => get_lang( 'Back to the report list' ),
                    'url'  => 'report.php' );
            
            if ( $is_allowed_to_edit )
            {
                if ( ! $id )
                {
                    $cmdList[] = array( 'img'  => 'export_list',
                                        'name' => get_lang( 'Publish the report' ),
                                        'url'  => htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqPublish') ) );
                }
                
                $cmdList[] = array( 'img'  => 'export',
                                    'name' => get_lang( 'Export to MS-Excel xlsx file' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=ex' . $id ? 'Re' : 'Ex' . 'port2xml&id=' . $id ) ) );
                
                $cmdList[] = array( 'img'  => 'export',
                                    'name' => get_lang( 'Export to csv' ),
                                    'url'  => htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=ex' . $id ? 'Re' : 'Ex' . 'port2csv&id=' . $id ) ) );
            }
            
            $cmdList[] = array( 'img'  => 'export',
                                'name' => get_lang( 'Export to pdf' ),
                                'url'  => htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=ex' . $id ? 'Re' : 'Ex' . 'port2pdf&id=' . $id ) ) );
            break;
        }
        
        case 'exExport2xml' :
        case 'exReport2xml' :
        {
            $reportXml = new ModuleTemplate( 'UCREPORT' , 'excel.xml.tpl.php' );
            $reportXml->assign( 'datas' , $report->export() );
            $reportXml->assign( 'courseData' , claro_get_current_course_data() );
            $reportXml->assign( 'userData' , claro_get_current_user_data() );
            $reportXml->assign( 'date' , $report->getDate() );
            header("Content-type: application/xml");
            header('Content-Disposition: attachment; filename="report_'
                   . claro_get_current_course_id()
                   . '.xlsx"');
            echo claro_utf8_encode( $reportXml->render() );
            exit;
        }
        
        case 'exExport2csv' :
        case 'exReport2csv' :
        {
            $csv = new CsvReportView( $report , $userId , $is_allowed_to_edit , ',' );
            $csv->export( 'report_' . claro_get_current_course_id() . '.csv' );
            exit;
        }
        
        case 'exExport2pdf' :
        case 'exReport2pdf' :
        {
            $reportPdf = new ModuleTemplate( 'UCREPORT' , 'report.pdf.tpl.php' );
            $reportPdf->assign( 'datas' , $report->export() );
            $reportPdf->assign( 'courseData' , claro_get_current_course_data() );
            
            $pdf = new TCPDF( 'L' , 'mm' , 'A4' , true , 'UTF-8' , false);
            $pdf->setTitle( claro_utf8_encode( 'Report_' . claro_get_current_course_id() ) );
            $pdf->SetSubject( claro_utf8_encode( 'Report_' . claro_get_current_course_id() ) );
            
            $pdf->AddPage();
            $pdf->writeHTML( claro_utf8_encode( $reportPdf->render() ) );
            
            $pdf->Output( 'report_' . claro_get_current_course_id() . '.pdf' , 'D' );
            exit;
        }
        
        default :
        {
            throw new Exception( 'bad command' );
        }
    }
    
    if ( isset( $dialog ) )
    {
        if ( $dialog == 'status' )
        {
            if ( ! isset( $message ) )
            {
                $message = $execution_ok ? get_lang( 'Success' )
                                         : get_lang( 'Action failed' );
            }
            
            $execution_ok ? $dialogBox->success( $message )
                          : $dialogBox->error( $message );
        }
        else
        {
            $boxTemplate = new ModuleTemplate( 'UCREPORT' , $dialog . '.tpl.php');
            $boxTemplate->assign( 'message' , $message );
            $boxTemplate->assign( 'urlAction' , $urlAction );
            $boxTemplate->assign( 'urlCancel' , $urlCancel );
            $boxTemplate->assign( 'xid' , $xid );
            $dialogBox->question( $boxTemplate->render() );
        }
    }
    
    ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'subTitle' ]
                                           , htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] ) ) );
    Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $pageTitle , '../../module/UCREPORT/help.php' , $cmdList )
                                                          . $dialogBox->render()
                                                          . $reportView->render() );
}
catch( Exception $e )
{
    if ( claro_debug_mode() )
    {
        $errorMsg = '<pre>' . $e->__toString() . '</pre>';
    }
    else
    {
        $errorMsg = $e->getMessage();
    }
    
    $dialogBox->error( '<strong>' . get_lang( 'Error' ) . ' : </strong>' . $errorMsg );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();