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

?>
<div class="claroDialogBox boxWarning">
    <div class="claroDialogMsg msgForm">
    <?php echo get_lang( 'Are you sure that you want to delete the slot <strong>%slotTitle</strong> in session <strong>%sessionTitle</strong> ?', array( '%slotTitle' => $this->slot->getTitle(), '%sessionTitle' => $this->subscription->getTitle() ) )
        .     '<br /><br />'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exSlotDelete&amp;slotId=' . $this->slot->getId() . '&amp;subscrId=' . $this->subscription->getId() . claro_url_relay_context( '&' ) . '">' . get_lang('Yes') . '</a>'
        .    '&nbsp;|&nbsp;'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang('No') . '</a>'
        ;
    ?>
    </div>
</div>