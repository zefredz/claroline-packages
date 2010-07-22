<?php if ( claro_is_allowed_to_edit() ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditReport') ); ?>">
        <img src="<?php echo get_icon_url( 'export_list' ); ?>" alt="current results" />
        <?php echo get_lang( 'Create a new report' ); ?>
    </a>
</span>
<?php endif; ?>
<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Report'); ?>
            </th>
            <th>
                <?php echo get_lang( 'Publication date' ); ?>
            </th>
<?php if ( claro_is_allowed_to_edit() ) : ?>
            <th>
                <?php echo get_lang( 'Delete' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Visibility' ); ?>
            </th>
<?php endif; ?>
        </tr>
    </thead>
    <tbody>
<?php if ( $this->reportList->numRows() ) : ?>
    <?php foreach ( $this->reportList as $report ) : ?>
        <?php if ( $report['visibility'] == Report::VISIBLE || claro_is_allowed_to_edit() ) : ?>
        <tr>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowReport&reportId='. $report['id'] ) );?>">
                    <?php echo $report[ 'title' ]; ?>
                </a>
            </td>
            <td>
                    <?php echo $report[ 'publication_date' ]; ?>
            </td>
                <?php if ( claro_is_allowed_to_edit() ) : ?>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteReport&reportId='. $report['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete'); ?>"/>
                </a>
            </td>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exChangeVisibility&reportId='. $report['id'] . '&visibility=' . $report['visibility'] ) );?>">
                    <?php if ( $report['visibility'] == Report::VISIBLE ) : ?>
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
                    <?php else: ?>
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible'); ?>"/>
                    <?php endif; ?>
                </a>
            </td>
                <?php endif; ?>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
        <tr>
            <td class="empty" colspan="<?php echo claro_is_allowed_to_edit() ? 6 : 2; ?>"><?php echo get_lang( 'No report available' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>