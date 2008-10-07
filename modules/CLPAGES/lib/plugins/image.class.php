<?php // $Id$

if ( count( get_included_files() ) == 1 ) die( basename(__FILE__) );

/**
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2008 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 */

// vim: expandtab sw=4 ts=4 sts=4:

class ImageComponent extends Component
{
    private $url = '';
    private $caption = '';
    private $height = 0;
    private $width = 0;

    /**
     * @see Component
     */
    public function render()
    {
        if( !empty($this->url) )
        {
            if( !empty($this->height) ) $height = 'height="'.htmlspecialchars($this->height).'"';
            else                        $height = '';

            if( !empty($this->width) ) $width = 'width="'.htmlspecialchars($this->width).'"';
            else                        $width = '';

            $out = '<div class="captionImg">' . "\n"
            .     '<img src="'.htmlspecialchars($this->url).'" '.$height.' '.$width.' alt="" /><br />' . "\n"
            .     '<div class="caption">'.htmlspecialchars($this->caption).'</div>' . "\n"
            .     '</div>' . "\n";

            return $out;

        }
        else
        {
            return '' . "\n";
        }
    }

    /**
     * @see Component
     */
    public function editor()
    {
        // use content in textarea
        return '<label for="url_'.$this->getId().'">' . get_lang('Url of an image') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
        .     '<input type="text" name="url_'.$this->getId().'" id="url_'.$this->getId().'" maxlength="255" size="60" value="'.htmlspecialchars($this->url).'" /><br />' . "\n"
        // caption
        .     '<label for="caption_'.$this->getId().'">' . get_lang('Caption') . '</label><br />' . "\n"
        .     '<input type="text" name="caption_'.$this->getId().'" id="caption_'.$this->getId().'" maxlength="255" size="60" value="'.htmlspecialchars($this->caption).'" /><br />' . "\n"
        // size - height
        .     '<label for="height_'.$this->getId().'">' . get_lang('Height') . '</label><br />' . "\n"
        .     '<input type="text" name="height_'.$this->getId().'" id="height_'.$this->getId().'" maxlength="10" size="10" value="'.htmlspecialchars($this->height).'" />' . "\n"
        .     '&nbsp;<small>'.get_lang('Leave emtpy to keep original size').'</small><br />' . "\n"
        // size - wodth
        .     '<label for="width_'.$this->getId().'">' . get_lang('Width') . '</label><br />' . "\n"
        .     '<input type="text" name="width_'.$this->getId().'" id="width_'.$this->getId().'" maxlength="10" size="10" value="'.htmlspecialchars($this->width).'" />' . "\n"
        .     '&nbsp;<small>'.get_lang('Leave emtpy to keep original size').'</small><br />' . "\n"
        ;
    }

    /**
     * @see Component
     */
    public function getEditorData()
    {
        $this->url = $this->getFromRequest('url_'.$this->getId());
        $this->caption = $this->getFromRequest('caption_'.$this->getId());
        $this->height = (int) $this->getFromRequest('height_'.$this->getId());
        $this->width = (int) $this->getFromRequest('width_'.$this->getId());
    }

    /**
     * @see Component
     */
    public function setData( $data )
    {
          $this->url = $data['url'];
          $this->caption = $data['caption'];
          $this->height = $data['height'];
          $this->width = $data['width'];
    }

    /**
     * @see Component
     */
    public function getData()
    {
        return array(
            'url' => $this->url,
            'caption' => $this->caption,
            'height' => $this->height,
            'width' => $this->width
        );
    }
}

PluginRegistry::register('image',get_lang('Image'),'ImageComponent', '', 'imageIco');
