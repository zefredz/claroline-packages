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

class Hidable
{
    public function isVisible()
    {
        return $this->is_visible === true;
    }
    
    public function show()
    {
        return $this->setVisibility( true );
    }
    
    public function hide()
    {
        return $this->setVisibility( false );
    }
    
    private function setVisibility( $is_visible = false )
    {
        if( ! $this->id )
        {
            throw new Exception( 'Session does not exist' );
        }
        
        $visibility = $is_visible === true ? false : true;
        
        return Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->tbl}`
            SET
                is_visible = " . Claroline::getDatabase()->escape( $visibility ) ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->id );
    }
}