<?php // $Id$
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package RSSREAD
 *
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

//$tlabelReq = 'RSSREAD';
include_once claro_get_conf_repository().'RSSREAD.conf.php';

require_once get_path('incRepositorySys') . '/lib/lastRSS/lastRSS.php';


$rss = new lastRSS;

// configure parser
$rss->cache_dir = get_path('rootSys') . 'tmp/cache/';
$rss->cache_time = get_conf('cacheTime')*60;
$rss->items_limit = get_conf('itemsToShow');
$rss->stripHTML = TRUE;

if( get_conf('feedCharset') != '' ) $rss->cp = get_conf('feedCharset');

$html = '';

if( false !== $rs = $rss->get( get_conf('feedUrl') ) )
{
    $feedTitle = ( get_conf('feedTitle') == '' ) ? $rs['title'] : get_conf('feedTitle');
    
    $html .= '<p>' . "\n";
    $html .= '<strong>' . $feedTitle . '</strong><br />' . "\n";

    foreach( $rs['items'] as $item )
    {
        $html .= '- <a href="' . $item['link'] . '">' . $item['title'] . '</a><br />' . "\n";
    }

    $html .= '</p>' . "\n";
}
else
{
    if( $is_platformAdmin ) $html .= '<p>' . get_lang('Error : cannot read RSS feed') . '</p>' . "\n";
}

$claro_buffer->append($html);

?>
