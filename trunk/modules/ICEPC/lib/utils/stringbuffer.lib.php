<?php

// $Id$

class Claro_StringBuffer implements Display
{
    private $out;
    
    public function __construct( $str = '' )
    {
        $this->out = $str;
    }
    
    public function appendContent( $str )
    {
        $this->out .= $str;
    }
    
    public function prependContent( $str )
    {
        $this->out .= $str . $this->out;
    }
    
    public function render()
    {
        return $this->out;
    }
}
