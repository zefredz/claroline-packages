<?php // $Id$

class ICADDEXT_Importer
{
    const MODE_PROBE = 'conflict_found';
    const MODE_ADD = 'not_added';
    
    protected static $required_fields = array(
          'prenom'
        , 'nom'
        , 'email' );
    protected static $allowed_fields = array(
          'username'
        , 'password'
        , 'officialCode'
        , 'officialEmail'
        , 'phoneNumber'
        , 'date_naissance'
        , 'institution'
        , 'annee_etude'
        , 'remarques' );
    protected static $user_tbl_fields = array(
          'nom'
        , 'prenom'
        , 'username'
        , 'password'
        , 'language'
        , 'authSource'
        , 'email'
        , 'officialCode'
        , 'officialEmail'
        , 'phoneNumber'
        , 'pictureUri'
        , 'creatorId'
        , 'isPlatformAdmin'
        , 'isCourseCreator' );
    protected static $user_added_tbl_fields = array(
          'actif'
        , 'mail_envoye'
        , 'user_id'
        , 'nom'
        , 'prenom'
        , 'email'
        , 'date_de_naissance'
        , 'institution'
        , 'annee_etude'
        , 'username'
        , 'password'
        , 'officialCode'
        , 'date_ajout'
        , 'remarques' );
    protected static $report_fields = array(
          'nom'
        , 'prenom'
        , 'officialCode'
        , 'email'
        , 'username'
        , 'password'
        , 'user_id' );
    protected static $check_conflict_fields = array(
          'nom'
        , 'prenom'
        , 'email'
        , 'username' );
    protected static $default_fields = array(
          'authSource' => 'external'
        , 'isPlatformAdmin' => 0
        , 'isCourseCreator' => 0
        , 'officialCode' => 'EXT' );
    protected static $mail_infos = array(
          'prenom' => 'firstname'
        , 'nom' => 'lastname'
        , 'username' => 'username'
        , 'password' => 'password'
        , 'email' => 'email' );
    
    protected $codeIncrement;
    
    public $output = array();
    
    public $csvParser;
    
    public $toAdd = array();
    
    public $added = array();
    
    public $conflict = array();
    
    /**
     * Constructor
     * @param ParseCsv object $csvParser
     */
    public function __construct( $csvParser )
    {
        $this->csvParser = $csvParser;
        
        $tbl = get_module_main_tbl( array( 'user'
                                         , 'ICADDEXT_user_added' ) );
        $this->userTbl = $tbl[ 'user' ];
        $this->userAddedTbl = $tbl[ 'ICADDEXT_user_added' ];;
        $this->database = Claroline::getDatabase();
    }
    
