<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class for resource type management
 * @const DEFAULT_FILE_NAME
 * @const REPLACE
 * @param string $fileNameStructure
 * @param string $repository
 * @param $resourceTypeList
 */
class ResourceTypeList
{
    const DEFAULT_FILE_NAME = 'type._TYPE_.xml';
    const REPLACE = '_TYPE_';
    
    protected $fileNameStructure;
    protected $repository;
    protected $resourceTypeList;
    
    /**
     * Constructor
     * @param string $repository
     * @param $fileNameStructure
     */
    public function __construct( $repository , $fileNameStructure = self::DEFAULT_FILE_NAME )
    {
        $this->repository = $repository;
        $this->fileNameStructure = $fileNameStructure;
        $this->load();
    }
    
    /**
     * Loads the resource type list
     * This method is called by the constructor
     */
    public function load()
    {
        $fileList = new DirectoryIterator( $this->repository );
        
        foreach( $fileList as $file )
        {
            if ( ! $file->isDir() && ! $file->isDot() )
            {
                $fileName = $file->getFileName();
                $part = explode( '.' , $fileName );
                
                if ( count( $part ) == 3 && $part[0] == 'type' && $part[2] == 'xml' )
                {
                    $type = $part[1];
                    $this->resourceTypeList[ $type ] = new ResourceType( $this->repository . $fileName );
                }
            }
        }
    }
    
    /**
     * Gets the list of all the type names
     * @return array $typeNameList
     */
    public function getResourceTypeList()
    {
        //return array_keys( $this->resourceTypeList );
        $resourceTypeList = array();
        
        foreach( $this->resourceTypeList as $type => $resourceType )
        {
            $resourceTypeList[ $type ] = $resourceType->getName();
        }
        
        return $resourceTypeList;
    }
    
    /**
     * A private method to get the xml definition file name from a type name
     * @param string $type
     * @return string $fileName
     */
    private function getFileName( $type )
    {
        return str_replace( self::REPLACE , $type , $this->fileNameStructure );
    }
    
    /**
     * Gets the ResourceType object with the specified name
     * @param string $name
     * @return ResourceType $resourceType;
     */
    public function get( $name )
    {
        /*
        if ( array_key_exists( $name , $this->resourceTypeList ) )
        {
            return $this->resourceTypeList[ $name ];
        }*/
        
        foreach( $this->getResourceTypeList() as $type => $typeName )
        {
            if ( $name == $typeName )
            {
                return $this->resourceTypeList[ $type ];
            }
        }
    }
    /**
     * Adds a new type
     * @param string $type
     */
    public function add( $type )
    {
        if ( ! array_key_exists( $type , $this->resourceTypeList ) )
        {
            $fileName = $this->getFileName( $type );
            $resourceType = new ResourceType( $fileName );
        }
    }
    
    /**
     * Removes a type
     * @param string $type
     */
    public function remove( $type )
    {
        unset( $this->resourceTypeList[ $type ] );
    }
}