<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents the metadatas
 * related to a specified resource
 * @property array $propertyList
 * @property array $translator
 */
class MetadataView
{
    protected $propertyList;
    protected $translator;
    
    protected $metadatas;
    
    /**
     * Constructor
     * @param array $metadatas
     */
    public function __construct( $metadatas )
    {
        $this->metadatas = $this->translate( $metadatas);
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

/**
 * Interface for metadatas that can be exported
 */
interface Exportable
{
    public function export();
}