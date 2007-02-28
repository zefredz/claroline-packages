<?php // $Id$
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package ADSENSE
 *
 */
if ( count( get_included_files() ) == 1 ) die( '---' );

class adsense
{
	var $prefs;
	
	function adsense()
	{
		$this->prefs = array();
		
		$this->prefs['client'] = get_conf('google_ad_client');
		$this->prefs['width'] = get_conf('google_ad_width');		
		$this->prefs['height'] = get_conf('google_ad_height');		
    	$this->prefs['format'] = get_conf('google_ad_format');
    	$this->prefs['type'] = get_conf('google_ad_type');    	
    	$this->prefs['channel'] = get_conf('google_ad_channel');    	
		$this->prefs['borderColor'] = get_conf('google_ad_borderColor');		
		$this->prefs['bgColor'] = get_conf('google_ad_bgColor');	
		$this->prefs['linkColor'] = get_conf('google_ad_linkColor');		
    	$this->prefs['urlColor'] = get_conf('google_ad_urlColor');
		$this->prefs['textColor'] = get_conf('google_ad_textColor');				
	}
	
	function display()
	{
	    $html = "\n";

        $html .= '<script type="text/javascript"><!--' . "\n"
        .    '    google_ad_client = "'.$this->prefs['client'].'";' . "\n"
        .    '    google_ad_width = '.$this->prefs['width'].';' . "\n"
        .    '    google_ad_height = '.$this->prefs['height'].';' . "\n"
        .    '    google_ad_format = "'.$this->prefs['format'].'";' . "\n"
        .    '    google_ad_type = "'.$this->prefs['type'].'";' . "\n"
        .    '    google_ad_channel = "'.$this->prefs['channel'].'";' . "\n"
        .    '    google_color_border = "'.$this->prefs['borderColor'].'";' . "\n"
        .    '    google_color_bg = "'.$this->prefs['bgColor'].'";' . "\n"
        .    '    google_color_link = "'.$this->prefs['linkColor'].'";' . "\n"
        .    '    google_color_url = "'.$this->prefs['urlColor'].'";' . "\n"
        .    '    google_color_text = "'.$this->prefs['textColor'].'";' . "\n"
        .    '//--></script>' . "\n";
        
        $html .= '<script type="text/javascript"' . "\n"
        .    '    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">' . "\n"
        .    '</script>' . "\n";
        
		return $html;
	}
}
?>
