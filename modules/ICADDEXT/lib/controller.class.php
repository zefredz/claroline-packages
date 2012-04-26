<?php // $Id$

class ICADDEXT_Controller
{
    public $importer;
    protected $status_ok = true;
    
    public $message = array();
    
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
            $this->_output();
        }
        else
        {
            $this->message[] = array( 'type' => 'error'
                                    , 'text' => 'invalid_command' );
        }
    }
    
    private function _rqAdd()
    {
        return;
    }
    
    private function _rqSelect()
    {
        if ( $_FILES && $_FILES[ 'CsvFile' ][ 'size' ] != 0 )
        {
            $authSource = $this->userInput->get( 'authSource' );
            $codePrefix = $this->userInput->get( 'officialCodePrefix' );
            
            if( $authSource ) ICADDEXT_Importer::$default_fields[ 'authSource' ] = $authSource;
            if( $codePrefix ) ICADDEXT_Importer::$default_fields[ 'officialCodePrefix' ] = $codePrefix;
            
            $file = $_FILES['CsvFile']['tmp_name'];
            
            if( $this->importer->csvParser->auto( $file ) )
            {
                $userData = $this->importer->csvParser->data;
            }
            else
            {
                $this->message[] = array( 'type' => 'error' , 'text' => 'invalid_csv' );
            }
        }
        else
        {
            $userData = $this->userInput->get( 'userData' );
            
            if( is_array( $userData ) )
            {
                $data = ICADDEXT_Importer::flush( $userData[0] );
                $this->importer->csvParser->data = array( $data );
                $this->importer->csvParser->titles = array_keys( $data );
            }
            else
            {
                $this->message[] = array( 'type' => 'error' , 'text' => 'invalid_data' );
            }
        }
        
        $this->importer->probe();
        
        $this->status_ok = $this->importer->toAdd
                        || $this->importer->conflict
                        || $this->importer->incomplete;
    }
    
    private function _exAdd()
    {
        $userData = $this->userInput->get( 'userData' );
        $selected = $this->userInput->get( 'selected' );
        $toForce = $this->userInput->get( 'toForce' );
        $send_mail = $this->userInput->get( 'send_mail' );
        
        if( ! empty( $toForce ) )
        {
            foreach( $toForce as $index => $data )
            {
                foreach( $data as $field => $value )
                {
                    $userData[ $index ][ $field ] = $value;
                }
            }
        }
        
        if( ! empty( $selected ) )
        {
            $toAdd = array_intersect_key( $userData , $selected );
            $this->status_ok = $this->importer->add( $toAdd , $send_mail );
        }
        else
        {
            $this->message[] = array( 'type' => 'error' , 'text' => 'no_user_selected' );
        }
        
        if( $this->importer->getReport() )
        {
            $this->message[] = array( 'type' => 'success' , 'text' => 'success_message' );
        }
        else
        {
            $this->message[] = array( 'type' => 'error' , 'text' => 'no_user_added' );
        }
    }
    
    /**
     * Outputs message
     */
    private function _output()
    {
        if( ! empty( $this->importer->output ) )
        {
            $msg = '';
            
            foreach( $this->importer->output as $error => $data )
            {
                $msg .= '<strong>' . get_lang( $error ) . ' :</strong> ' . implode( ', ' , $data ) . '<br />';
            }
            
            if( ! $msg )
            {
                $msg = 'undefined_error';
            }
            
            $this->message[] = array( 'type' => 'error' , 'text' => $msg );
        }
    }
}