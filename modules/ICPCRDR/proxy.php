<?php // $Id$

/**
 * Claroline Podcast Reader : Proxy to allow cross site XML file loading through 
 *  AJAX. This proxy script is a lightweight script and so it does not use the 
 *  Claroline kernel to avoid loading too much libraries.
 *
 * @version     ICPCRDR 1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICPCRDR
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

try
{
    // add some security : check the HTTP_REFERER
    if ( !isset($_SERVER['HTTP_REFERER']) )
    {
        throw new Exception('Not Allowed');
    }
    else
    {
        $tmp = parse_url($_SERVER['HTTP_REFERER']);
        
        if ( dirname( $tmp['path'] ) !== dirname( $_SERVER['PHP_SELF'] ) ) {
           throw new Exception('Not Allowed');
        }
    }
    
    // get the wanted remote file url
    if ( !isset($_REQUEST['url']) || empty($_REQUEST['url']) ) {
        throw new Exception('Missing parameter');
    }
    
    // load the library needed to load remote files
    require_once dirname(__FILE__) . '/lib/urlgetcontents.lib.php';
    
    if ( ! $content = url_get_contents($_REQUEST['url']) ) {
        throw new Exception('Cannot load url');
    }
    
    header("Content-Type: text/xml");
    echo $content;
}
catch ( Exception $e )
{
    header("HTTP/1.0 403 Forbidden");
    die($e->getMessage());
}
