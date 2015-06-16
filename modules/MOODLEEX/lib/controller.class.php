<?php // $Id$

/**
 * Moodle Resource Exporter
 *
 * @version     MOODLEEX 2.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2015 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     MOODLEEX
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class MOODLEEX_Controller
{
    protected $courseId;
    protected $podcastActivated;
    protected $output;
    protected $message;
    protected $status_ok;
    protected $userInput;
    
    /**
     * Contructor
     * @param Model object $model
     * @param UserInput object $userInput
     */
    public function __construct( $userInput, $courseId , $podcastActivated = false )
    {
        $this->userInput = $userInput;
        $this->courseId = $courseId;
        $this->podcastActivated = $podcastActivated;
        
        $this->status_ok = true;
        
        $this->message = array(
            'info'    => array(),
            'success' => array(),
            'error'   => array()
        );
    }
    
    /**
     * Verifies status
     * @return boolean
     */
    public function is_ok()
    {
        if( empty( $this->message[ 'error' ] ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Executes command
     */
    public function execute()
    {
        $cmd = $this->userInput->get( 'cmd' , 'list' );
        
        if( method_exists( $this , '_' . $cmd ) )
        {
            $this->{'_' . $cmd}();
        }
        else
        {
            $this->message[ 'error' ][] = 'invalid_command';
        }
    }
    
    public function output()
    {
        return $this->output;
    }
    
    public function message()
    {
        return $this->message;
    }
    
    private function _list()
    {
        $itemList = array();
        
        $itemList[ 'quiz' ] = MOODLEEX_get_quiz_list();
        $itemList[ 'document' ] = MOODLEEX_get_document_list();
        
        if( $this->podcastActivated )
        {
            $podcastCollection = new PodcastCollection();
            $itemList[ 'podcast' ] = $podcastCollection->getAll();
        }
        else
        {
            $itemList[ 'podcast' ] = array();
        }
        
        $this->output = $itemList;
    }
    
    private function _export()
    {
        $this->_list();
        $selectedItemList = $this->userInput->get( 'item' );
        $allSelected = $this->userInput->get( 'selectAll' );
        $mbzExporter = new MbzExporter( $this->courseId );
        
    }
    
    private function _archive()
    {
        $this->_list();
    }
}