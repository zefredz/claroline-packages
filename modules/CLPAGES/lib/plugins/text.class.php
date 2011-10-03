<?php

// $Id$

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

class TextComponent extends Component
{

    private $content = '';

    /**
     * @see Component
     */
    public function render()
    {
        return claro_parse_user_text($this->content);
    }

    /**
     * @see Component
     */
    public function editor()
    {
        return '<textarea name="content_' . $this->getId() . '" id="content_' . $this->getId() . '" rows="20" cols="80" style="width: 100%;">' . htmlspecialchars($this->render()) . '</textarea>';
    }

    /**
     * @see Component
     */
    public function getEditorData()
    {
        $this->content = $this->getFromRequest('content_' . $this->getId());
    }

    /**
     * @see Component
     */
    public function setData($data)
    {
        $this->content = $data['content'];
        
        if ( !preg_match('/content\: html tiny_mce/', $this->content) )
        {
            $this->content .= "\n".'<!-- content: html tiny_mce -->';
        }
    }

    /**
     * @see Component
     */
    public function getData()
    {
        return array ('content' => $this->content);
    }

}

PluginRegistry::register('text', get_lang('Text'), 'TextComponent', '', 'textIco');
