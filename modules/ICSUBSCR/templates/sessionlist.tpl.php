<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Session' ); ?></th>
            <th><?php echo get_lang( 'Start date' ); ?></th>
            <th><?php echo get_lang( 'End date' ); ?></th>
        <?php if( claro_is_allowed_tool_edit() ) : ?>
            <th><?php echo get_lang( 'Actions' ); ?></th>
        <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php if( $this->model->notEmpty() ) : ?>
        <?php foreach( $this->model->getItemList() as $session ) : ?>
        <tr>
            <td><a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?sessionId='. $session['id'] . '&sessionType=' . $session['type'] ) );?>"><?php echo $session['title']; ?></a></td>
            <td><?php echo $session['startDate']; ?></td>
            <td><?php echo $session['endDate']; ?></td>
            <?php if( claro_is_allowed_to_edit() ) : ?>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditSession&sessionId='. $session['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit'); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteSession&sessionId='. $session['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete'); ?>"/>
                </a>
                    <?php if ( $this->model->isVisible( $session['id'] ) ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exShow&sessionId='. $session['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exHide&sessionId='. $session['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible'); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="4"  align="center"><span class="empty"><?php echo get_lang( 'Empty' ); ?></span></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>