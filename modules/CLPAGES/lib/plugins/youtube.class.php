<?php // $Id$

/**
 * CLAROLINE
 *
 * $Revision$
 *
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 */
// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

class YouTubeComponent extends Component
{
    protected $videoId = '';

    /**
     * @see Component
     */
    public function render()
    {
        if( !empty($this->videoId) )
        {
            return '<center>' . "\n"
            .     '<object width="425" height="353">' . "\n"
            .     '<param name="movie" value="http://www.youtube.com/v/'.htmlspecialchars($this->videoId).'&rel=1"></param>' . "\n"
            .     '<param name="wmode" value="transparent"></param>' . "\n"
            .     '<embed src="http://www.youtube.com/v/'.$this->videoId.'&rel=1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="353"></embed>' . "\n"
            .     '</object>' . "\n"
            .     '</center>' . "\n";
        }
        else
        {
            return '<p>' . get_lang('Video not configured') . '</p>' . "\n";
        }
    }

    /**
     * @see Component
     */
    public function editor()
    {
        // use content in textarea
        return '<label for="videoId">' . get_lang('Video id') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
        .     '<input type="text" name="videoId_'.$this->getId().'" id="videoId_'.$this->getId().'" maxlength="255" value="'.htmlspecialchars($this->videoId).'" /><br />' . "\n";
    }

    /**
     * @see Component
     */
    public function getEditorData()
    {
        $this->videoId = $this->getFromRequest('videoId_'.$this->getId());
    }

    /**
     * @see Component
     */
    public function setData( $data )
    {
          $this->videoId = $data['videoId'];
    }

    /**
     * @see Component
     */
    public function getData()
    {
        return array('videoId' => $this->videoId);
    }
}

//PluginRegistry::register('youtube',get_lang('YouTube video'),'YouTubeComponent', 'Externals', 'youtubeIco');
