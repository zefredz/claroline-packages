<?php // $Id$

/**
 * Subscription
 *
 * @version     CLSUBSCR 0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */

if( isset( $this->subscription ) ) :
    /*if( ! claro_is_allowed_to_edit() && $this->subscription['visibility'] == 'invisible' ) :
        claro_die( get_lang( 'Not allowed' ) );
    endif;*/
    
    $subscription = $this->subscription;
    if( $this->subscription['lock'] == 'close' ) :
?>
<div class="claroDialogBox boxWarning">
    <?php echo get_lang( 'This subscription is locked. You can\'t update your choice.' ); ?>    
</div>
<?php
    endif;
endif;
?>
<div class="claroDialogBox">
    <?php
    if( claro_is_allowed_to_edit() ) :
    ?>
    <div style="float: right;">
        <?php
        if( ! $subscription['isVisible'] ) :
        ?>
        <span style="font-weight: bold; color: red;">
        <?php
            echo get_lang( 'This session is only available' );
        ?>
        <?php
            echo availability_date( $subscription['visibilityFrom'] , $subscription[ 'visibilityTo' ] );
        ?>
        </span>
        <?php
        endif;
        ?>
        <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqEdit&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>" /></a>
        <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqDelete&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>" /></a>
        <?php if ( $subscription[ 'visibility' ] == 'visible' ) : ?>
        <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=exInvisible&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible' ); ?>" /></a>
        <?php else : ?>
        <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=exVisible&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible' ); ?>" /></a>
        <?php endif; ?>
        <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=exLock&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo $subscription['lock'] == 'close'? get_icon_url( 'locked' ) : get_icon_url( 'unlock'); ?>" alt="<?php echo $subscription['lock'] == 'close' ? get_lang( 'Locked' ) : get_lang('Unlock'); ?>" /></a>
        <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqResult&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo get_icon_url( 'statistics'); ?>" alt="<?php echo get_lang( 'Result' ); ?>" /></a>
    </div>
    <?php
    elseif( $subscription['lock'] == 'close' ) :
    ?>
    <div style="float: right;">
        <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Locked' ); ?>" />
    </div>
    <?php
    endif;
    ?>
    <?php if ( $subscription['type'] == 'group' ) : ?>
    <img src="<?php echo get_icon_url( 'group' ); ?>" alt="<?php echo get_lang( 'Group' ); ?>" />
    <?php else: ?>
    <img src="<?php echo get_icon_url( 'user' ); ?>" alt="<?php echo get_lang( 'User' ); ?>" />
    <?php endif; ?>
    <span class="msgTitle"><?php echo $subscription['title']; ?></span>
    <div style="clear: both;"></div>
    <div style="margin: 1px; padding: 0 5px 0 5px ; border: 1px #CCC solid;">
        <?php echo $subscription['description']; ?>
    </div>
    <div>
        <?php
        if( $subscription['slotsAvailable'] > 1 ):
            echo get_lang( 'Information about the session : %slotsAvailable slots are still available on %totalSlotsAvailable', array( '%slotsAvailable' => $subscription['slotsAvailable'], '%totalSlotsAvailable' => $subscription['totalSlotsAvailable'] ) );
        elseif( $subscription['slotsAvailable'] > 0 ):
            echo get_lang( 'Information about the session : %slotsAvailable slot is still available on %totalSlotsAvailable', array( '%slotsAvailable' => $subscription['slotsAvailable'], '%totalSlotsAvailable' => $subscription['totalSlotsAvailable'] ) );
        else:
            echo get_lang( 'Information about the session : no more slot available.' );
        endif;
        ?>
    </div>
    <?php
    if( isset( $this->displayChoice ) && $this->displayChoice == true ) :
    ?>
    <div style="padding-top: 3px;">
        <?php
        if( isset( $this->userChoices[ $subscription['id'] ] ) ) :
            echo get_lang( 'My choice :') . ' ';
            foreach( $this->userChoices[ $subscription['id'] ] as $slot ) :
                echo $slot['title'];
            endforeach;
        ?>
        <?php if ( $subscription['modifiable'] == 'modifiable' || claro_is_allowed_to_edit() ) : ?>
        (<a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqSlotChoice&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><?php echo get_lang( 'Modify' ); ?></a>)
        <?php endif; ?>
        <?php
        else :
        ?>
        <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqSlotChoice&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo get_icon_url( 'enroll' ); ?>" alt="" /> <?php echo get_lang( 'Make a choice' ); ?></a>
        <?php
        endif;
        
        if( claro_is_allowed_to_edit() ) :
        ?>
        | <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqSlotChoice&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><?php echo get_lang( 'Edit proposed slots' ); ?></a>
        | <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqResult&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo get_icon_url( 'statistics' ); ?>" alt="" /> <?php echo get_lang( 'Show results' ); ?></a>
        <?php
        endif;
        ?>
    </div>
    <?php
    endif;
    ?>
</div>