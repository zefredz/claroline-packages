<?php // $Id$

/**
 * Claroline Advanced Link Tool
 *
 * @version     CLLKTOOL 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLKTOOL
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

class LinkRenderer implements Display
{
    protected $url, $title, $options, $type;
    
    public function __construct( $url, $options, $type, $title = '')
    {
        $this->url = $url;
        $this->options = $options;
        $this->type = $type;
        $this->title = $title;
    }
    
    /**
     *
     * Display the form to add/edit link
     *
     * @author Dimitri Rambout <dimitri.rambout@uclouvain.be>
     *
     * @param
     *
     * @return string form
     *
     */
    static public function displayForm( $formUrl, $title, $url, $typeList, $type, $options, $visibility, $id, $internOptionsList)
    {
        $selectOptionsList =  '<option value="0"></option>'
        . '<option value="freeValue" class="_freeValue">' . get_lang( 'Free value' ) . '</option>'
        ;
        foreach($internOptionsList as $labelValue => $_options)
        {
            $selectOptionsList .= '<optgroup label="' . $labelValue . '">';
            foreach($_options as $key => $value)
            {
                $selectOptionsList .= '<option value="' . $key . '" class="_'.$key.'">' . $value . '</option>';
            }
            $selectOptionsList .= '</optgroup>';
        }
        
        Claroline::getInstance()->display->header->addInlineJavascript('var selectOptionsList = \''. $selectOptionsList .'\';');
        Claroline::getInstance()->display->header->addInlineJavascript('var optionsNb = \''. count( $options ) .'\';');
        
        $form = new PhpTemplate( dirname(__FILE__) . '/../templates/linkaddeditform.tpl.php' );
        $form->assign( 'formUrl', $formUrl);
        $form->assign( 'title', $title);
        $form->assign( 'url', $url);
        $form->assign( 'typeList', $typeList);
        $form->assign( 'type', $type);
        $form->assign( 'options', isset($options['params']) ? $options['params'] : array());
        $form->assign( 'width', isset($options['width']) ? $options['width'] : '');
        $form->assign( 'height', isset($options['height']) ? $options['height'] : '');
        $form->assign( 'optionsList', $selectOptionsList );
        $form->assign( 'id', $id );
        
        return $form->render();
 
    }
    
    public function render()
    {
        $out = '';
        
        switch( $this->type )
        {
            case 'post:xml':
            case 'post:plain':
            case 'post:json':
                $out .= $this->_renderPost();
                break;
            case 'widget':
                $out .= $this->_renderWidget();
                break;
            case 'popup':
                $out .= $this->_renderPopup();
                break;
            case 'iframe':
                $out .= $this->_renderIframe();
                break;
            default:
                throw new Exception('Invalid link type');
        }
        
        return $out;
    }
    
    protected function _renderIframe()
    {
        $params = isset( $this->options['params'] ) ? $this->options['params'] : array();
        $data = http_build_query($params);
        
        $url = htmlspecialchars( strpos( $this->url, '?' ) !== false ? $this->url . '&' . $data : $this->url . '?' . $data );
        $width = htmlspecialchars( isset($this->options['width']) ? $this->options['width'] : '100%' );
        $height = htmlspecialchars( isset($this->options['height']) ? $this->options['height'] : '100%' );
        $out = '';
        $out .= '<h4>' . htmlspecialchars($this->title) . '</h4>' . "\n"
            . '<iframe src="'. $url .'" id="linkWidget" width="'.$width.'" height="'.$height.'">' . "\n"
            . '</iframe>' . "\n"
            ;
        
        return $out;
    }
    
    protected function _renderWidget()
    {
        $params = isset( $this->options['params'] ) ? $this->options['params'] : array();
        $data = http_build_query($params);
        
        $out .= '<h4>' . htmlspecialchars($this->title) . '</h4>' . "\n"
            . '<div id="linkWidget">' . "\n"
            . '</div>' . "\n"
            . '<script type="text/javascript">
                    $(function(){
                        $.post("'.$this->url.'", "'.$data.'", function( data ){
                            $("#linkWidget").append( data );
                        } );
                    });
                </script>'
            ;
        
        return $out;
    }
    
    protected function _renderPost()
    {
        
    }
    
    protected function _renderPopup()
    {
        
    }
}

class LinkCollectionRenderer implements Display
{
    protected $collection;
    
    public function render()
    {
        
    }
}
