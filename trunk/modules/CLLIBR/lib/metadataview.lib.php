<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.5 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents the metadatas
 * related to a specified resource
 */
abstract class MetadataView
{
    protected $propertyList;
    
    protected $metadatas;
    
    public function __construct( $metadatas )
    {
        $this->metadatas = $metadatas;
    }
    
    abstract public function render();
    
    public function getPropertyList()
    {
        return $this->propertyList;
    }
}