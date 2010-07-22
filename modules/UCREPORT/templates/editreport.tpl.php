<?php if ( claro_is_allowed_to_edit() ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowReport') ); ?>">
        <img src="<?php echo get_icon_url( 'statistics' ); ?>" alt="current results" />
        <?php echo get_lang( 'Generate the preview' ); ?>
    </a>
</span>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exEditReport' ) ); ?>" >
    <table class="claroTable emphaseLine" style="width: 100%;">
        <thead>
            <tr class="headerX">
                <th><?php echo get_lang( 'Assignment' ); ?></th>
                <th><?php echo get_lang( 'Activated' ); ?></th>
                <th><?php echo get_lang( 'Weight' ); ?></th>
                <th><?php echo get_lang( 'Proportional weight' ); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach( $this->assignmentDataList as $assignmentId => $assignment ) : ?>
            <tr>
                <td>
                    <?php echo $assignment[ 'title' ]; ?>
                </td>
                <td>
                    <input type="checkbox" name="active[<?php echo $assignmentId; ?>]" <?php if ( $assignment[ 'active' ] ) echo 'checked="checked"'; ?> />
                </td>
                <td>
                    <input type="text" size="2" name="weight[<?php echo $assignmentId; ?>]" value="<?php echo $assignment[ 'weight' ]; ?>" />
                </td>
                <td>
                    <?php echo 100 * $assignment[ 'proportional_weight' ]; ?> %
                </td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <input id="submit" type="submit" name="submitReport" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
</form>
<?php endif; ?>