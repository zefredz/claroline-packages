<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.9.3 $Revision$ - Claroline 1.11
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class PictureView extends ResourceView
{
    protected $acceptedFileList = array( 'png' , 'jpg' , 'jpeg' , 'gif' );
    
    public function render()
    {
        if ( is_string( $this->storedResource ) )
        {
            return '<img src="' . $this->storedResource . '" />';
        }
        else
        {
            return '<div id="imageView"><img src="data:'
                 . StoredResource::getMimeType( $this->storedResource->getFileName() )
                 . ';base64,'
                 . base64_encode( $this->storedResource->getFile( StoredResource::RAW_ACCESS ) )
                 . '" alt="'
                 . $this->storedResource->getFileName()
                 . '" /></div>';
        }
    }
}