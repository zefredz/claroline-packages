<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * OPML Generator module
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLOPML
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }

    // add a link to current user opml file in Claroline html header
    if ( claro_is_user_authenticated() )
    {
        $GLOBALS['htmlHeadXtra'][] = '<link rel="alternate"'
            . ' type="application/rss+xml"'
            . ' title="'.get_lang('List of RSS for all my forums').'"'
            . ' href="'.get_module_url('CLOPML')
            . '/index.php" />'
            ;
    }
?>