<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Title and description' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'date_from' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'date_to' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Status' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Join' ); ?>
            </th>
<?php if( $this->is_manager ) : ?>
            <th>
                <?php echo get_lang( 'Actions' ); ?>
            </th>
<?php endif; ?>
        </tr>
    </thead>
    <tbody>
<?php if( empty( $this->meetingList ) ) : ?>
        <tr>
            <td class="empty" colspan="5"><?php echo get_lang( 'No meeting to list' ); ?></td>
        </tr>
<?php else : ?>
    <?php foreach( $this->meetingList as $id => $data ) : ?>
        <tr>
            <td>
                <?php echo $data['title']; ?><br />
                <small><?php echo $data['description']; ?></small>
            </td>
            <td align="center">
                <?php echo $data['date_from']; ?>
            </td>
            <td align="center">
                <?php echo substr( $data['date_to'] , 12 ); ?>
            </td>
            <td align="center">
                <img src="<?php echo get_icon_url( 'on_schedule' ); ?>" alt="<?php echo get_lang( 'on schedule' ); ?>"/>
            </td>
            <td align="center">
                <img src="<?php echo get_icon_url( 'cannot_join' ); ?>" alt="<?php echo get_lang( 'on schedule' ); ?>"/>
            </td>
        <?php if( $this->is_manager ) : ?>
            <td align="center">
                <a title="<?php echo get_lang( 'Delete' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteMeeting&id=' . $data[ 'id' ] ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
                <a title="<?php echo get_lang( 'Edit' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditMeeting&meetingId='. $data[ 'id' ] ) );?>">
                    <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>"/>
                </a>
            <?php if( $data['is_visible'] ) : ?>
                <a title="<?php echo get_lang( 'Hide' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvisible&id=' . $data[ 'id' ] ) );?>">
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Hide' ); ?>"/>
                </a>
            <?php else : ?>
                <a title="<?php echo get_lang( 'Edit' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVisible&id='. $data[ 'id' ] ) );?>">
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Show' ); ?>"/>
                </a>
            <?php endif; ?>
            </td>
        <?php endif; ?>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
    </tbody>
</table>