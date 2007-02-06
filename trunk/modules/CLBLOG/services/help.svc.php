<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package CLBLOG
     */

if ( count( get_included_files() ) == 1 ) die( '---' );

// {{{ SCRIPT INITIALISATION
{ 
    // local variable initialisation
    $isAllowedToEdit = claro_is_allowed_to_edit();
    
    // success dialog
    $dispSuccess            = false;
    $successMsg             = '';
    
    // error dialog
    $dispError              = false; // display error box
    $fatalError             = false; // if set to true, the script ends after 
                                     // displaying the error
    $errorMsg = '';                  // error message to display
    $dispErrorBoxBackButton = false; // display back button on error
    $err                    = '';    // error string 
}
// }}}
// {{{ MODEL
{ 
    $tagInfo = null;
}
// }}}
// {{{ CONTROLLER
{ 
    $about = isset( $_REQUEST['about'] )
        ? trim( $_REQUEST['about'] )
        : 'about'
        ;
        
    $helpPath = $GLOBALS['helpDir'] . '/' . language::current_language() . '/' . $about . '.hlp.thtml';
    $helpPathDef = $GLOBALS['helpDir'] . '/english/' . $about . '.hlp.thtml';
        
    if ( file_exists( $helpPath ) )
    {
        $content = file_get_contents( $helpPath );
    }
    elseif ( file_exists( $helpPathDef ) )
    {
        $content = file_get_contents( $helpPathDef );
    }
    else
    {
        $dispError = true;
        $fatalError = true;
        $errorMsg = get_lang('No help found');
    }
}
// }}}
// {{{ VIEW
{
    $output = '';
    
    $output .= '<h1>'. get_lang('Help')
            .'</h1>' . "\n"
            ;
    
    if ( true == $dispError )
    {
        // display error
        $errorMessage =  '<h2>'
            . ( ( true == $fatalError ) 
                ? get_lang( 'Error (Fatal)' ) 
                : get_lang( 'Error' ) )
            . '</h2>'
            . "\n"
            ;
        
        $errorMessage .= '<p>'
            . htmlspecialchars($errorMsg) . '</p>' 
            . "\n"
            ;
        // display back link    
        // but back to where ???? (in case of fatal error)
        if ( true === $dispErrorBoxBackButton )
        {
            $errorMessage .= '<p><a href="'
                . $_SERVER['PHP_SELF']
                . '?page=list">['.get_lang('Back').']</a></p>'
                . "\n"
                ;
        }
        
        if ( true === $fatalError )
        {
            $output .= MessageBox::FatalError( $errorMessage );
        }
        else
        {
            $output .= MessageBox::Error( $errorMessage );
        }
    }
    
    if ( true === $dispSuccess )
    {
        // display error
        $successMessage =  '<h2>'
            . get_lang( 'Success' )
            . '</h2>'
            . "\n"
            ;
        
        $successMessage .= '<p>'
            . htmlspecialchars($successMsg) . '</p>' 
            . "\n"
            ;
            
        $output .= MessageBox::Success( $successMessage );
    }
    
    // no fatal error
    if ( true != $fatalError )
    {
        $output .= $content;
    }
    else
    {
        // nothing to do
    }
    
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php', 'name' => get_lang("Glossary"));
    $GLOBALS['interbredcrump'][]= array ( 'url' => null, 'name' => get_lang("Help"));
    
    $this->setOutput( $output );
}
// }}}
?>
