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
