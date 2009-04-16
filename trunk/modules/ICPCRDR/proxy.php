<?php // $Id$

/**
 * Claroline Advanced Link Tool : Proxy
 *
 * @version     ICPCRDR 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLKTOOL
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

try
{
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
    
    if ( !isset($_REQUEST['url']) || empty($_REQUEST['url']) ) {
        throw new Exception('Missing parameter');
    }
    
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
