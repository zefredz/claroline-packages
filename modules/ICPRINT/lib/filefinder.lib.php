<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * File finders
     * @package     kernel
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
// From PEAR PHP_Compat 1.5.0
if (!defined('PATH_SEPARATOR'))
{
    define('PATH_SEPARATOR',
        strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? ';' : ':'
    );
}
    
// From SPL examples by (c) Marcus Boerger, 2003 - 2007 
if ( ! class_exists( 'FindFile', false ) )
{
    class FindFile extends FilterIterator
    {
        protected $searchString;
        
        public function __construct( $path, $searchString )
        {
            $this->searchString = $searchString;
            
            $list = split(PATH_SEPARATOR, $path);
            
            if (count($list) <= 1)
            {
                parent::__construct(
                    new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($path)));
            } 
            else
            {
                $it = new AppendIterator();
                
                foreach ( $list as $path )
                {
                    $it->append(new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($path)));
                }
                
                parent::__construct($it);
            }   
        }
        
        public function getSearchString()
        {
            return $this->searchString;
        }
        
        public function accept()
        {
            return !strcmp($this->getSearchString(), $this->current() );
        }
    }
}

// From SPL examples by (c) Marcus Boerger, 2003 - 2007
if ( ! class_exists( 'FindFile', false ) )
{   
    class RegexFileFind extends FindFile
    {
        public function accept()
        {
            return preg_match( $this->current(), $this->getSearchString() );
        }
    }
}
    
    class ExtensionFindFile extends FindFile
    {
        public function accept()
        {
            return ( substr( $this->current(), - ( strlen($this->getSearchString()) ) )
                == $this->getSearchString() );
        }
    }
    
    class PdfFindFile extends ExtensionFindFile
    {
        public function __construct( $path )
        {
            parent::__construct( $path, '.pdf' );
        }
    }
?>