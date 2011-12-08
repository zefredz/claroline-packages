<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.9.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents the metadatas
 * related to a specified resource
 * ready to be rendered
 * @property array $propertyList
 * @property array $templateList
 * @property array $translator
 * @property array $metadatas
 * @property string $type
 */
abstract class MetadataView
{
    const TYPE_DEFAULT = 'default';
    
    protected $propertyList;
    protected $templateList;
    protected $translator;
    
    protected $metadatas;
    protected $type;
    
    /**
     * Constructor
     * @param array $metadatas
     */
    public function __construct( $metadatas )
    {
        $this->metadatas = $this->translate( $metadatas);
        $this->type = self::TYPE_DEFAULT;
    }
    
    /**
     * Getter for $propertyList
     * @return array $propertyList
     */
    public function getPropertyList()
    {
        return $this->propertyList;
    }
    
    /**
     * Setter for $type
     * @param string $type
     */
    public function setType( $type )
    {
        return $this->type = array_key_exists( $type , $this->templateList )
                               ? $type
                               : self::TYPE_DEFAULT;
    }
    
    /**
     * Translates the received metadatas into self-defined property names
     * @param array $metadatas
     * @return array $translatedMetadatas
     */
    public function translate( $metadatas )
    {
        $translatedMetadatas = array();
        
        foreach( $metadatas as $name => $value )
        {
            if ( array_key_exists( $name , $this->translator ) )
            {
                $translatedMetadatas[ $this->translator[ $name ] ] = $value;
            }
            else
            {
                $translatedMetadatas[ $name ] = $value;
            }
        }
        
        return $translatedMetadatas;
    }
}

/**
 * Interface for metadatas that can be rendered
 */
interface Renderable
{
    public function render();
}