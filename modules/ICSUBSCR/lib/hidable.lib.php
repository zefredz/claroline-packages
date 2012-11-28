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

abstract class Hidable
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
        if( ! $this->getId() )
        {
            throw new Exception( 'Session does not exist' );
        }
        
        if( Claroline::getDatabase()->exec( "
            UPDATE
                `{$this->getDatabaseTable()}`
            SET
                is_visible = " . Claroline::getDatabase()->quote( (boolean)$is_visible ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->getId() ) ) )
        {
            $this->is_visible = $is_visible;
            
            return true;
        }
        else
        {
            return false;
        }
    }
    
    abstract protected function getDatabaseTable();
    abstract public function getId();
}