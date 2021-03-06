<?php // $Id$

class ICADDEXT_Importer
{
    public static $required_fields = array(
          'prenom' => 'prenom'
        , 'nom' => 'nom'
        , 'email' => 'email'
        , 'date_naissance' => 'date_naissance' );
    
    public static $allowed_fields = array(
          'username' => 'username'
        , 'password' =>'password'
        , 'officialCode' => 'officialCode'
        , 'officialCodePrefix' => 'officialCodePrefix'
        , 'officialEmail' => 'officialEmail'
        , 'phoneNumber' => 'phoneNumber'
        , 'institution' => 'institution'
        , 'annee_etude' => 'annee_etude'
        //, 'date_naissance' => 'date_naissance'
        , 'remarques' => 'remarques'
        , 'authSource' => 'authSource' );
    
    public static $user_tbl_fields = array(
          'nom' => 'nom'
        , 'prenom' => 'prenom'
        , 'username' => 'username'
        , 'password' => 'password'
        , 'language' => 'language'
        , 'authSource' => 'authSource'
        , 'email' => 'email'
        , 'officialCode' => 'officialCode'
        , 'officialEmail' => 'officialEmail'
        , 'phoneNumber' => 'phoneNumber'
        , 'pictureUri' => 'pictureUri'
        , 'creatorId' => 'creatorId'
        , 'isPlatformAdmin' => 'isPlatformAdmin'
        , 'isCourseCreator' => 'isCourseCreator' );
    
    public static $user_added_tbl_fields = array(
          'actif' => 'actif'
        , 'mail_envoye' => 'mail_envoye'
        , 'user_id' => 'user_id'
        , 'nom' => 'nom'
        , 'prenom' => 'prenom'
        , 'email' => 'email'
        , 'date_naissance' => 'date_naissance'
        , 'phoneNumber' => 'phoneNumber'
        , 'institution' => 'institution'
        , 'annee_etude' => 'annee_etude'
        , 'officialCode' => 'officialCode'
        , 'date_ajout' => 'date_ajout'
        , 'remarques' => 'remarques' );
    
    public static $display_fields = array(
          'nom' => 'nom'
        , 'prenom' => 'prenom'
        , 'email' => 'email'
        , 'officialCode' => 'officialCode'
        , 'username' => 'username'
        , 'password' => 'password' );
    
    public static $default_fields = array(
          'authSource' => 'external'
        , 'isPlatformAdmin' => 0
        , 'isCourseCreator' => 0
        , 'officialCodePrefix' => 'EXT' );
    
    public static $mail_infos = array(
          'prenom' => 'firstname'
        , 'nom' => 'lastname'
        , 'username' => 'username'
        , 'password' => 'password'
        , 'email' => 'email' );
    
    public $csvParser;
    
    public $codeIncrement;
    
    public $output = array();
    
    public $toAdd = array();
    
    public $added = array();
    
    public $conflict = array();
    
    public $incomplete = array();
    
    public $invalid = array();
    
    public $autoGen = array();
    
    public $fromForm = false;
    
    public $defaultClassName;
    
    protected $parentClass;
    
    protected $classList;
    
    /**
     * Constructor
     * @param ParseCsv object $csvParser
     */
    public function __construct( $csvParser )
    {
        $this->csvParser = $csvParser;
        
        $tbl = get_module_main_tbl( array( 'user' , 'ICADDEXT_user_added' ) );
        $this->userTbl = $tbl[ 'user' ];
        $this->userAddedTbl = $tbl[ 'ICADDEXT_user_added' ];;
        $this->database = Claroline::getDatabase();
        $this->codeIncrement = $this->_codeIncrement();
        $this->defaultClassName = 'ICADDEXT_' . date( 'Y-m-d H:i' );
        $this->loadClasses();
    }
    
    /**
     * verifies if all is ok
     */
    public function is_ok( $force = false )
    {
        return empty( $this->incomplete )
            && empty( $this->invalid )
            && ( empty( $this->conflict ) || $force );
    }
    
