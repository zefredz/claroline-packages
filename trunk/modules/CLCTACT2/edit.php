<?php // $Id$

/**
 * Claroline Contact Page Generator
 *
 * @version     CLCTACT2 1.0beta $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLCTACT2
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$tlabelReq = 'CLCTACT2';

require dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses( 'utils/input.lib' , 'utils/validator.lib' , 'fileUpload.lib' );

if ( claro_is_platform_admin() )
{
    $contentFileUrl  = dirname( __FILE__ ) . '/content.txt';
    
    $userInput = Claro_UserInput::getInstance();
    
    $userInput->setValidator( 'cmd', new Claro_Validator_AllowedList( array( 'rqEditPage', 'exEditPage' ) ) );
    
    $cmd = $userInput->get( 'cmd' , 'rqEditPage' );
    
    switch ( $cmd )
    {
        case 'rqEditPage' :
        {
            $content = file_exists( $contentFileUrl ) ? implode( file( $contentFileUrl ) ) : '<em>' . get_lang( 'Put your content here' ) . '</em>';
            
            $html = '<h3>' . get_lang( 'Contact Page Editing' ) . '</h3>' .
                    '<form id="editContactPage" action="' .
                    htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] ) ) . '">' .
                    '<input type="hidden" name="cmd" value="exEditPage" />' .
                    claro_html_textarea_editor( 'contactList' , $content , 20 , 80 ) .
                    '<input type="submit" name="save" value="' . get_lang('Ok') . '" />' .
                    claro_html_button(htmlspecialchars( Url::Contextualize( $_SERVER[ 'HTTP_REFERER' ]) ), get_lang('Cancel')) .
                    '</form>';
            
            Claroline::getInstance()->display->body->appendContent( $html );
            
            break;
        }
        
        case 'exEditPage' :
        {
            $contactList = $userInput->get( 'contactList' );
            
            create_file( $contentFileUrl , $contactList );
            
            $html = $contactList . '<p><a class="claroCmd" href="' . get_module_url( 'CLCTACT2' ) . '/edit.php">
            <img src="' . get_icon( 'edit' ) . '" alt="edit contact page" />' . get_lang( 'Edit again' ) . '</a></p>';
            
            $message = new DialogBox();
            
            $message->success( get_lang( 'The changes have been applied.' ) );
            
            Claroline::getInstance()->display->body->appendContent( $message->render() );
            Claroline::getInstance()->display->body->appendContent( $html );
            
            break;
        }
        
        default :
        {
            throw new Exception( 'Invalid command!' );
        }
    }
}
else
{
    $dialogBox = new DialogBox;
    
    $dialogBox->warning( get_lang( 'This section is forbidden for users with no admin right' ) );
    
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

echo Claroline::getInstance()->display->render();