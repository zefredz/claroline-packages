<?php // $Id$

class ICADDEXT_View extends ModuleTemplate
{
    const LABEL = 'ICADDEXT';
    const ERROR = 'error';
    
    public static $templateList  = array( 'submit' => 'submit'
                                        , 'rqFix' => 'fix'
                                        , 'exFix' => 'fix'
                                        , 'rqAdd' => 'add'
                                        , 'exAdd' => 'report' );
    
    public $controller;
    protected $templateName;
    
    public function __construct( $controller )
    {
        $this->controller = $controller;
        
        $this->templateName = array_key_exists( $this->controller->cmd , self::$templateList )
                            && $this->controller->is_ok()
                            ? self::$templateList[ $this->controller->cmd ]
                            : self::ERROR;
        
        parent::__construct( self::LABEL , $this->templateName . '.tpl.php' );
    }
}