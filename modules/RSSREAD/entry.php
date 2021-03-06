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

require_once get_path('incRepositorySys') . '/lib/thirdparty/lastRSS/lastRSS.lib.php';

$rss = new lastRSS;

// configure parser
$rss->cache_dir = get_path('rootSys') . '/tmp/cache/';
$rss->cache_time = get_conf('cacheTime')*60;
$rss->stripHTML = TRUE;

if( get_conf('feedCharset') != '' ) $rss->cp = get_conf('feedCharset');

    /*
     * Output
     */
    $cssLoader = CssLoader::getInstance();
    $cssLoader->load( 'rssread', 'all'); 

	$html = '';
	
	if( false !== $rs = $rss->get( get_conf('feedUrl') ) )
	{
	    $feedTitle = ( get_conf('feedTitle') == '' ) ? $rs['title'] : get_conf('feedTitle');
	    
	    $html .= '<p>' . "\n";
	    $html .= '<div class="rssTitle">' . $feedTitle . '</div>' . "\n";
	    $html .= '<ul>'. "\n";
	
	    $limit = get_conf('itemsToShow');
	    $i = 0;
	    
	    foreach( $rs['items'] as $item )
	    {
	        if( $i < $limit )
	        {
	            $html .= '<div class="rssItem"><li><a href="' . $item['link'] . '">' . $item['title'] . '</a></li></div>' . "\n";
	            $i++;
	        }
	        else
	        {
	            break;
	        }
	    }
	
	    $html .= '</p>' . "\n";
	}
	else
	{
	    if( claro_is_platform_admin() )
	    {
	        $html .= '<p>' . get_lang('Error : cannot read RSS feed (Check feed url and if php setting "allow_url_fopen" is turned on).') . '</p>' . "\n";
	    }
	}
	$html .= '</ul>'. "\n";
	$claro_buffer->append($html);

?>
