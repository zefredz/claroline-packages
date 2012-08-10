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

abstract class pluginView extends ICSUBSCR_View
{
    public function __construct( $cmdList )
    {
        $templateList = array( 'subscribe' , 'edit' , 'result' );
        $templatePath = '/plugins/icsubscr.' . get_class() . '.plugin/';
        
        parent::__construct( $templateList , $cmdList , $templatePath );
    }
}