<h3 class="claroToolTitle"><?php echo $this->toolName; ?></h3>

<p><?php echo get_lang( 'List of active users for the last %time minutes :' , array( '%time' => $this->refreshTime ) ); ?></p>

<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
    <thead>
        <tr class="headerX" align="center" valign="top">
        <?php if( get_conf('showUserId') ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'user_id' ]; ?>"><?php echo get_lang( 'No.' ); ?></a>
            </th>
        <?php endif; ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'lastname' ]; ?>"><?php echo get_lang( 'Last name' ); ?></a>
            </th>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'firstname' ]; ?>"><?php echo get_lang( 'First name' ); ?></a>
            </th>
        <?php if( get_conf( 'showEmail' ) ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'email' ]; ?>"><?php echo get_lang( 'Email' ); ?></a>
            </th>
        <?php endif; ?>
        <?php if( get_conf( 'showSkypeStatus' ) ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'skype_name' ]; ?>"><?php echo get_lang( 'Skype account' ); ?></a>
            </th>
        <?php endif; ?>
        <?php if( get_conf( 'showLocalTime' ) ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'time_offset' ]; ?>"><?php echo get_lang( 'Local Time' ); ?></a>
            </th>
        <?php endif; ?>
        <?php if( get_conf( 'showStatus' ) ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'isCourseCreator' ]; ?>"><?php echo get_lang( 'Status' ); ?></a>
            </th>
        <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach( $this->userList as $user ) : ?>
        <tr>
        <?php if( get_conf( 'showUserId' ) ) : ?>
            <td align="center"><?php echo $user[ 'user_id' ]; ?></td>
        <?php endif; ?>
            <td><?php echo $user[ 'lastname' ]; ?></td>
            <td><?php echo $user[ 'firstname' ]; ?></td>
        <?php if( get_conf( 'showEmail' ) ) : ?>
            <td>
            <?php if( ! empty( $user[ 'email' ] ) ) : ?>
                <a href="mailto:<?php echo $user[ 'email' ]; ?>"><?php echo $user[ 'email' ]; ?></a>
            <?php else : ?>
                -
            <?php endif; ?>
            </td>
        <?php endif; ?>
        <?php if( get_conf( 'showSkypeStatus' ) ) : ?>
            <td>
            <?php if( ! empty( $user[ 'skype_name' ] ) ) : ?>
                <a href="skype:<?php echo $user[ 'skype_name' ]; ?>?call">
                    <img src="http://mystatus.skype.com/smallclassic/<?php echo $user[ 'skype_name' ]; ?>"
                    style="border: none;" width="100" height="15" alt="<?php echo get_lang('user\'s Skype status'); ?>" />
                </a>
            <?php else : ?>
                <em><?php echo get_lang( 'None' ); ?></em>
            <?php endif; ?>
            <?php if ( $user[ 'user_id' ] == claro_get_current_user_id() ) : ?>
                &nbsp;-&nbsp;
                <a href="edit.php"><?php echo get_lang( 'Configure your Skype account' ); ?></a>
            <?php endif; ?>
            </td>
        <?php endif; ?>
        <?php if( get_conf( 'showLocalTime' ) ) : ?>
            <td>
            <?php echo date( "H:i" , time() + $user[ 'time_offset' ] ); ?>
            </td>
        <?php endif; ?>
        <?php if( get_conf( 'showStatus' ) ) : ?>
            <td>
            <?php if ( $user[ 'isCourseCreator' ] ) : ?>
                <?php echo get_lang( 'Course creator' ); ?>
            <?php else: ?>
                <?php echo get_lang( 'User' ); ?>
            <?php endif; ?>
            </td>
        <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>