    /**
     * Adds selected users
     */
    public function add( $toAdd , $send_mail = true )
    {
        if ( is_array( $toAdd ) )
        {
            $this->csvParser->data = $toAdd;
            $this->csvParser->titles = array_keys( current( $toAdd ) );
        }
        else
        {
            throw new Exception( 'Invalid data' );
        }
        
        if( $this->probe( self::MODE_ADD ) )
        {
            $this->codeIncrement = $this->_codeIncrement();
            
            foreach( $this->toAdd as $userData )
            {
                $userData = $this->_addMissingFields( $userData );
                
                if( $this->_insert( $userData , 'user' ) )
                {
                    $userData[ 'user_id' ] = $this->database->insertId();
                    $this->_insert( $userData , 'user_added' );
                    $this->added[] = $userData;
                    
                    if( $send_mail
                    &&  user_send_registration_mail( $userData[ 'user_id' ] , self::_mailInfos( $userData ) ) )
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
                else
                {
                    $this->output[ 'failed' ][] = $userData[ 'username' ];
                }
            }
        }
        
        return ( ! empty( $this->added ) );
    }
    
    /**
     *
     */
    public function getReport()
    {
        return $this->csvParser->unparse( $this->added
                                        , array_keys( $this->added[ 0 ] ) );
    }
    
    /**
     *
     */
    public function getConflictFields()
    {
        return self::$check_conflict_fields;
        /*return array_intersect( self::$check_conflict_fields
                            ,   $this->csvParser->titles );*/
    }
    
    /**
     *
     */
    public function getNotAdded()
    {
        if( array_key_exists( self::MODE_ADD , $this->output ) )
        {
            return implode( ',' , $this->output[ self::MODE_ADD ] );
        }
    }
    
    /**
     * Calls two private functions
     */
    public function probe( $mode = self::MODE_PROBE )
    {
        return $this->_checkRequiredFields()
            && $this->_trackDuplicates( $mode );
    }
    
    /**
     * Checks for existence of required fields
     * @return array with missing fields
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
        
        return empty( $this->output );
    }
    
    /**
     * Checks for duplicates: firstname+lastname, username or mail
     * then moves the incriminated lines in a separated array
     * @param string $mode self::MODE_PROBE|self::MODE_ADD
     */
    private function _trackDuplicates( $mode )
    {
        foreach( $this->csvParser->data as $index => $line )
        {
            self::flush( $line );
            
            $line[ 'username' ] = array_key_exists( 'username' , $line )
                                ? $line[ 'username' ]
                                : self::username( $line[ 'prenom' ]
                                                , $line[ 'nom' ] );
            
            if( $mode == self::MODE_PROBE
            &&  $this->database->query( "
                SELECT
                    user_id
                FROM
                    `{$this->userTbl}`
                WHERE
                    nom = " . $this->database->quote( $line[ 'nom' ] ) . "
                AND
                    prenom = " . $this->database->quote( $line[ 'prenom' ] )
                )->numRows() )
            {
                $this->conflict[ $index ][ 'nom' ] = $line[ 'nom' ];
                $this->conflict[ $index ][ 'prenom' ] = $line[ 'prenom' ];
            }
            
            if( $this->database->query( "
                SELECT
                    user_id
                FROM
                    `{$this->userTbl}`
                WHERE
                    username = " . $this->database->quote( $line[ 'username' ] )
                )->numRows() )
            {
                $this->conflict[ $index ][ 'username' ] = $line[ 'username' ];
            }
            
            if( $this->database->query( "
                SELECT
                    user_id
                FROM
                    `{$this->userTbl}`
                WHERE
                    email = " . $this->database->quote( $line[ 'email' ] )
                )->numRows() )
            {
                $this->conflict[ $index ][ 'email' ] = $line[ 'email' ];
            }
            
            if( ! empty( $this->conflict[ $index ] ) )
            {
                $reportLine = $line[ 'prenom' ] . ' ' . $line[ 'nom' ];
                
                if( $mode == self::MODE_PROBE )
                {
                    $reportLine .= ' (' . implode( ', ' , array_keys( $this->conflict[ $index ] ) ) . ')';
                }
                
                $this->output[ $mode ][] = $reportLine;
            }
            
            /*
            $result = $this->database->query( "
                SELECT
                    nom,
                    prenom,
                    email,
                    username
                FROM
                    `{$this->userTbl}`
                WHERE
                    ( nom = " . $this->database->quote( $line[ 'nom' ] ) . "
                AND
                    prenom = " . $this->database->quote( $line[ 'prenom' ] ) . " )
                OR
                    email = " . $this->database->quote( $line[ 'email' ] ) . "
                OR
                    username = " . $this->database->quote( $line[ 'username' ] )
            )->fetch( Database_ResultSet::FETCH_ASSOC );
            
            if( ! empty( $result ) )
            {
                $this->conflict[ $index ] = array_intersect_assoc( $line , $result );
                $reportLine = $line[ 'prenom' ] . ' ' . $line[ 'nom' ];
                
                if( $mode == self::MODE_PROBE )
                {
                    $reportLine .= ' (' . implode( ', ' , array_keys( $this->conflict[ $index ] ) ) . ')';
                }
                
                $this->output[ $mode ][] = $reportLine;
            }
            */
        }
        
        $this->toAdd = array_diff_key( $this->csvParser->data , $this->conflict );
        
        return ! empty( $this->toAdd );
    }
    
    /*
     * Adds the missing fields in user's datas
     * - fixed values for 'authSource', 'isPlatformAdmin' and 'isCourseCreator'
     * - generates official code with the following format: XXX-YYYYMMDD-NNN
     * - generates username (if does not exist) in the following format: firstname.lastname
     * @param array $userData
     * @return array : modified arrat
     */
    private function _addMissingFields( $userData )
    {
        $userData = array_merge( self::$default_fields , $userData );
        
        $userData[ 'creatorId' ]     = claro_get_current_user_id();
        $userData[ 'officialCode' ] .= '-'
                                    . date( 'Ymd' )
                                    . '-'
                                    . str_pad( ++$this->codeIncrement , 3 , '0', STR_PAD_LEFT );
        
        if( ! array_key_exists( 'username' , $userData ) )
        {
            $userData[ 'username' ] = self::username( $userData[ 'prenom' ] , $userData[ 'nom' ] );
        }
        
        if( ! array_key_exists( 'password' , $userData ) )
        {
            $userData[ 'password' ] = self::mk_password();
        }
        
        $userData[ 'date_ajout' ] = date( 'Y-m-d H:i:s' );
        
        return $userData;
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
        
        /*return $this->database->query( "
            SELECT
                id
            FROM
                `{$this->userAddedTbl}`
            WHERE
                officialCode LIKE " . $this->database->quote( '%' . date( 'Ymd' ) . '%' )
        )->numRows();*/
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
    
    static private function _sqlString( $data , $allowed_fields = null , $encrypt = false )
    {
        if( ! $allowed_fields )
        {
            $allowed_fields = self::$allowed_fields;
        }
        
        $sqlArray = array();
        
        foreach( $data as $field => $value )
        {
            if( in_array( $field , $allowed_fields ) )
            {
                if( $field == 'password' && $encrypt )
                {
                    $value = md5( $value );
                }
                
                $sqlArray[] = $field . " = '" . $value . "'";
            }
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
        'I', 'U', 'Y' , '1', '3',  '4', '@', '!', '(', ')' );
    
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
        
        if( strlen( $string ) > 12 )
        {
            $string = substr( $string , 0 , 12 );
        }
        
        return strtolower( self::unaccent( $string ) );
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
}