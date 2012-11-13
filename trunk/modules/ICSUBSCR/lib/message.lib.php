<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.1 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Message extends DialogBox
{
    const SUCCESS = 'success';
    const ERROR = 'error';
    const INFO = 'info';
    const QUESTION = 'question';
    const WARNING = 'warning';
    
    protected $output = array();
    
    /**
     * Adds a mesage into the output
     * @param string $type : success, error or info
     * @param string $content : the message itself
     * @return void
     */
    public function addMsg( $type , $content , $action = null , $xid = null , $cancel = null )
    {
        $this->validate( $type );
        
        if( $type == self::QUESTION )
        {
            $template = new ModuleTemplate( 'ICSUBSCR' , 'question.tpl.php' );
            $template->assign( 'urlAction' , $action );
            $template->assign( 'xid' , $xid );
            $template->assign( 'urlCancel' , $cancel );
            $content .= $template->render();
        }
        
        $this->output[ $type ][] = $content;
        $this->{$type}( $content );
    }
    
    public function has( $type = self::ERROR )
    {
        $this->validate( $type );
        
        if( array_key_exists( $type , $this->output ) )
        {
            return count( $this->output[ $type ] );
        }
        else
        {
            return 0;
        }
    }
    
    public function hasError()
    {
        return $this->has();
    }
    
    public function hasSuccess()
    {
        return $this->has( self::DIALOG_SUCCESS );
    }
    
    private function validate( $type )
    {
        if( $type != self::SUCCESS
            && $type != self::ERROR
            && $type != self::INFO
            && $type != self::QUESTION
            && $type != self::WARNING )
        {
            throw new Exception( 'Invalid message type');
        }
        
        return true;
    }
}
