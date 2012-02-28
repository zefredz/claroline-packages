<?php

class ICADDEXT_View extends ModuleTemplate
{
    const LABEL = 'ICADDEXT';
    const ERROR = 'error';
    
    public static $templateList  = array( 'rqAdd' => 'submit'
                                        , 'rqSelect' => 'select'
                                        , 'exAdd' => 'report' );
    
    public $controller;
    protected $templateName;
    
    public function __construct( $cmd , $controller )
    {
        $this->controller = $controller;
        $this->controller->execute( $cmd );
        
        $this->templateName = array_key_exists( $cmd , self::$templateList )
                            && $this->controller->is_ok()
                            ? self::$templateList[ $cmd ]
                            : self::ERROR;
        
        parent::__construct( self::LABEL , $this->templateName . '.tpl.php' );
    }
}