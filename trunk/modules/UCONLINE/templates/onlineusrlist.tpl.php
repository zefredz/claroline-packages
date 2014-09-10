<h3 class="claroToolTitle"><?php echo $this->toolName; ?></h3>

<p><?php echo get_lang( 'List of active users for the last %time minutes :' , array( '%time' => $this->refreshTime ) ); ?></p>
<?php if( get_conf( 'UCONLINE_privacy' ) ) : ?>
<p>
    <strong><?php echo get_lang( 'Warning : the list below is currently restricted for security purposes' ) ?></strong>
</p>
<?php endif; ?>

<table style="text-align: center;" class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
    <thead>
        <tr class="headerX" align="center" valign="top">
        <?php if( get_conf( 'UCONLINE_showUserId' ) ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'id' ]; ?>"><?php echo get_lang( 'No.' ); ?></a>
            </th>
        <?php endif; ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'lastname' ]; ?>"><?php echo get_lang( 'Last name' ); ?></a>
            </th>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'firstname' ]; ?>"><?php echo get_lang( 'First name' ); ?></a>
            </th>
        <?php if ( get_conf( 'UCONLINE_showUserPicture' ) ) : ?>
            <th>
                <img src="<?php echo get_icon_url( 'user' ); ?>" alt="<?php echo get_lang( 'user picture' ); ?>" />
            </th>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showSendMessage' ) ) : ?>
            <th>
                <img src="<?php echo get_icon_url( 'mail_close' ); ?>" alt="<?php echo get_lang( 'Send a message' ); ?>" />
            </th>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showEmail' ) ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'email' ]; ?>"><?php echo get_lang( 'Email' ); ?></a>
            </th>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showSkypeStatus' ) ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'skype_name' ]; ?>"><?php echo get_lang( 'Skype account' ); ?></a>
            </th>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showLocalTime' ) ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'time_offset' ]; ?>"><?php echo get_lang( 'Local Time' ); ?></a>
            </th>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showStatus' ) ) : ?>
            <th>
                <a href="<?php echo $this->sortUrlList[ 'isCourseCreator' ]; ?>"><?php echo get_lang( 'Status' ); ?></a>
            </th>
        <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach( $this->userList as $user ) : ?>
        <tr>
        <?php if( get_conf( 'UCONLINE_showUserId' ) ) : ?>
            <td align="center"><?php echo $user[ 'id' ]; ?></td>
        <?php endif; ?>
            <?php if ( claro_is_platform_admin() ) : ?>
            <td><a href="<?php echo claro_htmlspecialchars( Url::Contextualize( get_path('clarolineRepositoryWeb') . '/admin/admin_profile.php?uidToEdit=' . $user[ 'id' ] ) ); ?>"><?php echo $user[ 'lastname' ]; ?></a></td>
            <?php else : ?>
            <td><?php echo $user[ 'lastname' ]; ?></td>
            <?php endif; ?>
            <td><?php echo $user[ 'firstname' ]; ?></td>
        <?php if ( get_conf( 'UCONLINE_showUserPicture' ) ) : ?>
            <td>
                <a href="#" id="user<?php echo $user[ 'id' ]; ?>" class="userBlock">
                    <span>
                        <img src="<?php echo get_icon_url( 'user' ); ?>" alt="<?php echo get_lang( 'user picture' ); ?>" />
                    </span>
                    <span class="userPicture">
                        <img src="<?php echo $user[ 'picture' ]; ?>" alt="<?php echo get_lang( 'user picture' ); ?>"/>
                    </span>
                </a>
            </td>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showSendMessage' ) ) : ?>
            <td>
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( get_path('clarolineRepositoryWeb') . '/messaging/sendmessage.php?cmd=rqMessageToUser&amp;userId=' . $user[ 'id' ] ) ); ?>">
                    <img src="<?php echo get_icon_url_url( 'mail_close' ); ?>" alt="send a message" />
                </a>
            </td>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showEmail' ) ) : ?>
            <td>
            <?php if( ! empty( $user[ 'email' ] ) ) : ?>
                <a href="mailto:<?php echo $user[ 'email' ]; ?>"><?php echo $user[ 'email' ]; ?></a>
            <?php else : ?>
                -
            <?php endif; ?>
            </td>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showSkypeStatus' ) ) : ?>
            <td>
            <?php if ( $user[ 'id' ] == claro_get_current_user_id() ) : ?>
                <a href="edit.php"><?php echo get_lang( 'Configure your Skype account' ); ?></a>
                &nbsp;-&nbsp;
            <?php endif; ?>
            <?php if( ! empty( $user[ 'skype_name' ] ) ) : ?>
                <a href="skype:<?php echo $user[ 'skype_name' ]; ?>?call">
                    <img src="http://mystatus.skype.com/smallclassic/<?php echo $user[ 'skype_name' ]; ?>"
                    style="border: none;" width="100" height="15" alt="<?php echo get_lang('user\'s Skype status'); ?>" />
                </a>
            <?php else : ?>
                <em><?php echo get_lang( 'None' ); ?></em>
            <?php endif; ?>
            </td>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showLocalTime' ) ) : ?>
            <td>
            <?php echo date( "H:i" , time() + $user[ 'time_offset' ] ); ?>
            </td>
        <?php endif; ?>
        <?php if( get_conf( 'UCONLINE_showStatus' ) ) : ?>
            <td>
            <?php if ( $user[ 'isPlatformAdmin' ] ) : ?>
                <?php echo get_lang( 'Platform administrator' ); ?>
            <?php elseif ( $user[ 'isCourseCreator' ] ) : ?>
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