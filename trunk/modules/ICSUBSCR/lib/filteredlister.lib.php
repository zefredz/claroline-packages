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

abstract class FilteredLister extends Lister
{
    /**
     * Helper for add() with allowed fields and default values
     * @param array $data
     * @return int $id : the item's id
     */
    public function create( $data )
    {
        return $this->add( $data , self::$allowedFields );
    }
}