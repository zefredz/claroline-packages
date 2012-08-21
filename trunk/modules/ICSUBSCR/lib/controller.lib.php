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

class ICSUBSCR_Controller
{
    const ERROR = 'error';
    const SUCCESS = 'success';
    const INFO = 'info';
    const QUESTION = 'question';
    
    protected $defaultCmd;
    protected $model;
    protected $view;
    protected $id;
    protected $allowedToEdit;
    
    protected $output = array();
    
    /**
     * Constructor
     * @param Session objet $session
     */
    public function __construct( $model , $id = null , $allowedToEdit = false )
    {
        $this->model = $model;
        $this->id = $id;
        $this->allowedToEdit = $allowedToEdit;
        
        $viewName = substr( get_class( $this ) , 0 , -10 ) . 'View';
        $this->view = new $viewName;
    }
    
    /**
     * Getter for id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Executes command
     * @param string $cmd : the passed command
     * @param array $data : the command's parameters
     * @param string $successMsg
     * @param string $errorMsg
     */
    public function execute( $cmd = null , $id = null , $param = null )
    {
        if( ! $cmd )
        {
            $cmd = $this->defaultCmd;
        }
        
        if( method_exists( $this , $cmd ) )
        {
            if( $id )
            {
                $this->{$cmd}( $id , $param );
            }
            else
            {
                $this->{$cmd}( $param );
            }
        }
        else
        {
            $this->addMsg( self::ERROR , 'Invalid command' );
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
        $view = $this->view->get();
        $view->assign( 'model' , $this->model );
        $view->assign( 'id' , $this->getId() );
        $view->assign( 'dateUtil' , new DateUtil( get_lang( '_date' ) ) );
        
        return $view;
    }
    
    /**
     * Output
     * @return string (html)
     */
    public function output()
    {
        $output = $this->view->getToolTitle()->render();
        
        if( ! empty( $this->output ) )
        {
            $dialogBox = new DialogBox();
            
            foreach( $this->output as $line )
            {
                $dialogBox->{$line['type']}( $line['msg'] );
            }
            
            $output .= $dialogBox->render();
        }
        
        if( $view = $this->getView() )
        {
            $output .= $view->render();
        }
        
        return $output;
    }
    
    /**
     * Adds a mesage into the output
     * @param string $type : success, error or info
     * @param string $content : the message itself
     * @return void
     */
    protected function addMsg( $type , $content )
    {
        if( $type != self::SUCCESS
            && $type != self::ERROR
            && $type != self::INFO
            && $type != self::QUESTION )
        {
            throw new Exception( 'Invalid message type');
        }
        
        $this->output[] = array( 'type' => $type , 'msg' => $content );
    }
}