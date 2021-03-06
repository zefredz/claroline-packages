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
    
    // var_dump($this->subscription);
    
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
        if( ! $subscription['isAvailable'] ) :
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
    <div>
    <?php if ( $subscription['context'] == 'group' ) : ?>
    <img src="<?php echo get_icon_url( 'group' ); ?>" alt="<?php echo get_lang( 'Group' ); ?>" />
    <?php else: ?>
    <img src="<?php echo get_icon_url( 'user' ); ?>" alt="<?php echo get_lang( 'User' ); ?>" />
    <?php endif; ?>
    <span class="msgTitle"><?php echo $subscription['title']; ?></span>
    </div>
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
            echo '<strong><span class="error">' . get_lang( 'Information about the session : no more slot available.' ) . '</span></strong>';
        endif;
        ?>
    </div>
    
    <?php if( isset( $this->displayChoice ) && $this->displayChoice == true ) : ?>
    
    <div style="padding-top: 3px;">
        
        <?php if( isset( $this->userChoices[ $subscription['id'] ] ) ) : ?>
        
            <?php echo get_lang( 'My choice :') . ' '; ?>
        
            <?php foreach( $this->userChoices[ $subscription['id'] ] as $slot ) : ?>
        
                <?php echo $slot['title']; ?>
        
            <?php endforeach; ?>
        
            <?php if ( ( $subscription['isAvailable'] &&  $subscription['modifiable'] == 'modifiable' ) 
                || claro_is_allowed_to_edit() ) : ?>
                    (<a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqSlotChoice&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><?php echo get_lang( 'Modify' ); ?></a>)
            <?php endif; ?>
        
        <?php else : ?>
        
            <?php if ( $subscription['isAvailable'] || claro_is_allowed_to_edit() ): ?>
                <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqSlotChoice&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo get_icon_url( 'enroll' ); ?>" alt="" /> <?php echo get_lang( 'Make a choice' ); ?></a>
            <?php else: ?>
                <span style="font-weight: bold; color: red;">
                <?php
                    echo get_lang( 'This session is only available' );
                ?>
                <?php
                    echo availability_date( $subscription['visibilityFrom'] , $subscription[ 'visibilityTo' ] );
                ?>
                </span>
            <?php endif; ?>
            
        <?php endif; ?>
        
        
        <?php if( claro_is_allowed_to_edit() ) : ?>
        | <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqSlotChoice&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><?php echo get_lang( 'Edit proposed slots' ); ?></a>
        | <a href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] . '?cmd=rqResult&subscrId=' . $subscription['id'] . claro_url_relay_context( '&' ) ); ?>"><img src="<?php echo get_icon_url( 'statistics' ); ?>" alt="" /> <?php echo get_lang( 'Show results' ); ?></a>
        <?php endif; ?>
        
        
    </div>
    
    <?php endif; ?>
</div>