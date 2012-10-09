<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.4 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class SessionList extends Lister
{
    const CONTEXT = 'context';
    const ENUM_CONTEXT_USER = 'user';
    const ENUM_CONTEXT_GROUP = 'group';
    
    public $typeList;
    
    /**
     * Constructor
     * @param string $context : the actual context
     */
    public function __construct( $context = self::ENUM_CONTEXT_USER , $typeList , $allowedToEdit = false )
    {
        $tbl = get_module_course_tbl( array( 'icsubscr_session' ) );
        
        $this->typeList = $typeList;
        
        $filter = array( self::CONTEXT => $context );
        
        if( ! $allowedToEdit )
        {
            $filter[ self::PARAM_VISIBILITY ] = self::ENUM_VISIBILITY_VISIBLE; 
        }
        
        parent::__construct( new Session , $filter );
    }
}