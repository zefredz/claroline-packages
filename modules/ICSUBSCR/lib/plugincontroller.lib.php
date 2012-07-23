<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

abstract class PluginController
{
    private static $templateList = array( 'subscribe' , 'edit' , 'result' );
    
    protected $output = array();
    protected $selectedView = -1;
    
    
    public function __construct( $session )
    {
        $this->session = $session;
    }
    
    /**
     * Executes command
     */
    public function execute( $cmd , $data = null )
    {
        if( method_exists( $this , $cmd ) )
        {
            $this->output( $this->{$cmd}( $data ) );
        }
        else
        {
            $this->output[] = array(
                'type' => 'error',
                'text' => 'invalid_command' );
        }
    }
    
    public function getMessage()
    {
        return $this->output;
    }
    
    public function getView()
    {
        if( array_key_exists( $this->selectedView , self::$templateList ) )
        {
            $view = new PhpTemplate( get_module_path( 'ICSUBSCR' )
                . '/plugins/icsubscr.' . $label
                . '.plugin/templates/'. self::$templateList[ $this->selectedView ] );
            $view->assign( 'model' , $this->session );
            $view->assign( 'controller' , $this->output );
        }
        
        return $view;
    }
    
    /**
     * Output
     */
    public function output()
    {
        $output = '';
        
        if( ! empty( $this->output ) )
        {
            $dialogBox = new DialogBox();
            
            foreach( $this->output as $type => $msg )
            {
                $dialogBox->{$type}( $msg );
            }
            
            $output = $dialogBox->render();
        }
        
        if( array_key_exists( $this->selectedView , self::$templateList ) )
        {
            $view = new PhpTemplate( get_module_path( 'ICSUBSCR' )
                . '/plugins/icsubscr.' . $label
                . '.plugin/templates/'. self::$templateList[ $this->selectedView ] );
            $view->assign( 'model' , $this->session );
            $view->assign( 'controller' , $this->output );
            
            $output .= $view->render();
        }
        
        return $output;
    }
    
    private function rqShowSession()
    {
        $this->selectedView = 0;
    }
    
    private function exSubcribe( $slotList , $userId = null , $groupId = null )
    {
        $record = new Record( $this->session , $userId , $groupId );
        
        if( ! $record->subscribe( $slotList ) )
        {
            $this->output[] = array( 'error' => 'Cannot save subscription' );
        }
        
        $this->selectedView = 0;
    }
}