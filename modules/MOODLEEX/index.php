<?php // $Id$

/**
 * Moodle Resource Exporter
 *
 * @version     MOODLEEX 1.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2015 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     MOODLEEX
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'MOODLEEX';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses(
    'user.lib',
    'utils/input.lib',
    'utils/validator.lib',
    'display/layout.lib'
);

From::Module( 'MOODLEEX' )->uses(
    'moodleex.lib',
    'moodlequiz.class',
    'moodlequestion.class'
);

$dialogBox = new DialogBox();

try
{
    $userInput = Claro_UserInput::getInstance();
    $cmd = $userInput->get( 'cmd' );
    $pageTitle = get_lang( 'Excercises exporter' );
    $quizList = MOODLEEX_get_quiz_list();
    
    $template = new ModuleTemplate( 'MOODLEEX' , 'exerciselist.tpl.php' );
    $template->assign( 'quizList' , $quizList );
    
    if( $cmd == 'export' )
    {
        $dialog = new DialogBox();
        
        $quizId = (int)$userInput->get( 'quizId' );
        
        $quizz = new MoodleQuiz(
            $quizId,
            $quizList[ $quizId ][ 'title' ],
            $quizList[ $quizId ][ 'description' ],
            $quizList[ $quizId ][ 'shuffle' ] );
        
        if ( ! $quizz->export() )
        {
            $dialogBox->error( get_lang( 'Export failed' ) );
        }
    }
    
    Claroline::getInstance()->display->body->appendContent(
        claro_html_tool_title( $pageTitle )
        . $dialogBox->render()
        . $template->render() );
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