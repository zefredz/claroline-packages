<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.2 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

abstract class ICSUBSCR_View
{
    protected $subTitle = '';
    protected $cmdList;
    protected $templateList;
    protected $templatePath;
    
    public $selectedView = 0;
    public $activeCmdList = array();
    
    public function __construct( $templateList , $cmdList , $templatePath = '' )
    {
        $this->templateList = $templateList;
        $this->cmdList = $cmdList;
        $this->templatePath = $templatePath;
    }
    
    public function getToolTitle()
    {
        $title = array(
            'mainTitle' => get_lang( 'Subscriptions' ),
            'subTitle' => get_lang( $this->subTitle ) );
        
        $this->setCmdList();
        
        $toolTitle = new ToolTitle( $title , null , $this->activeCmdList );
        
        return $toolTitle;
    }
    
    public function get()
    {
        return new PhpTemplate( get_module_path( 'ICSUBSCR' )
            . $this->templatePath
            . '/templates/'
            . $this->templateList[ $this->selectedView ]
            . '.tpl.php' );
    }
    
    protected function addCmd( $index )
    {
        $this->activeCmdList[] = $this->cmdList[ $index ];
    }
    
    protected abstract function setCmdList();
}