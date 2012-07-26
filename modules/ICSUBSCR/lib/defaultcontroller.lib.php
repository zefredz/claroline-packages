<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class DefaultController extends ICSUBSCR_Controller
{
    protected static $templateList = array( 'rqShowList' , 'rqCreateSession' );
    
    protected $sessionList;
    protected $pluginLoader;
    
    public function __construct( $sessionList , $pluginLoader )
    {
        parent::__construct( $sessionList );
        $this->pluginLoader = $pluginLoader;
    }
    
    private function rqViewSession()
    {
        $sessionType = $this->model->get( $sessionId , 'type' );
        return $this->pluginLoader->get( $sessionType );
    }

}