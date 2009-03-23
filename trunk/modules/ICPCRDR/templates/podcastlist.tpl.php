<p>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddPodcast' ) ); ?>">
    <img src="<?php echo get_icon_url('feed_add'); ?>" alt="" /> '
    <?php echo get_lang( 'Add a podcast'); ?>
    </a>
</p>

<?php if ( count($this->podcasts) ): ?>
    <ul>
    <?php foreach ($this->podcasts as $currentPodcast): ?>
        <?php if( $currentPodcast['visibility'] == 'visible' || claro_is_allowed_to_edit() ) :?>
        <li>
            <?php if ( $currentPodcast['visibility'] == 'visible'): ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=visit&podcastId='.(int)$currentPodcast['id'] ) );?>">
                <?php echo htmlspecialchars($currentPodcast['title']); ?>
                </a>
            <?php else: ?>
                <a class="invisible" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=visit&podcastId='.(int)$currentPodcast['id'] ) );?>">
                <?php echo htmlspecialchars($currentPodcast['title']); ?>
                </a>
            <?php endif;?>
            
            <?php if (claro_is_allowed_to_edit()): ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditPodcast&podcastId='.(int)$currentPodcast['id'] ) ); ?>">
                <img src="<?php echo get_icon_url('feed_edit'); ?>" alt="" />'
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeletePodcast&podcastId='.(int)$currentPodcast['id'] ) ); ?>">
                <img src="<?php echo get_icon_url('feed_delete'); ?>" alt="" />'
                </a>
                
                <?php if ($currentPodcast['visibility'] == 'visible'): ?>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvisible&podcastId='.(int)$currentPodcast['id'] ) ); ?>">
                    <img src="<?php echo get_icon_url('visible'); ?>" alt="" />'
                    </a>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVisible&podcastId='.(int)$currentPodcast['id'] ) ); ?>">
                    <img src="<?php echo get_icon_url('invisible'); ?>" alt="" />'
                    </a>
                <?php endif; ?>
                
            <?php endif; ?>
            
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
    <p>
        <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddPodcast' ) ); ?>">
        <img src="<?php echo get_icon_url('feed_add'); ?>" alt="" /> '
        <?php echo get_lang( 'Add a podcast'); ?>
        </a>
    </p>
<?php endif; ?>