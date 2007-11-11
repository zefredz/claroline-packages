<?php // $Id$
/**
 *
 * @version 0.1 $Revision: 22 $
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
		
		$this->prefs['ad_client'] = get_conf('google_ad_client');
		$this->prefs['ad_width'] = get_conf('google_ad_width');		
		$this->prefs['ad_height'] = get_conf('google_ad_height');		
    	$this->prefs['ad_format'] = get_conf('google_ad_format');
    	$this->prefs['ad_type'] = get_conf('google_ad_type');    	
    	$this->prefs['cpa_choice'] = get_conf('google_cpa_choice');
    	$this->prefs['ad_channel'] = get_conf('google_ad_channel');    	    	
		$this->prefs['ad_borderColor'] = get_conf('google_ad_borderColor');		
		$this->prefs['ad_bgColor'] = get_conf('google_ad_bgColor');	
		$this->prefs['ad_linkColor'] = get_conf('google_ad_linkColor');		
    	$this->prefs['ad_urlColor'] = get_conf('google_ad_urlColor');
		$this->prefs['ad_textColor'] = get_conf('google_ad_textColor');				
	}
	
	function display()
	{
	    $html = "\n";

        $html .= '<script type="text/javascript"><!--' . "\n"
        .    '    google_ad_client = "'.$this->prefs['ad_client'].'";' . "\n"
        .    '    google_ad_width = '.$this->prefs['ad_width'].';' . "\n"
        .    '    google_ad_height = '.$this->prefs['ad_height'].';' . "\n"
        .    '    google_ad_format = "'.$this->prefs['ad_format'].'";' . "\n";
        
        if( !empty($this->prefs['ad_type']) )       $html .= '    google_ad_type = "'.$this->prefs['ad_type'].'";' . "\n";
        if( !empty($this->prefs['cpa_choice']) )    $html .= '    google_cpa_choice = "'.$this->prefs['cpa_choice'].'";' . "\n";                
        if( !empty($this->prefs['ad_channel']) )    $html .= '    google_ad_channel = "'.$this->prefs['ad_channel'].'";' . "\n";


        $html .= '    google_color_border = "'.$this->prefs['ad_borderColor'].'";' . "\n"
        .    '    google_color_bg = "'.$this->prefs['ad_bgColor'].'";' . "\n"
        .    '    google_color_link = "'.$this->prefs['ad_linkColor'].'";' . "\n"
        .    '    google_color_url = "'.$this->prefs['ad_urlColor'].'";' . "\n"
        .    '    google_color_text = "'.$this->prefs['ad_textColor'].'";' . "\n"
        .    '//--></script>' . "\n";
        
        $html .= '<script type="text/javascript"' . "\n"
        .    '    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">' . "\n"
        .    '</script>' . "\n";
        
		return $html;
	}
}
?>
