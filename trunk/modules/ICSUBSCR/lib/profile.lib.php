<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class profile
{
    protected $name;
    protected $defaultOptionList;
    protected $template;
    
    public function __construct( $fileName = null )
    {
        if ( $fileName )
        {
            $this->fileName = $fileName;
            $this->load();
        }
    }
    
    public function load()
    {
        $xmlElement = simplexml_load_file( $this->fileName );
        
        $this->name = (string)$xmlElement->name;
        
        foreach( $xmlElement->defaultOptionList[0] as $option )
        {
            $this->authorizedFileList[ (string)$option->name ] = (string)$option->vallue;
        }
        
        $this->template = (string)$xmlElement->template;
    }
    
    public function getName()
    {
        return $this->name;
    }
}