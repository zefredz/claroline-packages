<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
* File finders
* @package     kernel
*/

class FileFinder extends FilterIterator
{
    protected $searchString;
    
    public function __construct( $path, $searchString, $recursive = true )
    {
        $this->searchString = $searchString;
        
        if ( ! $recursive )
        {
            parent::__construct( 
                new IteratorIterator( 
                    new DirectoryIterator($path) ) ); 
        }
        else
        {
             parent::__construct(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path)));
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

// From SPL examples by (c) Marcus Boerger, 2003 - 2007

class RegexpFileFinder extends FileFinder
{
    public function accept()
    {
        return preg_match( $this->current(), $this->getSearchString() );
    }
}

class ExtensionFileFinder extends FileFinder
{
    public function accept()
    {
        return ( substr( $this->current(), - ( strlen($this->getSearchString()) ) )
            == $this->getSearchString() );
    }
}