    /**
     * Adds selected users
     */
    public function add( $toAdd , $send_mail = true , $addToClass = null )
    {
        if ( ! is_array( $toAdd ) )
        {
            throw new Exception( 'Invalid data' );
        }
        
        $this->_fillMissingValues( $toAdd );
        
        if( $addToClass )
        {
            $class = new Claro_Class( Claroline::getDatabase() );
            
            if( is_int( $addToClass ) && $addToClass != 0 )
            {
                $class->load( $addToClass );
            }
            elseif( is_string( $addToClass ) && strlen( $addToClass ) != 0 )
            {
                $class->setParentId( $this->parentClass->getId() );
                $class->setName( $addToClass );
                $class->create();
            }
            else
            {
                throw new Exception( 'Invalid class datas' );
            }
            
            $classList = new Claro_ClassUserList( $class , Claroline::getDatabase() );
        }
        
        foreach( $this->csvParser->data as $userData )
        {
            if( $this->_insert( $userData , 'user' ) )
            {
                $userData[ 'user_id' ] = $this->database->insertId();
                $this->_insert( $userData , 'user_added' );
                $this->added[] = $userData;
                
                if( $addToClass )
                {
                    $classList->addUserId( $userData[ 'user_id' ] );
                }
                
                if( $send_mail )
                {
                    if( user_send_registration_mail( $userData[ 'user_id' ] , self::_mailInfos( $userData ) ) )
                    {
                        $this->database->exec( "
                            UPDATE
                                `{$this->userAddedTbl}`
                            SET
                                actif = 1,
                                mail_envoye = 1
                            WHERE
                                user_id = " . $userData[ 'user_id' ] );
                    }
                    else
                    {
                        $this->output[ 'mail_failed' ][] = $userData[ 'email' ];
                    }
                }
            }
            else
            {
                $this->output[ 'failed' ][] = $userData[ 'username' ];
            }
        }
        
        return ( ! empty( $this->added ) );
    }
    
    /**
     *
     */
    public function getReport()
    {
        if( ! empty( $this->added ) )
        {
            foreach( array_keys( $this->added ) as $index )
            {
                unset( $this->added[ $index ][ 'remarques' ] );
            }
            
            return $this->csvParser->unparse( $this->added
                                            , array_keys( $this->added[ 0 ] ) );
        }
    }
    
    /**
     * Check submitted data
     */
    public function probe( $data = null )
    {
        if( ! empty( $data ) )
        {
            $this->csvParser->data = $data;
            $this->_cleanValues();
            $this->csvParser->titles = array_keys( pos( $data ) );
        }
        
        if( $this->_checkRequiredFields() )
        {
            $this->_checkMissingValues();
            $this->_checkInvalidMails();
            $this->_trackDuplicates();
            $this->_fillMissingValues();
            $this->_toAdd();
        }
    }
    
    /**
     *
     */
    public function isAutoGen( $field , $user )
    {
        return array_key_exists( $field , $this->autoGen )
            && in_array( $user , $this->autoGen[ $field ] );
    }
    
    /**
     *
     */
    public function getClasses()
    {
        return $this->classList;
    }
    
    /**
     *
     */
    private function loadClasses()
    {
        $this->parentClass = new Claro_Class( Claroline::getDatabase() );
        
        try
        {
            $this->parentClass->loadByName( 'ICADDEXT_classes' );
        }
        catch( Exception $e )
        {
            $this->parentClass->setName( 'ICADDEXT_classes' );
            $this->parentClass->create();
        }
        
        try
        {
            $classList = $this->parentClass->getSubClassesIterator();
            
            $this->classList = array();
            
            foreach( $classList as $class )
            {
                $this->classList[ $class->getId() ] = $class->getName();
            }
        }
        catch( Exception $e )
        {
            $this->classList = array();
        }
    }
    
    /**
     *
     */
    private function _toAdd()
    {
        $this->toAdd = $this->csvParser->data;
        
        if( ! empty( $this->incomplete ) )
        {
            $this->toAdd = array_diff_key( $this->toAdd , $this->incomplete );
            $this->invalid = array_diff_key( $this->invalid , $this->incomplete );
        }
        
        if( ! empty( $this->invalid ) )
        {
            $this->toAdd = array_diff_key( $this->toAdd , $this->invalid );
            $this->conflict = array_diff_key( $this->conflict , $this->invalid );
        }
        
        if( ! empty( $this->conflict ) )
        {
            $this->toAdd = array_diff_key( $this->toAdd , $this->conflict );
        }
    }
    
    /**
     * Checks for existence of required fields
     */
    private function _checkRequiredFields()
    {
        foreach( self::$required_fields as $required_field )
        {
            if( ! in_array( $required_field , $this->csvParser->titles ) )
            {
                $this->output[ 'missing_fields' ][] = $required_field;
            }
        }
        
        /*if( $this->fromForm && ! in_array( 'date_naissance' , $this->csvParser->titles ) )
        {
            $this->output[ 'missing_fields' ][] = 'date_naissance';
        }*/
        
        return empty( $this->output );
    }
    
    /**
     * Checks for lines with missing values
     */
    private function _checkMissingValues()
    {
        foreach( $this->csvParser->data as $index => $userData )
        {
            foreach( self::$required_fields as $required_field )
            {
                if( ! array_key_exists( $required_field , $userData ) || empty( $userData[ $required_field ] ) )
                {
                    $this->output[ 'missing_values' ][ $index ] = $required_field;
                    $this->incomplete[ $index ] = $required_field;
                }
            }
        }
    }
    
    /**
     * Checks for invalid values
     */
    private function _checkInvalidMails()
    {
        foreach( $this->csvParser->data as $index => $userData )
        {
            if( ! ( array_key_exists( 'missing_values' , $this->output )
                   && array_key_exists( $index , $this->output[ 'missing_values' ] ) )
                && ! self::is_mail( trim( $userData[ 'email' ] ) ) )
            {
                $this->output[ 'invalid_mail' ][ $index ] = $userData[ 'nom' ] . ' (' . $userData[ 'email' ] . ')';
                $this->invalid[ $index ] = $index;
            }
        }
    }
    
    /**
     * Checks for duplicates: firstname+lastname, username or mail
     * then moves the incriminated lines in a separated array
     */
    private function _trackDuplicates()
    {
        foreach( $this->csvParser->data as $index => $userData )
        {
            $userName = array_key_exists( 'username' , $userData )
                    ? $userData[ 'username' ]
                    : self::username( $userData[ 'prenom' ] , $userData[ 'nom' ] );
            
            if( $this->database->query( "
                SELECT
                    user_id
                FROM
                    `{$this->userTbl}`
                WHERE
                    nom = " . $this->database->quote( $userData[ 'nom' ] ) . "
                AND
                    prenom = " . $this->database->quote( $userData[ 'prenom' ] )
                )->numRows() )
            {
                $this->conflict[ $index ][ 'nom' ] = $userData[ 'nom' ];
                $this->conflict[ $index ][ 'prenom' ] = $userData[ 'prenom' ];
            }
            
            if( $this->database->query( "
                SELECT
                    user_id
                FROM
                    `{$this->userTbl}`
                WHERE
                    username = " . $this->database->quote( $userName )
                )->numRows() )
            {
                $this->conflict[ $index ][ 'username' ] = $userName;
            }
            
            if( $this->database->query( "
                SELECT
                    user_id
                FROM
                    `{$this->userTbl}`
                WHERE
                    email = " . $this->database->quote( $userData[ 'email' ] )
                )->numRows() )
            {
                $this->conflict[ $index ][ 'email' ] = $userData[ 'email' ];
            }
            
            if( ! empty( $this->conflict[ $index ] ) )
            {
                $reportLine = $userData[ 'prenom' ] . ' ' . $userData[ 'nom' ];
                $reportLine .= ' (' . implode( ', ' , array_keys( $this->conflict[ $index ] ) ) . ')';
                $this->output[ 'conflict_found' ][] = $reportLine;
            }
        }
    }
    
    /**
     *
     */
    private function _fillMissingValues( $dataList = null )
    {
        if( empty( $dataList ) )
        {
            $dataList = $this->csvParser->data;
        }
        
        $this->csvParser->data = self::_addMissingFields( $dataList );
        
        if( in_array( 'officialCodePrefix' , $this->csvParser->titles ) )
        {
            unset( $this->csvParser->titles[ array_search( 'officialCodePrefix'
                                                          , $this->csvParser->titles ) ] );
        }
    }
    
    /*
     * Adds the missing fields in user's datas
     * - fixed values for 'authSource', 'isPlatformAdmin' and 'isCourseCreator'
     * - generates official code with the following format: XXX-YYYYMMDD-NNN
     * - generates username (if does not exist) in the following format: firstname.lastname
     * @param array $userData
     */
    private function _addMissingFields( $dataList )
    {
        $filledData = array();
        
        foreach( $dataList as $index => $userData )
        {
            $userData = self::flush( $userData );
            
            $userData = array_merge( self::$default_fields , $userData );
            $userData[ 'creatorId' ] = claro_get_current_user_id();
            $userData[ 'date_ajout' ] = date( 'Y-m-d H:i:s' );
            
            if( ! $this->fromForm && ! array_key_exists( 'remarques' , $userData ) )
            {
                $userData[ 'remarques' ] = get_lang( 'from_CSV' );
            }
            
            if( ! array_key_exists( 'officialCode' , $userData ) )
            {
                $userData[ 'officialCode' ] = $userData[ 'officialCodePrefix' ]
                                            . '-'
                                            . date( 'Ymd' )
                                            . '-'
                                            . str_pad( ++$this->codeIncrement , 3 , '0', STR_PAD_LEFT );
                
                $this->autoGen['officialCode'][] = $index;
            }
            elseif( $userData[ 'officialCodePrefix' ] != 'EXT' )
            {
                $userData[ 'officialCode' ] = $userData[ 'officialCodePrefix' ]
                                            . '-'
                                            . $userData[ 'officialCode' ];
            }
            
            if( ! array_key_exists( 'username' , $userData ) )
            {
                $userData[ 'username' ] = self::username( $userData[ 'prenom' ] , $userData[ 'nom' ] );
                
                $this->autoGen['username'][] = $index;
            }
            
            if( ! array_key_exists( 'password' , $userData ) )
            {
                $userData[ 'password' ] = self::mk_password();
                
                $this->autoGen['password'][] = $index;
            }
            
            unset( $userData[ 'officialCodePrefix' ] );
            
            $filledData[ $index ] = $userData;
        }
        
        return $filledData;
    }
    
    /**
     * Counts the number of external users added today
     * @return int
     */
    private function _codeIncrement()
    {
        $addedToday = $this->database->query( "
            SELECT
                officialCode
            FROM
                `{$this->userAddedTbl}`
            WHERE
                officialCode LIKE " . $this->database->quote( '%' . date( 'Ymd' ) . '%' ) );
        
        $codeIncrement = 0;
        
        foreach( $addedToday as $fgs )
        {
            $codeExt = (int)substr( $fgs[ 'officialCode' ] , -3 );
            if( $codeExt > $codeIncrement )
            {
                $codeIncrement = $codeExt;
            }
        }
        
        return $codeIncrement;
    }
    
    /**
     * Insert line in database
     */
    private function _insert( $data , $mode )
    {
        if( $mode == 'user' )
        {
            $tbl = $this->userTbl;
            $fields = self::$user_tbl_fields;
            $encrypt = get_conf('userPasswordCrypted');
        }
        elseif( $mode == 'user_added' )
        {
            $tbl = $this->userAddedTbl;
            $fields = self::$user_added_tbl_fields;
            $encrypt = false;
        }
        else
        {
            throw Exception( 'Invalid argument: $mode = ' . $mode );
        }
        
        $sql = "INSERT INTO `{$tbl}` SET \n";
        
        return $this->database->exec( $sql . self::_sqlString( $data , $fields , $encrypt ) );
    }
    
    private function _cleanValues()
    {
        foreach( $this->csvParser->data as $lineIndex => $line )
        {
            foreach( $line as $index => $value )
            {
                $this->csvParser->data[ $lineIndex ][ $index ] = trim( $value);
            }
        }
    }
    
    static private function _sqlString( $data , $allowed_fields = null , $encrypt = false )
    {
        if( ! $allowed_fields )
        {
            $allowed_fields = self::$allowed_fields;
        }
        
        $sqlArray = array();
        
        foreach( $data as $field => $value )
        {
            if( isset( $allowed_fields[ $field ] ) )
            {
                if( $field === 'password' && $encrypt )
                {
                    $value = md5( $value );
                }
                
                $sqlArray[] = $field . " = " . Claroline::getDatabase()->quote( $value );
            }
        }
        
        if( empty( $sqlArray) )
        {
            throw new Exception( 'Empty data' );
        }
        
        return implode( ",\n" , $sqlArray );
    }
    
    static private function _mailInfos( $data )
    {
        $mailInfos = array();
        
        foreach( self::$mail_infos as $key => $value )
        {
            $mailInfos[$value] = $data[$key];
        }
        
        return $mailInfos;
    }
    
    /**
     * Generate random password with some security inside
     * @param   int $ng number of characters
     * @return  string password
     */
    static public function mk_password( $nb = 8 )
    {
    
        $letter = array();
    
        $letter[0] = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i',
        'j', 'k', 'm', 'n', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A',
        'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J',
        'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'D',
        'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '9',
        '6', '5', '1', '3');
    
        $letter[1] =  array( '@', '!', '(', ')', 'a', 'e', 'i', 'o', 'u', 'y', 'A', 'E',
        'U', 'Y' , '1', '3',  '4', '@', '!', '(', ')' );
    
        $letter[-1] = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k',
        'm', 'n', 'p', 'q', 'r', 's', 't',
        'v', 'w', 'x', 'z', 'B', 'C', 'D', 'F',
        'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P',
        'Q', 'R', 'S', 'T', 'V', 'W', 'X', 'Z',
        '5', '6', '9', '@', '!', 4, 5, 6, 7, 8, 9);
    
        $passwd_str = '';
        $prec     = 1;
        $precprec = -1;
    
        srand( ( double )microtime() * 20001107 );
    
        while( strlen( $passwd_str ) < $nb )
        {
            // To generate the password string we follow these rules : (1) If two
            // letters are consonnance (vowel), the following one have to be a vowel
            // (consonnance) - (2) If letters are from different type, we choose a
            // letter from the alphabet.
    
            $type     = ( $precprec + $prec ) / 2;
            $r        = $letter[ $type ][ array_rand( $letter[$type] , 1 ) ];
            $passwd_str .= $r;
            $precprec = $prec;
            $prec     = in_array( $r, $letter[-1] ) - in_array( $r, $letter[1] );
        }
        
        return $passwd_str;
    }
    
