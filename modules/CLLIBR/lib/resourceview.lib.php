<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * An abstract class for resource view
 * @property StoredResource $storedResource
 * @abstract render();
 */
abstract class ResourceView
{
    protected $acceptedFileList;
    
    /**
     * Constructor
     */
    public function __construct( $storedResource )
    {
        $this->storedResource = $storedResource;
    }
    
    /**
     * Renders the view
     */
    abstract public function render();
    

    /**
     * Verifies the validity on the file name,
     * and if valid, sets the resource name
     * @return boolean true on success
     */
    public function validate( $fileName )
    {
        return in_array( $fileName , $this->acceptedFileList );
    }

}