<?php // $Id$
/**
 * CLSURVEY 1.1 $Revision$
 *
 * @version 1.0.0
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLSURVEY
 *
 * @author Christophe Gesché <moosh@claroline.net>
 * @author Philippe Dekimpe <dkp@ecam.be>
 * @author Claro Team <cvs@claroline.net>
 * @author Frederic Fervaille <frederic.fervaille@uclouvain.be>
 *
 */

$tlabelReq = 'CLSURVEY';

require dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

From::Module( 'CLSURVEY' )->uses( 'survey.lib' );
FromKernel::uses( 'utils/input.lib' , 'utils/validator.lib' );

if ( ! claro_is_in_a_course() || ! claro_is_course_allowed() ) claro_disp_auth_form( true );

$dialogBox = new DialogBox();

if ( ! claro_is_course_admin() )
{
    $dialogBox->error( get_lang( 'Not allowed' ) );
}
else
{
    $userInput = Claro_UserInput::getInstance();
    
    $userInput->setValidator( 'cmd' , new Claro_Validator_AllowedList( array( 'rqImport' , 'exImport' ) ) );
    
    $cmd = $userInput->get( 'cmd' , 'rqImport' );
    
    switch ( $cmd )
    {
        case 'rqImport':
        {
            $form = '<form id="importSurveyForm" action="'
            . claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] .'?cmd=exImport' ) )
            .'" enctype="multipart/form-data" method="post">'  . "\n"
            . '    <h4>' . get_lang( 'Title' ) . '</h4>'
            . '    <input id="surveyTitle" type="text" name="title" style="width: 330px;"/><br />'
            . '    <h4>' . get_lang( 'Description ') . '</h4>'
            . '    <textarea id="surveyDescription" name="description" rows="8" cols="40"></textarea>'
            . '    <h4>' . get_lang( 'Select a file to import' ) . '</h4>'
            . '    <input type="file" name="CSVfile" /><br/>' . "\n"
            . '    <input type="submit" name="submitCSV" value="' . get_lang( 'Import' ) . '" />' . "\n"
            .      claro_html_button( claro_htmlspecialchars( Url::Contextualize( 'survey_list.php' ) ) , get_lang( 'Cancel' ) )  . "\n"
            . '</form>';
            
            $dialogBox->form( $form );
            
            break;
        }
        
        case 'exImport':
        {
            $title = $userInput->get( 'title' );
            $description = $userInput->get( 'description' );
            
            if( !isset( $_FILES['CSVfile'] ) || empty( $_FILES['CSVfile']['name'] ) || $_FILES['CSVfile']['size'] == 0 )
            {
                $dialogBox->error( get_lang( 'You must select a file' ) );
            }
            elseif( createSurvey( $title , $description , importSurvey( $_FILES['CSVfile']['tmp_name'] ) ) )
            {
                $dialogBox->success( get_lang( 'Import done' ) );
            }
            else
            {
                $dialogBox->error( get_lang( 'Import failed' ) );
            }
            break;
        }
        
        default:
        {
            throw new Exception ( 'Invalid command' );
        }
    }
}

ClaroBreadCrumbs::getInstance()->append( get_lang( 'Survey' ) , claro_htmlspecialchars( Url::Contextualize( 'index.php' ) ) );
ClaroBreadCrumbs::getInstance()->append( get_lang( 'Import' ) , claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) );

Claroline::getInstance()->display->body->appendContent( '<h3>' . get_lang( 'Import survey' ) . '</h3>' );
Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );

echo Claroline::getInstance()->display->render();
