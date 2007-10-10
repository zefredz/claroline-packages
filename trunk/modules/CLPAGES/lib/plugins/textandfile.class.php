<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }

    /**
     * Text and File Component
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Claroline team <info@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLPAGES
     */
     
    class TextAndFileComponent extends Component
    {
        private $content = '';
        private $textAlign = 'left';
        private $fileAlign = 'right';
        private $url = '';
    	
    	public function render()
    	{
            $out = '<div style="float:'.$this->textAlign.'; max-width: 50%;">' . claro_parse_user_text($this->content) . '</div>';
            
            if( !empty($this->url) )
    		{
	    		include_once get_path('incRepositorySys') . '/lib/htmlxtra.lib.php';
	    		
	    		$out .= '<div style="float:'.$this->fileAlign.'">' . "\n"
                .    claro_html_media_player($this->url,$this->url)
                .	 '</div>' . "\n"
                ;
                
                
    		}
    		else
    		{
    			$out .=  '' . "\n";
    		}
    		
            $out .= '<div class="spacer"></div>';
    		
            return $out;
    	}
    	
    	public function editor()
    	{
            $out = '<textarea name="content_'.$this->getId().'" rows="20" cols="80" style="width: 100%;">'.htmlspecialchars(claro_parse_user_text($this->content)).'</textarea>';
            
            $out .= '<label for="url_'.$this->getId().'">' . get_lang('Url of a file') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
    		.	 '<input type="text" name="url_'.$this->getId().'" id="url_'.$this->getId().'" maxlength="255" value="'.htmlspecialchars($this->url).'" /><br />' . "\n"
    		;
    		
            $out .= get_lang('Layout :') . '&nbsp;<span class="required">*</span><br />' . "\n"
    		.	 '<input type="radio" name="layout_'.$this->getId().'" id="align_'.$this->getId().'_left" value="left"'.( $this->textAlign == 'left' ? ' checked="checked"' : ''  ).' />'
    		.    '<label for="layout_'.$this->getId().'_left">' . get_lang('Text on left') . '</label><br />' . "\n"
    		.	 '<input type="radio" name="layout_'.$this->getId().'" id="align_'.$this->getId().'_right" value="right"'.( $this->textAlign == 'right' ? ' checked="checked"' : ''  ).' />'
    		.    '<label for="layout_'.$this->getId().'_right">' . get_lang('Text on right') . '</label><br />' . "\n"
    		;
    		
    		return $out;
    	}
    	
    	public function getEditorData()
    	{
            $this->url = $this->getFromRequest('url_'.$this->getId());
    		$this->content = $this->getFromRequest('content_'.$this->getId());
    		$this->textAlign = $this->getFromRequest('layout_'.$this->getId());
    		$this->fileAlign = $this->textAlign == 'left' ? 'right' : 'left';
    	}
    	
    	/**
		 * @see Component
		 */
    	function setData( $data )
    	{
            $this->content = $data['content'];
            $this->textAlign = $data['textAlign'];
            $this->fileAlign = $this->textAlign == 'left' ? 'right' : 'left';
  			$this->url = $data['url'];
    	}

		/**
		 * @see Component
		 */
    	function getData()
    	{
    		return array(
                'content' => $this->content,
                'textAlign' => $this->textAlign,
				'url' => $this->url,
			);
    	}
    }
    
    PluginRegistry::register('textandfile',get_lang('Text and file'),'TextAndFileComponent');
?>