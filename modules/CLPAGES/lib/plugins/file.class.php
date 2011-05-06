<?php // $Id$

/**
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 */
// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

class FileComponent extends Component
{
    protected $url = '';

    /**
     * @see Component
     */
    public function render()
    {
        if( !empty($this->url) )
        {
            include_once get_path('incRepositorySys') . '/lib/htmlxtra.lib.php';

            return claro_html_media_player($this->url,$this->url);
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
        return '<label for="url_'.$this->getId().'">' . get_lang('Url of a file') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
        .	 '<input type="text" name="url_'.$this->getId().'" id="url_'.$this->getId().'" maxlength="255" size="60" value="'.htmlspecialchars($this->url).'" /><br />' . "\n";
    }

    /**
     * @see Component
     */
    public function getEditorData()
    {
        $this->url = $this->getFromRequest('url_'.$this->getId());
    }

    /**
     * @see Component
     */
    public function setData( $data )
    {
        $this->url = $data['url'];
    }

    /**
     * @see Component
     */
    public function getData()
    {
        return array('url' => $this->url);
    }
}

PluginRegistry::register('file',get_lang('File'),'FileComponent', '', 'fileIco');
