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

class Claro_PageLayout implements Display
{
    const ERROR = 'error';
    const SUCCESS = 'success';
    const INFO = 'info';
    const QUESTION = 'question';
    
    protected $title;
    protected $cmdList;
    protected $dialog;
    protected $body;
    
    /**
     * Constructor
     * @param PhpTemplate object $template
     * @param string $title
     * @param array $cmdList
     * @param string $helpUrl
     */
    public function __construct( $title , $template = null , $cmdList = array() , $helpUrl = null )
    {
        $this->body = $template;
        $this->cmdList = $cmdList;
        $this->helpUrl = $helpUrl;
        $this->title = new ToolTitle( $title , $this->helpUrl , &$this->cmdList );
        $this->dialog = new DialogBox;
    }
    
    /**
     * Setter for $this->body
     */
    public function setBody( $templateName )
    {
        $this->body = new ModuleTemplate( $tlabelReq , $templateName . '.tpl.php' );
    }
    
    /**
     * Renders the layout
     * @return string : the html code of the layout
     */
    public function render()
    {
        $output = $this->title->render()
            . $this->dialog->render()
            . $this->body->render();
        
        return $output;
    }
    
    /**
     * Adds a mesage into the dialog area
     * @param string $type : success, error or info
     * @param string $content : the message itself
     * @return void
     */
    public function addMsg( $type , $content )
    {
        if( $type != self::SUCCESS
            && $type != self::ERROR
            && $type != self::INFO
            && $type != self::QUESTION )
        {
            throw new Exception( 'Invalid message type');
        }
        
        $this->dialog->{$type}( $content );
    }
    
    /**
     * Adds a command in the command list
     * @param array $cmd
     * @return void
     */
    public function addCmd( $cmd )
    {
        if( ! is_array( $cmd )
           || ! array_key_exists( 'name' , $cmd )
           || ! array_key_exists( 'url' , $cmd ) )
        {
            throw new Exception( 'Invalid command data' );
        }
        
        $this->cmdList[] = $cmd;
    }
}