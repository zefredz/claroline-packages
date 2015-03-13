<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Items' ); ?></th>
            <th><?php echo get_lang( 'Export' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo get_lang( 'Download all course\'s documents in a single zip file' ); ?></td>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( get_path( 'rootWeb' ) . 'claroline/document/document.php?cmd=exDownload&file=' ) );?>">
                    <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                </a>
            </td>
        </tr>
    </tbody>
</table>