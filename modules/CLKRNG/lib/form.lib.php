<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Description
 *
 * @version     1.12 $Revision$
 * @copyright   2001-2014 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     PACKAGE_NAME
 */

require_once __DIR__ . '/html.lib.php';

class FormElement extends HtmlElement
{  
    public function getId()
    {
        if ( ! array_key_exists( 'id', $this->attributes ) )
        {
            $this->attributes['id'] = $this->attributes['name'] .'_'. self::$id++;
        }
        
        return $this->attributes['id'];
    }
    
    public function renderAttributes()
    {
        if ( ! array_key_exists( 'name', $this->attributes ) )
        {
            throw new Exception ('Form elements must have a name');
        }
        
        $this->getId(); 
        
        return parent::renderAttributes();
    }
}

class AutoCloseFormElement extends FormElement
{
    public function render()
    {
        return "<{$this->elementName}".$this->renderAttributes()." />";
    }
}

class OpenCloseFormElement extends FormElement
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

class Form extends OpenCloseHtmlElement
{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    
    protected $action, $method;
    
    protected $elements = array();
    
    public function __construct( $action = '', $method = Form::METHOD_POST, $extra = array() )
    {
        $this->action = empty( $action )
            ? $_SERVER['PHP_SELF']
            : $action
            ;
            
         if ( ! ( $method == Form::METHOD_POST || Form::METHOD_GET ) )
         {
            throw new Exception ("Invalid method {$method}");
         }
         
         $this->method = $method;
         
         parent::__construct('form', $extra);
         
         $this->setAttribute( 'action', $this->action );
         $this->setAttribute( 'method', $this->method );
    }
    
    public function render()
    {
        $this->content = "\n";
        
        foreach ( $this->elements as $element )
        {
            $this->content .= $element['element']->render()
                . ($element['newline'] ? '<br />' : '')
                . "\n"
                ;
        }
        
        return parent::render();
    }
    
    public function addElement( $element, $newline = false )
    {
        $this->elements[] = array(
            'element' => $element,
            'newline' => $newline
        );
    }
}

class Fieldset extends OpenCloseHtmlElement
{
    protected $legend;
    protected $elements = array();
    
    public function __construct( $legend, $extra = '' )
    {
        parent::__construct( 'fieldset', $extra );
        $this->legend = new Legend( $legend );
    }
    
    public function addElement( $element, $newline = false )
    {
        $this->elements[] = array(
            'element' => $element,
            'newline' => $newline
        );
    }
    
    public function render()
    {
        $this->content = "\n" . $this->legend->render() . "\n";
        
        foreach ( $this->elements as $element )
        {
            $this->content .= $element['element']->render()
                . ($element['newline'] ? '<br />' : '')
                . "\n"
                ;
        }
        
        return parent::render();
    }
}

class Legend extends OpenCloseHtmlElement
{
    public function __construct( $legend, $extra = '' )
    {
        parent::__construct('legend', $extra);
        $this->setContent( $legend );
    }
}

class Label extends OpenCloseHtmlElement
{
    protected $label, $for, $extra;
    
    public function __construct( $label, $for, $extra = null )
    {
        $attributes = is_array( $extra ) ? $extra : array();
        $attributes['for'] = $for;
           
        parent::__construct( 'label', $attributes );
        
        $this->setContent( $label );
    }
}

class InputGeneric extends AutoCloseFormElement
{
    protected $label = null;
    protected $labelAfter = false;
    
    public function __construct( $type, $name, $value = '', $extra = '' )
    {
        if ( ! in_array( $type
            , array( 'text','password','submit','button','image','file','radio','checkbox','hidden') ) )
        {
            throw new Exception ( "Invalid input type {$type}" );
        }
        
        parent::__construct( 'input' );
        
        $this->setAttribute( 'type', $type );
        $this->setAttribute( 'name', $name );
        $this->setAttribute( 'value', $value );
        if (is_array($extra)) $this->appendAttributes( $extra );
    }
    
    public function setLabel( $label, $after = false, $extra = '' )
    {
        $this->label = new Label( $label, $this->getId(), $extra );
        $this->labelAfter = $after ? true : false;
    }
    
    public function disable()
    {
        $this->setAttribute( 'disabled', 'disabled' );
    }
    
    public function enable()
    {
        $this->unsetAttribute( 'disabled' );
    }
    
