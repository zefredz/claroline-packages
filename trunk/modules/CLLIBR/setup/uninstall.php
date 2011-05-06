<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

//$cllibr_path = get_path( 'rootSys' ) . 'cllibrary';
$cllibr_path = get_conf( 'CLLIBR_storage_directory' );

$cllibr_dir = new DirectoryIterator( $cllibr_path );

foreach ( $cllibr_dir as $file )
{
    if ( ! $file->isDot() )
    {
        unlink( $cllibr_path . $file->getFileName() );
    }
}

rmdir( $cllibr_path );
