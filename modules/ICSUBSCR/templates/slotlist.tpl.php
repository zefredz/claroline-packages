<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Label' ); ?></th>
        <?php if( $this->session->getType() != Session::TYPE_UNDATED ) : ?>
            <th><?php echo get_lang( 'Start date' ); ?></th>
            <th><?php echo get_lang( 'End Date' ); ?></th>
        <?php endif; ?>
            <th><?php echo get_lang( 'Total available space' ); ?></th>
            <th><?php echo get_lang( 'Remaining space' ); ?></th>
            <th><?php echo get_lang( 'Action' ); ?></th>
            <th><?php echo get_lang( 'Rank' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if( count( $this->session->getSlotList( true ) ) ) : ?>
        <?php foreach( $this->session->getSlotList() as $slot ) : ?>
        <tr>
            <td><?php echo $slot->getLabel(); ?></td>
            <td align="center">
                <img src="<?php echo get_icon_url( $slot->getContext() == Session::CONTEXT_GROUP ? 'group' : 'user' ); ?>" alt="<?php echo get_lang( $slot->getContext() ); ?>" />
            </td>
            <td>
                <?php echo get_lang( $slot->getType() ); ?>
            </td>
            <td><?php echo $slot->getStartDate() ? claro_html_localised_date( '%a %d %b %Y' , strtotime( $slot->getStartDate() ) ) : 'no date'; ?></td>
            <td><?php echo $slot->getEndDate() ? claro_html_localised_date( '%a %d %b %Y' , strtotime( $slot->getEndDate() ) ) : 'no date'; ?></td>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqModifySession&sessionId='. $slot->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteSession&sessionId='. $slot->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
                    <?php if ( $slot->isVisible() ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exHideSession&sessionId='. $slot->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible' ); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exShowSession&sessionId='. $slot->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible' ); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
            <td align="center">
                    <?php if( $this->sessionList->getRank( $slot->getId() ) != 1 ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMoveSessionUp&sessionId='. $slot->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'move_up' ); ?>" alt="<?php echo get_lang( 'Move up' ); ?>"/>
                </a>
                    <?php endif; ?>
                    <?php if( $this->sessionList->getRank( $slot->getId() ) != $this->sessionList->getMaxRank() ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMoveSessionDown&sessionId='. $slot->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'move_down' ); ?>" alt="<?php echo get_lang( 'Move down' ); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="7" align="center"><span class="empty"><?php echo get_lang( 'Empty' ); ?></span></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>