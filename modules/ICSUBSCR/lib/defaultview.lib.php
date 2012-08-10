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

class DefaultView extends ICSUBSCR_View
{
    public function __construct()
    {
        $templateList = array( 'sessionlist' , 'editsession' );
        $cmdList = array(
            array(
                'img'  => 'new',
                'name' => get_lang( 'Create a new session' ),
                'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'ICSUBSCR' )
                        .'/index.php?cmd=rqCreateSession' ) ) ),
            array(
                'img'  => 'back',
                'name' => get_lang( 'Back to the session list' ),
                'url'  => htmlspecialchars( Url::Contextualize( get_module_url( 'ICSUBSCR' )
                        .'/index.php?cmd=rqShowSessionList' ) ) ) );
        
        parent::__construct( $templateList , $cmdList );
    }
    
    protected function setCmdList()
    {
        switch( $this->selectedView )
        {
            case 0:
                if( claro_is_allowed_to_edit() )
                {
                    $this->addCmd( 0 );
                }
                break;
            
            case 1:
                $this->addCmd( 1 );
                break;
            
            default:
                throw new Exception( 'Template does not exist' );
        }
    }
}