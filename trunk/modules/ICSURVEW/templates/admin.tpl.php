<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqImport' ) ); ?>">
        <img src="<?php echo get_icon_url( 'import' ); ?>" alt="<?php echo get_lang( 'import'); ?>"/>
        <?php echo get_lang( 'Import a survey' ); ?>
    </a>
</span>

<table class="claroTable emphaseLine" style="width: 100%;">
    <thead>
        <tr class="headerX">
            <th align="center"><?php echo get_lang( 'Title' ); ?></th>
            <th align="center"><?php echo get_lang( 'Activate' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach( $this->surveyList as $survey ) : ?>
        <tr>
            <td><?php echo $survey['title']; ?></td>
        <?php if( $survey['id'] == $this->activeId ) : ?>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exDeactivate&surveyId='. $survey['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'on' ); ?>"
                         alt="<?php echo get_lang( 'enabled' ); ?>" />
                </a>
            </td>
        <?php else : ?>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exActivate&surveyId='. $survey['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'off' ); ?>"
                         alt="<?php echo get_lang( 'disabled' ); ?>" />
                </a>
            </td>
        <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>