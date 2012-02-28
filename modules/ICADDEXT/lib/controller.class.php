<?php // $Id$

class ICADDEXT_Controller
{
    public $importer;
    protected $status_ok = false;
    
    public $message = null;
    
    /**
     * Contructor
     * @param Importer object $importer
     * @param UserInput object $userInput
     */
    public function __construct( $importer , $userInput )
    {
        $this->importer = $importer;
        $this->userInput = $userInput;
    }
    
    /**
     * Verifies status
     * @return boolean
     */
    public function is_ok()
    {
        return $this->status_ok;
    }
    
    /**
     * Executes command
     */
    public function execute( $cmd )
    {
        if( method_exists( $this , '_' . $cmd ) )
        {
            $this->{'_' . $cmd}();
        }
        else
        {
            $this->message = array( 'type' => 'error'
                                  , 'text' => 'invalid_command' );
        }
    }
    
    private function _rqAdd()
    {
        $this->status_ok = true;
    }
    
    private function _rqSelect()
    {
        if ( isset( $_FILES['CSVFile'] ) )
        {
            $file = $_FILES['CSVFile']['tmp_name'];
            
            if( $this->importer->csvParser->auto( $file ) )
            {
                $userData = $this->importer->csvParser->data;
            }
            else
            {
                $this->message = array( 'type' => 'error' , 'text' => 'invalid_csv' );
            }
        }
        else
        {
            $userData = $this->userInput->get( 'userData' );
            
            if( is_array( $userData ) )
            {
                $data = self::_flush( $userData );
                $this->importer->csvParser->data = array( $data );
                $this->importer->csvParser->titles = array_keys( $data );
            }
            else
            {
                $this->message = array( 'type' => 'error' , 'text' => 'invalid_data' );
            }
        }
        
        $this->importer->probe();
        $this->status_ok = $this->importer->getToAdd();
        
        if( ! $this->status_ok )
        {
            $msg = '';
            
            foreach( $this->importer->output as $error => $data )
            {
                $msg .= '<strong>' . get_lang( $error ) . ' :</strong> ' . implode( ', ' , $data );
            }
            
            $this->message = array( 'type' => 'error' , 'text' => $msg );
        }
    }
    
    private function _exAdd()
    {
        $selected = $this->userInput->get( 'selected' );
        $userData = $this->userInput->get( 'userData' );
        $sendMail = $this->userInput->get( 'sendMail' );
        $toAdd = array_intersect_key( $userData , $selected );
        $this->satus_ok = $this->importer->add( $toAdd , $sendMail );
    }
    
    /**
     * Removes empty fields from an array
     * @param array $array
     * @return array : the cleaned up array
     */
    private static function _flush( $array )
    {
        foreach( $array as $key => $value )
        {
            if( ! $value )
            {
                unset( $array[ $key ] );
            }
        }
        
        return $array;
    }
}