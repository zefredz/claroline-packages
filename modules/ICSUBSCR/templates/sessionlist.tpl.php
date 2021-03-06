<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Session' ); ?></th>
        <?php if( claro_is_allowed_to_edit() ) : ?>
            <th><?php echo get_lang( 'Context' ); ?></th>
            <th><?php echo get_lang( 'Session type' ); ?></th>
        <?php endif; ?>
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
    <?php if( count( $this->sessionList->getItemList( true ) ) ) : ?>
        <?php foreach( $this->sessionList->getItemList() as $session ) : ?>
        <?php if( $session->isVisible() || claro_is_allowed_to_edit() ) : ?>
        <tr>
            <td>
            <?php if( claro_is_allowed_to_edit() || $session->isAvailable() ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?sessionId='. $session->getId() . '&sessionType=' . $session->getType() ) );?>"><?php echo $session->getTitle(); ?></a>
            <?php else : ?>
                <?php echo $session->getTitle(); ?> <em>(<?php echo get_lang( 'Unavailable' ); ?>)</em>
            <?php endif; ?>
            </td>
            <?php if( claro_is_allowed_to_edit() ) : ?>
            <td align="center">
                <img src="<?php echo get_icon_url( $session->getContext() == Session::CONTEXT_GROUP ? 'group' : 'user' ); ?>" alt="<?php echo get_lang( $session->getContext() ); ?>" />
            </td>
            <td>
                <?php echo get_lang( $session->getType() ); ?>
            </td>
            <?php endif; ?>
            <td><?php echo $session->getOpeningDate() ? claro_html_localised_date( '%a %d %b %Y' , strtotime( $session->getOpeningDate() ) ) : 'no date'; ?></td>
            <td><?php echo $session->getClosingDate() ? claro_html_localised_date( '%a %d %b %Y' , strtotime( $session->getClosingDate() ) ) : 'no date'; ?></td>
            <?php if( claro_is_allowed_to_edit() ) : ?>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqModifySession&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteSession&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
                    <?php if ( $session->isOpen() ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exCloseSession&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'unlock' ); ?>" alt="<?php echo get_lang( 'lock' ); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exOpenSession&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Unlock' ); ?>"/>
                </a>
                    <?php endif; ?>
                    <?php if ( $session->isVisible() ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exHideSession&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible' ); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exShowSession&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible' ); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
            <td align="center">
                    <?php if( $this->sessionList->getRank( $session->getId() ) != 1 ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMoveSessionUp&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'move_up' ); ?>" alt="<?php echo get_lang( 'Move up' ); ?>"/>
                </a>
                    <?php endif; ?>
                    <?php if( $this->sessionList->getRank( $session->getId() ) != $this->sessionList->getMaxRank() ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMoveSessionDown&sessionId='. $session->getId() ) );?>">
                    <img src="<?php echo get_icon_url( 'move_down' ); ?>" alt="<?php echo get_lang( 'Move down' ); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
            <?php else : ?>
            <td align="center">
                <?php if( $session->isAvailable() ) : ?>
                <img src="<?php echo get_icon_url( 'unlock' ); ?>" alt="<?php echo get_lang( 'Open' ); ?>"/>
                <?php else : ?>
                <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Closed' ); ?>"/>
                <?php endif; ?>
            </td>
            <?php endif; ?>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="<?php echo claro_is_allowed_to_edit() ? 7 : 5; ?>"  align="center"><span class="empty"><?php echo get_lang( 'Empty' ); ?></span></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>