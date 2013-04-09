<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Name' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'email' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Submission date' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Abstract' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Actions' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
<?php if ( ! empty( $this->ticketList ) ) : ?>
    <?php foreach ( $this->ticketList as $ticketId => $ticket ) : ?>
        <tr>
            <td>
                <?php echo $ticket[ 'userName' ]; ?>
            </td>
            <td>
                <a href="mailto:<?php echo $ticket[ 'mail' ]; ?>" ><?php echo $ticket[ 'mail' ]; ?></a>
            </td>
            <td>
                <?php echo $ticket[ 'submissionDate' ]; ?>
            </td>
            <td >
                <?php echo $ticket[ 'shortDescription' ]; ?>
            </td>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=readDescription&ticketId='. $ticketId ) );?>">
                    <img src="<?php echo get_icon_url( 'lookat' ); ?>" alt="<?php echo get_lang( 'Read issue description' ); ?>" />
                </a>
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=resendMail&ticketId='. $ticketId ) );?>">
                    <img src="<?php echo get_icon_url( 'sendmail' ); ?>" alt="<?php echo get_lang( 'Resend mail' ); ?>" />
                </a>
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=closeTicket&ticketId='. $ticketId ) );?>">
                    <img src="<?php echo get_icon_url( 'tick' ); ?>" alt="<?php echo get_lang( 'Close ticket' ); ?>" />
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
        <tr>
            <td style="color: #888888; text-align: center; font-style: italic;"colspan="5"><?php echo get_lang( 'No pending tickets' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>