<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }

    /**
     * Text and Image Component
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Claroline team <info@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLPAGES
     */

    class TextAndImageComponent extends Component
    {
        private $content = '';
        private $textAlign = 'left';
        private $imgAlign = 'right';
        private $url = '';
    	private $caption = '';
    	private $height = 0;
    	private $width = 0;

    	public function render()
    	{
			$out = '<div style="width:99%">' . "\n";

			// Image
            if( !empty($this->url) )
    		{
    			if( !empty($this->height) ) $height = 'height="'.htmlspecialchars($this->height).'"';
    			else						$height = '';

    			if( !empty($this->width) ) $width = 'width="'.htmlspecialchars($this->width).'"';
    			else						$width = '';

	    		$out .= '<div class="captionImg" style="width: 48%; float:'.$this->imgAlign.'">' . "\n"
	    		.	 '<img src="'.htmlspecialchars($this->url).'" '.$height.' '.$width.' alt="" /><br />' . "\n"
	    		.	 '<div class="caption">'.htmlspecialchars($this->caption).'</div>' . "\n"
	    		.	 '</div>' . "\n"
                ;

    		}
    		else
    		{
    			$out .= '' . "\n";
    		}

    		// Text
            $out .= '<div style="width: 48%; float: '.$this->textAlign.';">' . claro_parse_user_text($this->content) . '</div>';

            $out .= '<div class="spacer"></div>' . "\n"
            .	 '</div>' . "\n";

            return $out;
    	}

    	public function editor()
    	{
    		$out = '';

            $out .= get_lang('Layout :') . '&nbsp;<span class="required">*</span><br />' . "\n"
    		.	 '<input type="radio" name="layout_'.$this->getId().'" id="layout_'.$this->getId().'_left" value="left"'.( $this->textAlign == 'left' ? ' checked="checked"' : ''  ).' />'
    		.    '<label for="layout_'.$this->getId().'_left">' . get_lang('Text on left') . '</label>' . "\n"
    		.	 '<input type="radio" name="layout_'.$this->getId().'" id="layout_'.$this->getId().'_right" value="right"'.( $this->textAlign == 'right' ? ' checked="checked"' : ''  ).' />'
    		.    '<label for="layout_'.$this->getId().'_right">' . get_lang('Text on right') . '</label><br /><br />' . "\n"
    		;

    		// Text
            $out .= '<fieldset>' . "\n"
            .	 '<legend>'.get_lang('Text').'</legend>' . "\n"
			.	 '<textarea name="content_'.$this->getId().'" id="content_'.$this->getId().'" rows="20" cols="80" style="width: 100%;">'.htmlspecialchars(claro_parse_user_text($this->content)).'</textarea>'
			.	 '</fieldset>' . "\n"
			;

			// Image
            $out .= '<fieldset>' . "\n"
            .	 '<legend>'.get_lang('Image').'</legend>' . "\n"
			.	 '<label for="url_'.$this->getId().'">' . get_lang('Url of an image') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
    		.	 '<input type="text" name="url_'.$this->getId().'" id="url_'.$this->getId().'" maxlength="255" size="60" value="'.htmlspecialchars($this->url).'" /><br />' . "\n"
    		// caption
    		.	 '<label for="caption_'.$this->getId().'">' . get_lang('Caption') . '</label><br />' . "\n"
    		.	 '<input type="text" name="caption_'.$this->getId().'" id="caption_'.$this->getId().'" maxlength="255" size="60" value="'.htmlspecialchars($this->caption).'" /><br />' . "\n"
    		// size - height
			.	 '<label for="height_'.$this->getId().'">' . get_lang('Height') . '</label><br />' . "\n"
    		.	 '<input type="text" name="height_'.$this->getId().'" id="height_'.$this->getId().'" maxlength="10" size="10" value="'.htmlspecialchars($this->height).'" />' . "\n"
    		.	 '&nbsp;<small>'.get_lang('Leave emtpy to keep original size').'</small><br />' . "\n"
    		// size - width
    		.	 '<label for="width_'.$this->getId().'">' . get_lang('Width') . '</label><br />' . "\n"
    		.	 '<input type="text" name="width_'.$this->getId().'" id="width_'.$this->getId().'" maxlength="10" size="10" value="'.htmlspecialchars($this->width).'" />' . "\n"
    		.	 '&nbsp;<small>'.get_lang('Leave emtpy to keep original size').'</small><br />' . "\n"
    		.	 '</fieldset>' . "\n"
    		;


    		return $out;
    	}

    	public function getEditorData()
    	{
            $this->url = $this->getFromRequest('url_'.$this->getId());
    		$this->caption = $this->getFromRequest('caption_'.$this->getId());
    		$this->height = (int) $this->getFromRequest('height_'.$this->getId());
    		$this->width = (int) $this->getFromRequest('width_'.$this->getId());
    		$this->content = $this->getFromRequest('content_'.$this->getId());
    		$this->textAlign = $this->getFromRequest('layout_'.$this->getId());
    		$this->imgAlign = $this->textAlign == 'left' ? 'right' : 'left';
    	}

    	/**
		 * @see Component
		 */
    	function setData( $data )
    	{
            $this->content = $data['content'];
            $this->textAlign = $data['textAlign'];
            $this->imgAlign = $this->textAlign == 'left' ? 'right' : 'left';
  			$this->url = $data['url'];
  			$this->caption = $data['caption'];
  			$this->height = $data['height'];
  			$this->width = $data['width'];
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
				'caption' => $this->caption,
				'height' => $this->height,
				'width' => $this->width
			);
    	}
    }

    PluginRegistry::register('textandimage',get_lang('Text and image'),'TextAndImageComponent', 'layout', 'textImageIco');
?>