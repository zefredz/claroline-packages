<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Description
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2007 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     PACKAGE_NAME
 */

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

class HtmlElement
{
    protected $attributes;
    protected $elementName;
    
    protected static $id = 1;
    
    public function __construct( $elementName, $attributes = null )
    {
        $this->elementName = $elementName;
        $this->attributes = is_array( $attributes ) ? $attributes : array();
    }
    
    public function setAttribute( $name, $value )
    {
        $this->attributes[$name] = $value;
    }
    
    public function unsetAttribute( $name )
    {
        unset( $this->attributes[$name] );
    }
    
    public function appendAttributes( $attr = null )
    {
        if ( is_array( $attr ) )
        {
            $this->attributes = array_merge( $this->attributes, $attr );
        }
    }
    
    public function renderAttributes()
    {
        $str = '';
        
        foreach ( $this->attributes as $name => $value )
        {
            $str .= " {$name}=\"{$value}\"";
        }
        
        return $str;
    }
}

class AutoCloseHtmlElement extends HtmlElement
{
    public function render()
    {
        return "<{$this->elementName}".$this->renderAttributes()." />";
    }
}

class OpenCloseHtmlElement extends HtmlElement
{
    protected $content = '';
    
    public function setContent( $content )
    {
        $this->content = $content;
    }
    
    public function render()
    {
        return "<{$this->elementName}".$this->renderAttributes().">"
            . $this->content
            . "</{$this->elementName}>"
            ;
    }
}

