<?php // $Id$

class ICADDEXT_Controller
{
    public $importer;
    
    public $mode;
    public $cmd;
    public $message = array();
    
    protected $status_ok = true;
    
    /**
     * Contructor
     * @param Importer object $importer
     * @param UserInput object $userInput
     */
    public function __construct( $importer , $userInput , $cmd )
    {
        $this->importer = $importer;
        $this->userInput = $userInput;
        $this->cmd = $cmd;
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
    public function execute()
    {
        if( method_exists( $this , '_' . $this->cmd ) )
        {
            $this->{'_' . $this->cmd}();
            $this->_output();
        }
        else
        {
            $this->message[] = array( 'type' => 'error' , 'text' => 'invalid_command' );
        }
    }
    
    /**
     * default command... does nothing
     */
    private function _submit()
    {
        return;
    }
    
    /**
     * CSV file or form submission
     */
    private function _rqFix()
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
                $this->importer->fromForm = true;
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
    
    /**
     * Correcting parsed datas
     */
    private function _exFix()
    {
        $userData = $this->userInput->get( 'userData' );
        $selected = $this->userInput->get( 'selected' );
        $toFix = $this->userInput->get( 'toFix' );
        $send_mail = $this->userInput->get( 'send_mail' );
        
        $userData = array_intersect_key( (array)$userData , (array)$selected );
        $toFix = array_intersect_key( (array)$toFix , (array)$selected );
        
        foreach( $toFix as $index => $data )
        {
            $userData[ $index ] = array_merge( $userData[ $index ] , $data );
        }
        
        if( ! empty( $userData ) )
        {
            $this->importer->probe( $userData );
            $this->status_ok = true;
        }
        else
        {
            $this->message[] = array( 'type' => 'error' , 'text' => 'no_user_selected' );
        }
    }
    
    /**
     * Last verification before adding users
     */
    private function _rqAdd()
    {
        $this->_exFix();
    }
    
    /**
     * Adding users
     */
    private function _exAdd()
    {
        $userData = $this->userInput->get( 'userData' );
        $selected = $this->userInput->get( 'selected' );
        $toForce = $this->userInput->get( 'toForce' );
        $send_mail = $this->userInput->get( 'send_mail' );
        $create_class = $this->userInput->get( 'create_class' );
        $add_to_class = $this->userInput->get( 'add_to_class' );
        $class_name = $this->userInput->get( 'class_name' );
        $class_id = $this->userInput->get( 'class_id' );
        
        if( $create_class && strlen( (string)$class_name ) != 0 )
        {
            $addToClass = (string)$class_name;
        }
        elseif( $add_to_class && (int)$class_id != 0 )
        {
            $addToClass = (int)$class_id;
        }
        else
        {
            $addToClass = null;
        }
        
        if( is_null( $addToClass ) && ( $create_class || $add_to_class ) )
        {
            $this->message[] = array( 'type' => 'error' , 'text' => 'bad_class_data' );
        }
        
        $userData = array_intersect_key( (array)$userData , (array)$selected );
        $toForce = array_intersect_key( (array)$toForce , (array)$selected );
        
        $toAdd = array_merge( $userData , $toForce );
        
        if( ! empty( $toAdd ) )
        {
            $this->status_ok = $this->importer->add( $toAdd , $send_mail , $addToClass );
        }
        else
        {
            $this->message[] = array( 'type' => 'error' , 'text' => 'no_user_selected' );
        }
        
        if( $this->importer->getReport() )
        {
            $this->message[] = array( 'type' => 'success' , 'text' => 'success_message' );
            $this->message[] = array( 'type' => 'info' , 'text' => $send_mail ? 'mail_sent' : 'mail_not_sent' );
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