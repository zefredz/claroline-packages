<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package HTML
     */
    
    class MessageBox
    {
        function FatalError( $message )
        {
            return MessageBox::Message( $message, 'fatalError' );
        }
        
        function Error( $message )
        {
            return MessageBox::Message( $message, 'error' );
        }
        
        function Warning( $message )
        {
            return MessageBox::Message( $message, 'warning' );
        }
        
        function Notice( $message )
        {
            return MessageBox::Message( $message, 'notice' );
        }
        
        function Info( $message )
        {
            return MessageBox::Message( $message, 'info' );
        }
        
        function Success( $message )
        {
            return MessageBox::Message( $message, 'success' );
        }
        
        function Question( $message )
        {
            return MessageBox::Message( $message, 'question' );
        }
        
        function Message( $message, $messageClass = null )
        {
            if ( !empty( $messageClass ) )
            {
                $class = ' class="' . $messageClass . '"';
            }
            else
            {
                $class = '';
            }
            
            $output = '<div' . $class . '>' . "\n"
                . $message
                . '</div>'
                . "\n"
                ;
                
            return MessageBox::Display( $output );
        }
        
        function Display( $message )
        {
            return '<div class="claroMessageBox">' . "\n"
                . $message . "\n"
                . '</div>'
                . "\n"
                ;
        }
    }
?>
