<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Datagrid library
 *
 * @version     1.8-backport $Revision$
 * @copyright   2001-2007 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     icprint
 */

interface Claro_Renderer
{
    public function render();
}

class Claro_Html_Element implements Claro_Renderer
{
    protected static $ids = array();
    
    protected $autoClose;
    protected $name;
    protected $attributes;
    protected $content;
    
    public function __construct( $name, $attributes = null, $autoClose = false )
    {
        if ( !is_array( $attributes ) || empty( $attributes ) )
        {
            $attributes = array();
        }
        
        if ( array_key_exists( 'id', $attributes ) )
        {
            if ( in_array( $attributes['id'], self::$ids ) )
            {
                throw new Exception("A html element of id {$attributes['id']} already exists");
            }
            else
            {
                self::$ids[] = $attributes['id'];
            }
        }
        
        $this->name = $name;
        $this->attributes = $attributes;
        $this->autoClose = $autoClose;
        $this->content = '';
    }
    
    public function __destruct()
    {
        if ( array_key_exists( 'id', $this->attributes ) )
        {
            if ( in_array( $this->attributes['id'], self::$ids ) )
            {
                foreach ( self::$ids as $key => $value )
                {
                    if ( $value == $this->attributes['id'] )
                    {
                        unset ( self::$ids[$key] );
                        break;
                    }
                }
            }
        }
    }
    
    public function setContent( $content )
    {
        $this->content = $content;
    }
    
    public function render()
    {
        return "<{$this->name}"
            . ( !empty( $this->attributes )
                ? $this->formatAttributes( $this->attributes ) 
                : '' )
            . ( $this->autoClose
                ? " />"
                : ">{$this->content}</{$this->name}>" )
            ;
    }
    
    public function formatAttributes( $attributes )
    {
        if ( empty( $attributes ) )
        {
            return '';
        }
        else
        {
            $attribs = '';
            
            foreach ( $attributes as $key => $value )
            {
                if ( $value )
                {
                    $attribs .= " {$key}=\"{$value}\"";
                }
            }
            
            return $attribs;
        }
    }
    
    public function getId()
    {
        if ( array_key_exists( 'id', $this->attributes ) )
        {
            return $this->attributes['id'];
        }
        else
        {
            return null;
        }
    }
    
    public function getAttr( $name )
    {
        if ( array_key_exists( $name, $this->attributes ) )
        {
            return $this->attributes[$name];
        }
        else
        {
            return null;
        }
    }
    
    public function setAttr( $name, $value )
    {
        $this->attributes[$name] = $value;
    }
}

class Claro_Utils_Datagrid extends Claro_Html_Element
{
    protected $lineNumber = 0;
    protected $lineCount = 0;
    
    protected $columnsLabels = array();
    protected $columnsValues = array();
    protected $columnsOrder = array();
    protected $rows = array();
    
    protected $title = '';
    protected $footer = '';
    protected $emptyMessage = '';
    
    public function __construct( $attributes = null )
    {
        parent::__construct( 'table', $attributes );
    }
    
    public function setTitle( $title )
    {
        $this->title = $title;
    }
    
    public function setFooter( $footer )
    {
        $this->footer = $footer;
    }
    
    public function setEmptyMessage( $emptyMessage )
    {
        $this->emptyMessage = $emptyMessage;
    }
    
    public function setRows( array $rows )
    {
        $this->rows = $rows;
        $this->lineCount = count( $rows );
    }
    
    public function prependColumn( $key, $label, $value )
    {
        $this->columnsLabels[$key] = $label;
        $this->columnsValues[$key] = $value;
        array_unshift( $this->columnsOrder, $key );
    }
    
    public function addColumn( $key, $label, $value  )
    {
        $this->columnsLabels[$key] = $label;
        $this->columnsValues[$key] = $value;
        array_push( $this->columnsOrder, $key );
    }
    
    public function addDataColumn( $key, $label  )
    {
        $this->addColumn( $key, $label, "%html($key)%" );
    }
    
    public function getColumnsCount()
    {
        return count( $this->columnsOrder );
    }
    
    public function getRowsCount()
    {
        return count( $this->rows );
    }
    
