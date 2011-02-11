<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$cllibr_path = get_path( 'rootSys' ) . 'cllibrary';

if ( ! is_dir( $cllibr_path ) && ! mkdir( $cllibr_path , 0700 ) )
{
    throw new Exception( 'Error while creating the library root directory' );
    exit();
}

$htaccess_path = $cllibr_path . '/.htaccess';

if( ! file_exists( $htaccess_path ) )
{
    $htaccess = fopen( $htaccess_path , 'w' );
    fwrite( $htaccess , 'Deny from all' );
    fclose( $htaccess );
}