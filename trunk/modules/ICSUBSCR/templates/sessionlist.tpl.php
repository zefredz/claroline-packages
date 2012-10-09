<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Session' ); ?></th>
            <th><?php echo get_lang( 'Start date' ); ?></th>
            <th><?php echo get_lang( 'End date' ); ?></th>
        <?php if( claro_is_allowed_to_edit() ) : ?>
            <th><?php echo get_lang( 'Actions' ); ?></th>
            <th><?php echo get_lang( 'Rank' ); ?></th>
        <?php else : ?>
            <th><?php echo get_lang( 'Status' ); ?></th>
        <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php if( $this->model->notEmpty() ) : ?>
        <?php foreach( $this->model->getItemList( $this->resetView ) as $session ) : ?>
        <tr>
            <td><a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?sessionId='. $session->getId() . '&sessionType=' . $session->get( 'type' ) ) );?>"><?php echo $session->get( 'title' ); ?></a></td>
            <td><?php echo $session->get( 'startDate' ) ? claro_html_localised_date( '%a %d %b %Y' , strtotime( $session->get( 'startDate' ) ) ) : 'no date'; ?></td>
            <td><?php echo $session->get( 'endDate' ) ? claro_html_localised_date( '%a %d %b %Y' , strtotime( $session->get( 'endDate' ) ) ) : 'no date'; ?></td>
            <?php if( claro_is_allowed_to_edit() ) : ?>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditSession&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteSession&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
                    <?php if ( $session->isOpen() ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exLock&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'unlock' ); ?>" alt="<?php echo get_lang( 'lock' ); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exUnlock&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Unlock' ); ?>"/>
                </a>
                    <?php endif; ?>
                    <?php if ( $session->isVisible() ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exHide&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible' ); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exShow&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible' ); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
            <td align="center">
                    <?php if( $this->model->getRank( $session->getId() ) != 1 ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMoveUp&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'move_up' ); ?>" alt="<?php echo get_lang( 'Move up' ); ?>"/>
                </a>
                    <?php endif; ?>
                    <?php if( $this->model->getRank( $session->getId() ) != $this->model->getMaxRank() ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMoveDown&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'move_down' ); ?>" alt="<?php echo get_lang( 'Move down' ); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
            <?php else : ?>
            <td align="center">
                <?php if( $this->model->isAvailable( $session->getId() ) ) : ?>
                <img src="<?php echo get_icon_url( 'unlock' ); ?>" alt="<?php echo get_lang( 'Open' ); ?>"/>
                <?php else : ?>
                <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Closed' ); ?>"/>
                <?php endif; ?>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="<?php echo claro_is_allowed_to_edit() ? 5 : 3; ?>"  align="center"><span class="empty"><?php echo get_lang( 'Empty' ); ?></span></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>