    // readonly works on text fields only (text,password and textarea
    public function readonly()
    {
        if ( ! in_array( $this->attributes['type'], array('text','password') ) )
        {
            throw new Exception ("Only text input could be readonly not for {$this->attributes['type']}");
        }
        
        $this->setAttribute( 'readonly', 'readonly' );
    }
    
    public function checked()
    {
        if ( ! in_array( $this->attributes['type'], array('radio', 'checkbox') ) )
        {
            throw new Exception ("Only radio and checkbox input could be checked not {$this->attributes['type']}");
        }
        
        $this->setAttribute( 'checked', 'checked' );
    }
    
    public function render()
    {
        $label = is_null( $this->label )  ? ''  : $this->label->render();
        
        return ( $this->labelAfter ? '' : (empty($label) ? '' : $label .'<br />' ) )
            . parent::render()
            . ( $this->labelAfter ? $label : '' )
            ;
    }
}

class InputText extends InputGeneric
{
    public function __construct( $name, $value = '', $size = '', $maxlength = '', $extra = '', $password = false )
    {
        $type = $password ? 'password' : 'text';
        
        parent::__construct( $type, $name, $value );
        
        if (!empty($size)) $this->setAttribute( 'size', $size );
        if (!empty($maxlength)) $this->setAttribute( 'maxlength', $maxlength );
        if (is_array($extra)) $this->appendAttributes( $extra );
    }
}

class InputPassword extends InputText
{
    public function __construct( $name, $value = '', $size = '', $maxlength = '', $extra = '' )
    {
        parent::__construct( $name, $value, $size, $maxlength, $extra, true );
    }
}

class InputSubmit extends InputGeneric
{
    public function __construct( $name, $value, $onsubmit = '', $extra = '' )
    {
        parent::__construct( 'submit', $name, $value );
        
        if (!empty($onsubmit)) $this->setAttribute( 'onsubmit', $onsubmit );
        if (is_array($extra)) $this->appendAttributes( $extra );
    }
}

class InputButton extends InputGeneric
{
    public function __construct( $name, $value, $onclick = '', $extra = '' )
    {
        parent::__construct( 'button', $name, $value );
        
        if (!empty($onclick)) $this->setAttribute( 'onclick', $onclick );
        if (is_array($extra)) $this->appendAttributes( $extra );
    }
}

class InputCancel extends InputButton
{
    protected $location;
    
    public function __construct( $name, $value, $location = '', $extra = '' )
    {
        $this->location = empty($location)? $_SERVER['PHP_SELF'] : $location;
        $onclick = "window.location='{$this->location}'";
            
        parent::__construct( $name, $value, $onclick, $extra );
    }
    
    public function render()
    {
        return "<a href=\"{$this->location}\">"
            . parent::render()
            . "</a>"
            ;
    }
}

class InputFile extends InputGeneric
{
    public function __construct( $name, $value = '', $extra = '' )
    {
        parent::__construct( 'file', $name, $value, $extra );
    }
}

class InputImage extends InputGeneric
{
    public function __construct( $name, $src, $value = '', $extra = '' )
    {
        parent::__construct( 'image', $name, $value );
        
        $this->setAttribute( 'src', $src );
        if (is_array($extra)) $this->appendAttributes( $extra );
    }
}

class InputHidden extends InputGeneric
{
    public function __construct( $name, $value, $extra = '' )
    {
        parent::__construct( 'hidden', $name, $value, $extra );
    }
}

class InputRadio extends InputGeneric
{
    public function __construct( $name, $value, $label = '', $checked = false, $extra = '' )
    {
        parent::__construct( 'radio', $name, $value, $extra );
        
        if ( $checked ) $this->checked();
        
        if ( !empty($label) ) $this->setLabel( $label, true );
    }
}

class InputCheckbox extends InputGeneric
{
    public function __construct( $name, $value, $label = '', $checked = false, $extra = '' )
    {
        parent::__construct( 'checkbox', $name, $value, $extra );
        
        if ( $checked ) $this->checked();
        
        if ( !empty($label) ) $this->setLabel( $label, true );
    }
}

class InputRadioList
{
    protected $radioList = array();
    protected $name, $checked;
    
    public function __construct( $name, $list, $checked = '' )
    {
        $this->name = $name; 
        $this->checked = $checked;
        
        foreach ( $list as $value => $label )
        {
            $this->radioList[] = new InputRadio( $name, $value, $label, ($checked==$value) );
        }           
    }
    
