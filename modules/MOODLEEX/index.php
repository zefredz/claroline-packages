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
    'display/layout.lib',
    'thirdparty/pclzip/pclzip.lib'
);

From::Module( 'MOODLEEX' )->uses(
    'moodleex.lib',
    'mbzexporter.class',
    'moodlequiz.class',
    'moodlequestion.class',
    'controller.class'
);

$podcastActivated = is_module_installed_in_course( 'ICPCRDR' , claro_get_current_course_id() );

if( $podcastActivated )
{
    From::Module( 'ICPCRDR' )->uses(
    'podcastcollection.lib',
    'podcastparser.lib'
    );
}

CssLoader::getInstance()->load( 'moodle' , 'screen' , 'print' );

$warningText = '<strong>If you can read this text, set your course language to "French" or "English"!</strong>';

$dialogBox = new DialogBox();
$dialogBox->info( get_lang( '[Module introduction text] %warning' , array( '%warning' => $warningText ) ) );

try
{
    $courseId = claro_get_current_course_id();
    $userInput = Claro_UserInput::getInstance();
    
    $controller = new MOODLEEX_Controller( $userInput , $courseId , $podcastActivated );
    $controller->execute();
    
    $pageTitle = get_lang( 'Moodle resource exporter' );
    
    $template = new ModuleTemplate( 'MOODLEEX' , 'main.tpl.php' );
    $template->assign( 'itemList' , $controller->output() );
    $template->assign( 'warningText' , $warningText );
    $template->assign( 'podcastActivated' , $podcastActivated );
    
    /*if( $cmd == 'exportQuiz' )
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
    elseif( $cmd == 'exportPod' )
    {
        $podcastId = (int)$userInput->get( 'podcastId' );
        $podcast = $podcastCollection->get( $podcastId );
        $podcastParser = new PodcastParser();
        $podcastParser->parseFeed( $podcast[ 'url' ] );
        $videoList = $podcastParser->getItems();
        
        $output = '';
        
        foreach( $videoList as $video )
        {
            $output .= $video->metadata[ 'title' ] . '  :  ' . $video->metadata[ 'link' ] . "\n";
            
        }
        
        header("Content-type: text/plain" );
        header('Content-Disposition: attachment; filename="' . MOODLEEX_clean( $podcast[ 'title' ] ) . '.txt"');
        header('Content-Enoding: UTF-8');
        echo claro_utf8_encode( $output );
        exit();
    }*/
    
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