    protected function renderHeader()
    {
        $header = !empty($this->title) 
            ? "<caption>{$this->title}</caption>\n" 
            : '' 
            ;
            
        $header .= "<thead>\n<tr>"; 
        
        foreach ( $this->columnsOrder as $column )
        {
            $header .= "<th>{$this->columnsLabels[$column]}</th>";
        }
        
        $header .= "</tr>\n</thead>\n";
        
        return $header;
    }
    
    protected function renderBody()
    {
        if ( ! count( $this->rows ) )
        {
            return ( !empty($this->emptyMessage)
                ? "<tbody><tr><td colspan=\"{$this->getColumnsCount()}\">{$this->emptyMessage}</td></tr></tbody>\n"
                : "<tbody><!-- empty --></tbody>\n" )
                ;
        }
        else
        {
            $tbody = "<tbody>\n";
            
            foreach ( $this->rows as $row )
            {
                $tableRow = '';
                
                foreach ( $this->columnsOrder as $column )
                {
                    $tableRow .= "<td>"
                        . str_replace( '%_lineCount_%'
                            , $this->lineCount,
                            str_replace( '%_lineNumber_%'
                                , $this->lineNumber
                                , $this->columnsValues[$column] ) )
                        ."</td>"
                        ;
                }
                
                foreach ( $row as $key => $value )
                {
                    $tableRow = $this->replace( $key, $value, $tableRow );
                }
                
                $tbody .= "<tr>{$tableRow}</tr>\n";
                $this->lineNumber++;
            }
            
            $tbody .= "</tbody>\n";
            
            return $tbody;
        }
    }
    
    protected function renderFooter()
    {
        return !empty($this->footer)
            ? "<tfoot>\n<tr><td colspan=\"{$this->getColumnsCount()}\">{$this->footer}</td></tr>\n</tfoot>\n"
            : ''
            ;
    }
    
    public function render()
    {
        $this->setContent( $this->renderHeader().$this->renderFooter().$this->renderBody() );
        
        return parent::render();
    }
    
    protected function replace( $key, $value, $output )
    {
        $output = str_replace( "%$key%", $value, $output );
        $output = str_replace( "%html($key)%", htmlspecialchars( $value ), $output );
        $output = str_replace( "%uu($key)%", rawurlencode( $value ), $output );
        $output = str_replace( "%int($key)%", (int) $value, $output );
        
        return $output;
    }
}

class Claro_Utils_Autogrid extends Claro_Utils_Datagrid
{
    public function setRows( array $rows )
    {
        if ( !empty ( $rows ) )
        {
            $this->rows = $rows;
            
            $this->columnsOrder = array_merge( $this->columnsOrder, array_keys( $rows[0] ) );
            
            foreach ( array_keys( $rows[0] ) as $column )
            {
                $this->columnsLabels[$column] = htmlspecialchars( $column );
                $this->columnsValues[$column] = "%html({$column})%";
            }
        }
    }
}

class Claro_Utils_Clarogrid extends Claro_Utils_Datagrid
{
    protected $superHeader = '';
    
    public function __construct()
    {
        parent::__construct( array(
            'class' => 'claroTable'
        ) );
    }
    
    public function setSuperHeader( $superHeader )
    {
        $this->superHeader = $superHeader;
    }
    
    public function emphaseLine()
    {
        $this->attributes['class'] = 'claroTable emphaseLine';
    }
    
    public function fullWidth()
    {
        $this->attributes['style'] = 'width: 100%';
    }
    
    protected function renderHeader()
    {
        $header = !empty($this->title) 
            ? "<caption>{$this->title}</caption>\n" 
            : '' 
            ;
            
        $header .= "<thead>\n";
        
        $header .= ( !empty($this->superHdr) 
                ? "<tr class=\"superHeader\">\n"
                    . "<td colspan=\"{$this->getColumnsCount()}\">{$this->superHeader}</td>\n"
                    . "</tr>\n"
                : '' )
                ;

        $header .= "<tr class=\"headerX\">"; 
        
        foreach ( $this->columnsOrder as $column )
        {
            $header .= "<th>{$this->columnsLabels[$column]}</th>";
        }
        
        $header .= "</tr>\n</thead>\n";
        
        return $header;
    }
}
