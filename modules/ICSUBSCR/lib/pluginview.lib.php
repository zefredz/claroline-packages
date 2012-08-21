<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.1 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

abstract class pluginView extends ICSUBSCR_View
{
    public function __construct()
    {
        $templateList = array( 'subscribe' , 'edit' , 'result' );
        $templatePath = '/plugins/icsubscr.plugin.'
            . strtolower( substr( get_class( $this ) , 0 , -4 ) );
        
        $cmdList = array(
            array(
                'img'  => 'new',
                'name' => get_lang( 'Edit choices' ),
                'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'ICSUBSCR' )
                        .'/index.php?cmd=rqEditSlot' ) ) ),
            array(
                'img'  => 'back',
                'name' => get_lang( 'Add a new choice' ),
                'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'ICSUBSCR' )
                        .'/index.php?cmd=rqAddSlot' ) ) ) );
        
        parent::__construct( $templateList , $cmdList , $templatePath );
    }
}