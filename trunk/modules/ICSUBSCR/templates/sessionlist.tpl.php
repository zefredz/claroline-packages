<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Session' ); ?></th>
            <th><?php echo get_lang( 'Start date' ); ?></th>
            <th><?php echo get_lang( 'End date' ); ?></th>
            <th><?php echo get_lang( 'Actions' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if( empty( $this->sessionList ) ) : ?>
        <tr>
            <td colspan="4"  align="center"><span class="empty"><?php echo get_lang( 'Empty' ); ?></span></td>
        </tr>
    <?php else : ?>
        <?php foreach( $this->sessionList as $session ) : ?>
        <tr>
            <td><?php echo $session['title']; ?></td>
            <td><?php echo $session['startDate']; ?></td>
            <td><?php echo $session['endDate']; ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>