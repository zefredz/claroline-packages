<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Podcasts' ); ?></th>
            <th><?php echo get_lang( 'Export' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $this->podcastList as $podcast ) : ?>
        <tr>
            <td><?php echo $podcast[ 'title' ]; ?></td>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exportPod&podcastId='. $podcast[ 'id' ] ) );?>">
                    <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>