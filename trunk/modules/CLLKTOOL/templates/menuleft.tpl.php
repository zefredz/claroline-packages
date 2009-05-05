<a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddLink' ) ); ?>"><img src="./img/link_add.png" alt="" /><?php echo get_lang( 'Create a new link'); ?></a>
<ul>
<?php
foreach( $this->links as $link ) :
    if( $link['visibility'] == 'visible' || $this->is_allowed_to_edit ) :
    ?>
    <li>
        <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=visit&linkId='.(int)$link['id'] ) ); ?>" <?php if($link['visibility'] != 'visible') : ?>class="invisible"<?php endif;?> ><?php echo htmlspecialchars($link['title']); ?></a>
        <?php
        if( $this->is_allowed_to_edit ) :
        ?>
        <!-- Edit link -->
        <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditLink&linkId='.(int)$link['id'] ) ); ?>"><img src="./img/link_edit.png" alt="<?php echo get_lang('Modify'); ?>" /></a>
        <!-- Delete link -->
        <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteLink&linkId='.(int)$link['id'] ) ); ?>"><img src="./img/link_delete.png" alt="<?php echo get_lang('Delete'); ?>" /></a>
        <!-- Visibility -->
        <?php
        if( $link['visibility'] == 'visible' ) :
        ?>
        <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvis&linkId='.(int)$link['id'] ) ); ?>"><img src="<?php echo get_icon_url('visible'); ?>" alt="<?php echo get_lang('Visible'); ?>" title="<?php echo get_lang('Make invisible'); ?>" /></a>
        <?php
        else :
        ?>
        <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVis&linkId='.(int)$link['id'] ) ); ?>"><img src="<?php echo get_icon_url('invisible'); ?>" alt="<?php echo get_lang('Invisible'); ?>" title="<?php echo get_lang('Make visible'); ?>" /></a>
        <?php
        endif;
        ?>
        <?php
        endif;
        ?>
    </li>
    <?php
    endif;
endforeach;
?>
</ul>
<?php
if( count( $this->links ) ) :
?>
<a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddLink' ) ); ?>"><img src="./img/link_add.png" alt="" /><?php echo get_lang( 'Create a new link'); ?></a>
<?php
endif;
?>