    public function render()
    {
        $ret = '';
        
        foreach ( $this->radioList as $radio )
        {
            $ret .= $radio->render() . "<br />\n";
        }
        
        return $ret;
    }
}

class InputCheckboxList
{
    protected $checkboxList = array();
    protected $name, $checked;
    
    public function __construct( $name, $list, $checked = '' )
    {
        $this->name = $name; 
        $this->checked = $checked;
        
        foreach ( $list as $value => $label )
        {
            $this->checkboxList[] = new InputCheckbox( $name, $value, $label, ($checked==$value) );
        }           
    }
    
    public function render()
    {
        $ret = '';
        
        foreach ( $this->checkboxList as $checkbox )
        {
            $ret .= $checkbox->render() . "<br />\n";
        }
        
        return $ret;
    }
}

class Textarea extends OpenCloseFormElement
{
    public function __construct( $name, $value, $rows, $cols, $extra = '' )
    {
        parent::__construct( 'textarea' );
        
        $this->setAttribute( 'name', $name );
        $this->setAttribute( 'rows', $rows );
        $this->setAttribute( 'cols', $cols );
        
        if ( is_array($extra) ) $this->appendAttributes( $extra );
        
        $this->setContent( $value );
    }
}

class SelectElement extends OpenCloseHtmlElement
{
    public function disable()
    {
        $this->setAttribute( 'disabled', 'disabled' );
    }
    
    public function enable()
    {
        $this->unsetAttribute( 'disabled' );
    }
}

class Option extends SelectElement
{
    public function __construct( $label, $value, $extra = '' )
    {
        parent::__construct( 'option' );
        
        $this->setAttribute( 'value', $value );
        
        if ( is_array($extra) ) $this->appendAttributes( $extra );
        
        $this->setContent( $label );
    }
    
    public function selected()
    {
        $this->attributes['selected'] = 'selected';
    }
}

class Optgroup extends SelectElement
{
    protected $elements = array();
    
    public function __construct( $label, $extra = '' )
    {
        parent::__construct( 'optgroup' );
        
        $this->setAttribute( 'label', $label );
        
        if ( is_array($extra) ) $this->appendAttributes( $extra );
    }
    
    public function addOption( $element )
    {
        $this->elements[] = $element;
    }
    
    public function render()
    {
        $this->content = "\n";
        
        foreach ( $this->elements as $element )
        {
            $this->content .= $element->render()."\n";
        }
        
        return parent::render();
    }
}

class SelectBox extends OpenCloseFormElement
{
    protected $elements = array();
    
    public function __construct( $name, $extra = '' )
    {
        parent::__construct( 'select' );
        
        $this->setAttribute( 'name', $name );
        
        if ( is_array($extra) ) $this->appendAttributes( $extra );
    }
    
    public function addOption( $element )
    {
        $this->elements[] = $element;
    }
    
    public function render()
    {
        $this->content = "\n";
        
        foreach ( $this->elements as $element )
        {
            $this->content .= $element->render()."\n";
        }
        
        return parent::render();
    }
    
    public static function fromArray( $name, $optionList, $selectedValue, $extra = '' )
    {
        $select = new Select( $name, $extra );
        
        foreach ( $optionList as $value => $label )
        {
            $option = new Option( $label, $value );
            if ( $value == $selectedValue ) $option->selected();
            $select->addOption( $option );
        }
        
        return $select;
    }
}

class CsrfToken extends InputHidden
{
    protected $token;
    protected $time;
    
    public function __construct()
    {
        $this->token = md5(uniqid(rand(),true));
        $this->time = time();
        
        parent::__construct( 'token', $this->token, array( 'id' => '_token' ) );
    }
    
    public function getToken()
    {
        return $this->token;
    }
    
    public function getTime()
    {
        return $this->time;
    }
    
    public static function checkToken( $tokenToCheck, $csrfToken, $csrfTime )
    {
        if ( $csrfToken != $tokenToCheck
            || ( time() - $csrfTime ) > 60 )
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}

class FormUniqid extends InputHidden
{
    protected $formId;
    
    public function __construct()
    {
        $this->formId = uniqid('');
        parent::__construct( 'formuniqid', $this->formId, array( 'id' => '_formuniqid' ) );
    }
    
    public function getUniqid()
    {
        return $this->formId;
    }
}
