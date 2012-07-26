<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

abstract class ICSUBSCR_Controller
{
    const DEFAULT_SUCCESS_MESSAGE = 'success';
    const DEFAULT_ERROR_MESSAGE = 'error';
    
    protected $model;
    
    protected $output = array();
    protected $templatePath = '';
    protected $selectedView = -1;
    
    
    /**
     * Constructor
     * @param Session objet $session
     */
    public function __construct( $model )
    {
        $this->model = $model;
    }
    
    /**
     * Executes command
     * @param string $cmd : the passed command
     * @param array $data : the command's parameters
     * @param string $successMsg
     * @param string $errorMsg
     */
    public function execute( $cmd
        , $data = null
        , $successMsg = self::DEFAULT_SUCCESS_MESSAGE
        , $errorMsg = self::DEFAULT_ERROR_MESSAGE )
    {
        if( method_exists( $this , $cmd ) )
        {
            $this->output = $this->{$cmd}( $data )
                ? array(
                    'type' => 'success',
                    'text' => $successMsg )
                : array(
                    'type' => 'error',
                    'text' => $errorMsg );
        }
        else
        {
            $this->output[] = array(
                'type' => 'error',
                'text' => 'invalid_command' );
        }
    }
    
    /**
     * Gets executed command's output
     * @return array : the output
     */
    public function getMessage()
    {
        return $this->output;
    }
    
    /**
     * Gets view for executed command
     * @return PhpTemplate object
     */
    public function getView()
    {
        if( array_key_exists( $this->selectedView , self::$templateList ) )
        {
            $view = new PhpTemplate( get_module_path( 'ICSUBSCR' )
                . $this->templatePath
                . 'templates/'
                . self::$templateList[ $this->selectedView ] );
            $view->assign( 'model' , $this->model );
            $view->assign( 'controller' , $this->output );
            
            return $view;
        }
    }
    
    /**
     * Output
     * @return string (html)
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
        
        if( $view = $this->getView() )
        {
            $output .= $view->render();
        }
        
        return $output;
    }
}