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
 * ready to be exported
 * @property array $forbiddenCharList
 * @property string $extension
 */
abstract class MetadataExport extends MetadataView
{
    public $forbiddenCharList = array( ' ' , '/' , '\\' , '$' , '*' , '(' , ')' , '[' , ']' );
    
    protected $fileExtension;
    
    /**
     * Gets the export's file name
     * @return string $fileName
     */
    public function getFileName( $resourceId = null )
    {
        $fileName = array_key_exists( 'title' , $this->metadatas )
                  ? str_replace( $this->forbiddenCharList , '_' , $this->metadatas[ 'title' ] )
                  : 'resource' . $resourceId;
        
        return 'metadata_' . $fileName . '.' . $this->fileExtension;
    }
    
    abstract public function export( $url );
}