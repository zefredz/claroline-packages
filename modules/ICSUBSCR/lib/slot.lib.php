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

class Slot
{
    const ID = 'id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const START_DATE = 'startDate';
    const AVAILABLE_SPACE = 'availableSpace';
    const VISIBILITY = 'visibility';
    
    const ENUM_VISIBLE = 'visible';
    const ENUM_INVISIBLE = 'invisible';
    
    protected $data;
    
    public function load( $data )
    {
        $this->data = $data;
    }
    
    public function get( $name )
    {
        if( array_key_exists( $name , $this->data ) )
        {
            return $this->data[ $name ];
        }
    }
    
    public function getId()
    {
        return get( self::ID );
    }
    
    public function getTitle()
    {
        return get( self::TITLE );
    }
    
    public function getDescription()
    {
        return get( self::DESCRIPTION );
    }
    
    public function getDate()
    {
        return get( self::START_DATE );
    }
    
    public function getAvailableSpace()
    {
        return get( self::AVAILABLE_SPACE );
    }
}