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
?>