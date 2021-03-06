<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Text and File Component
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @author      Claroline team <info@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE
 * @package     CLPAGES
 */

class Text2ColumnsComponent extends Component
{
    protected $contentA = '';
    protected $contentB = '';
    protected $textAAlign = 'left';
    protected $textBAlign = 'right';

    public function render()
    {
        $out = '<div style="width:99%">' . "\n";

        // Text A
        $out .= '<div style="width: 48%; float: '.$this->textAAlign.';">' . claro_parse_user_text($this->contentA) . '</div>';

        // Text B
        $out .= '<div style="width: 48%; float: '.$this->textBAlign.';">' . claro_parse_user_text($this->contentB) . '</div>';

        $out .= '<div class="spacer"></div>' . "\n"
        .	 '</div>' . "\n";

        return $out;
    }

    public function editor()
    {
        $out = '';

        $out .= get_lang('Layout :') . '&nbsp;<span class="required">*</span><br />' . "\n"
        .	 '<input type="radio" name="layout_'.$this->getId().'" id="layout_'.$this->getId().'_left" value="left"'.( $this->textAAlign == 'left' ? ' checked="checked"' : ''  ).' />'
        .    '<label for="layout_'.$this->getId().'_left">' . get_lang('Text A on left') . '</label>' . "\n"
        .	 '<input type="radio" name="layout_'.$this->getId().'" id="layout_'.$this->getId().'_right" value="right"'.( $this->textAAlign == 'right' ? ' checked="checked"' : ''  ).' />'
        .    '<label for="layout_'.$this->getId().'_right">' . get_lang('Text A on right') . '</label><br /><br />' . "\n"
        ;

        $out .= '<fieldset>' . "\n"
        .	 '<legend>'.get_lang('Text A').'</legend>' . "\n"
        .	 '<textarea name="contentA_'.$this->getId().'" id="contentA_'.$this->getId().'" rows="20" cols="80" style="width: 100%;">'.htmlspecialchars(claro_parse_user_text($this->contentA)).'</textarea>'
        .	 '</fieldset>' . "\n"
        ;

        $out .= '<fieldset>' . "\n"
        .	 '<legend>'.get_lang('Text B').'</legend>' . "\n"
        .	 '<textarea name="contentB_'.$this->getId().'" id="contentB_'.$this->getId().'" rows="20" cols="80" style="width: 100%;">'.htmlspecialchars(claro_parse_user_text($this->contentB)).'</textarea>'
        .	 '</fieldset>' . "\n"
        ;


        return $out;
    }

    public function getEditorData()
    {
        $this->contentA = $this->getFromRequest('contentA_'.$this->getId());
        $this->contentB = $this->getFromRequest('contentB_'.$this->getId());
        $this->textAAlign = $this->getFromRequest('layout_'.$this->getId());
        $this->textBAlign = $this->textAAlign == 'left' ? 'right' : 'left';
    }

    /**
     * @see Component
     */
    function setData( $data )
    {
        $this->contentA = $data['contentA'];
        
        if ( !preg_match('/content\: html tiny_mce/', $this->contentA) )
        {
            $this->contentA .= "\n".'<!-- content: html tiny_mce -->';
        }
        
        $this->contentB = $data['contentB'];
        
        if ( !preg_match('/content\: html tiny_mce/', $this->contentB) )
        {
            $this->contentB .= "\n".'<!-- content: html tiny_mce -->';
        }
        
        $this->textAAlign = $data['textAAlign'];
        $this->textBAlign = $this->textAAlign == 'left' ? 'right' : 'left';
    }

    /**
     * @see Component
     */
    function getData()
    {
        return array(
            'contentA' => $this->contentA,
            'contentB' => $this->contentB,
            'textAAlign' => $this->textAAlign
        );
    }
}

PluginRegistry::register('text2columns',get_lang('Text on 2 columns'),'Text2ColumnsComponent', 'layout', 'textTextIco');
