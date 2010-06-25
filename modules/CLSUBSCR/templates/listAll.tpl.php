<?php // $Id$

/**
 * Subscription
 *
 * @version     CLSUBSCR 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */

foreach( $this->subscriptions as $subscription ) :
    if( claro_is_allowed_to_edit() || $subscription['isVisible'] == true ) :
        include( dirname( __FILE__ ) . '/subscription.tpl.php' );
    endif;
endforeach;
?>