    /**
     * Replaces accented chars with their equivalent unaccented ones
     * @param string $string
     * @return string : the "cleaned up" string
     */
    static public function unaccent( $string )
    {
        return preg_replace( '~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i'
                            , '$1'
                            , htmlentities( claro_utf8_encode( $string ) , ENT_QUOTES , 'UTF-8' ) );
    }
    
    static public function clean( $string )
    {
        $string = str_replace( ' ' , '' , $string );
        $string = str_replace( '\'' , '' , $string );
        $string = str_replace( '"' , '' , $string );
        
        $string = self::unaccent( $string );
        
        if( strlen( $string ) > 12 )
        {
            $string = substr( $string , 0 , 12 );
        }
        
        return strtolower( $string );
    }
    
    /**
     * Generates username
     * @param string $firstName
     * @param string $lastName
     * @return string username
     */
    static public function username( $firstName , $lastName )
    {
        return self::clean( $firstName ) . '.' . self::clean( $lastName );
    }
    
    /**
     * Removes empty fields from an array
     * @param array $array
     * @return array : the cleaned up array
     */
    static public function flush( $array )
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
    
    /**
     * Verifies if mail is valid
     */
    static public function is_mail( $string )
    {
        /* Nasty, but works! ;-)
        
        $strPart = explode( ' ' , $string );
        $nbOk = count( $strPart ) == 1;
        $mlPart = explode( '@' , $strPart[0] );
        $atOk = count( $mlPart ) == 2;
        $unOk = ! empty( $mlPart[0] );
        $dnPart = explode( '.' , $mlPart[1] );
        $dnOk = ! empty( $mlPart[1] )
            && count( $dnPart ) == 2
            && ! empty( $dnPart[0] )
            && ! empty( $dnPart[1] );
        
        return $nbOk && $atOk && $unOk && $dnOk;
        */
        
        if( function_exists( 'filter_var' ) ) // PHP >= 5.2
        {
            if( filter_var( $string , FILTER_VALIDATE_EMAIL ) === false )
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else // PHP < 5.2
        {
            return preg_match( '#^[\w.-_]+@[\w.-_]+\.[a-zA-Z]{2,6}$#' , $string ); 
        }
    }
}
