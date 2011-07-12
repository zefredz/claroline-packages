<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.8.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents a resource type definition
 * @const TYPE_SHORT
 * @const TYPE_LONG
 * @protected string $fileName
 * @protected string $name
 * @protected array $authorizedFileList
 * @protected array $defaultMetadataList
 */
class ResourceType
{
    const TYPE_SHORT = 'short';
    const TYPE_LONG  = 'long';
    
    protected $fileName;
    protected $name;
    protected $authorizedFileList = array();
    protected $defaultMetadataList = array();
    
    /**
     * Constructor
     * @param string $fileName
     */
    public function __construct( $fileName = null )
    {
        if ( $fileName )
        {
            $this->fileName = $fileName;
            $this->load();
        }
    }
    
    /**
     * Loads the type definition from the associated xml file
     * This method is called by the constructor if it received a $fileName
     */
    public function load()
    {
        $xmlElement = simplexml_load_file( $this->fileName );
        
        $this->name = $xmlElement->name;
        
        foreach( $xmlElement->authorizedFileList[0] as $extension )
        {
            $this->authorizedFileList[] = $extension->__toString();
        }
        
        foreach( $xmlElement->defaultMetadataList[0] as $metadata )
        {
            $this->defaultMetadataList[ $metadata->name->__toString() ] = $metadata->type->__toString();
        }
    }
    
    /**
     * Getter for $name
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Getter for $authorizedFileList
     * @return array $authorizedFileList
     */
    public function getAuthorizedFileList()
    {
        return $this->authorizedFileList;
    }
    
    /**
     * Getter for $defaultMetadataList
     * @return array $defaultMetadataList
     */
    public function getDefaultMetadataList()
    {
        return $this->defaultMetadataList;
    }
    
    /**
     * Setter for $name
     * @param string $name
     */
    public function setName( $name )
    {
        $this->name = $name;
    }
    
    /**
     * Adds a new extension in authorized file list
     * @param string $extension
     */
    public function addAuthorizedFile( $extension )
    {
        if ( ! in_array( $extension , $this->authorizedFileList ) )
        {
            $this->addAuthorizedFile[] = $extension;
        }
    }
    
    /**
     * Removes a new extension in authorized file list
     * @param string $extension
     */
    public function removeAuthorizedFile( $extension )
    {
        foreach( $this->authorizedFileList as $index => $ext )
        {
            if ( $extension == $ext )
            {
                unset( $this->addAuthorizedFile[ $index ] );
            }
        }
    }
    
    /**
     * Adds a metadata in default list
     * @param string $name
     * @param string $type
     */
    public function addMetadata( $name , $type = self::TYPE_SHORT )
    {
        if ( array_key_exists( $name , $this->defaultMetadataList ) )
        {
            $this->defaultMetadataList[ $name ] = $type;
        }
    }
    
    /**
     * Removes a metadata from default list
     * @param string $name
     */
    public function removeMetadata( $name )
    {
        unset( $this->defaultMetadataList[ $name ] );
    }
    
    /**
     * Changes the type of a metadata field
     * @param string $name
     * @param string $type
     */
    public function changeType( $name , $type )
    {
        if ( $this->metadataExists( $name ) )
        {
            $this->defaultMetadataList[ $name ] = $type;
        }
    }
    
    /**
     * Saves the resource type definition in a xml file
     */
    public function save()
    {
        $xml  = '<?xml version="1.0"?>' . "\n";
        $xml .= '<documentType>' . "\n";
        $xml .= '    <name>' . str_replace( ' ' , '_' , $this->name ) . '</name>' . "\n";
        $xml .= '    <authorizedFileList>' . "\n";
        
        foreach( $this->authorizedFileList as $extension )
        {
            $xml .= '        <extension>' . $extension . '</extension>' . "\n";
        }
        
        $xml .= '    </authorizedFileList>' . "\n";
        $xml .= '    <defaultMetadataList>' . "\n";
        
        foreach( $this->defaultMetadataList as $name => $type )
        {
            $xml .= '        <metadata>' . "\n";
            $xml .= '            <name>' . $name . '</name>' . "\n";
            $xml .= '            <type>' . $type . '</type>' . "\n";
            $xml .= '        </metadata>' . "\n";
        }
        
        $xml .= '    </defaultMetadataList>' . "\n";
        $xml .= '</documentType>';
        
        $xmlElement = new SimpleXMLElement;
        $xmlElement->asXML( $this->fileName );
    }
}