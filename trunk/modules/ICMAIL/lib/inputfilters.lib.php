<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Description
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Claroline Team <info@claroline.net>
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     PACKAGE_NAME
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    interface InputFilter
    {
        public function isValid( $value );
    }
    
    class ValueTypeFilter implements InputFilter
    {
        protected static $supportedType = array(
            'ctype' => array( 'alnum'
                , 'alpha', 'digit', 'lower'
                , 'upper', 'space', 'xdigit' ),
            'phptype' => array( 'float'
            , 'int', 'string', 'array', 'bool' ) );
            
        public function __construct( $type )
        {
            $this->type = $type;
        }
        
        public function isValid( $value )
        {
            if ( in_array( $this->type, self::$supportedType['ctype'] ) )
            {
                if ( call_user_func( 'ctype_' . $this->type, $value ) )
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            elseif ( in_array( $this->type, self::$supportedType['phptype'] ) )
            {
                switch( $this->type )
                {
                    case 'bool':
                        return is_bool( $value );
                    case 'int':
                        return is_integer( $value );
                    case 'float':
                        return is_float( $value );
                    case 'array':
                        return is_array( $value );
                    case 'string':
                        return is_string( $value );
                }
            }
            else
            {
                return false;
            }
        }
    }
    
    class AllowedValueListFilter implements InputFilter
    {
        protected $allowedValues;
        
        public function __construct( $allowedValues )
        {
            $this->allowedValues = $allowedValues;
        }
        
        public function isValid( $value )
        {
            return in_array( $value, $this->allowedValues );
        }
    }
    
    class PregMatchFilter implements InputFilter
    {
        protected $regexp;
        
        public function __construct( $regexp )
        {
            $this->regexp = $regexp;
        }
        
        public function isValid( $value )
        {
            return preg_match( $this->regexp, $value );
        }
    }
    
    class FileExtensionFilter implements InputFilter
    {
        protected $extension;
        
        public function __construct( $extension )
        {
            $extension = $extension[0] == '.'
                ? substr( $extension, 1 )
                : $extension
                ;
                
            $this->extension = $extension;
        }
        
        public function isValid( $value )
        {
            return ( pathinfo( $value, PATHINFO_EXTENSION ) == $this->extension );
        }
    }
    
    class NotEmptyFilter implements InputFilter
    {
        public function isValid( $value )
        {
            return ( !empty( $value ) );
        }
    }
?>