<!--
    $Id$
    
    Podcast list template
    * @version     ICPCRDR 1.0 $Revision$ - Claroline 1.9
    * @copyright   2001-2011 Universite catholique de Louvain (UCL)
    * @author      Frederic Minne <zefredz@claroline.net>
    * @license     http://www.gnu.org/copyleft/gpl.html
    *              GNU GENERAL PUBLIC LICENSE version 2 or later
    * @package     ICPCRDR
-->
<?php if ( claro_is_allowed_to_edit() ) : ?>

<p>
    <a class="claroCmd" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddPodcast' ) ); ?>">
    <img src="<?php echo get_icon_url('feed_add'); ?>" alt="" />
    <?php echo get_lang( 'Add a podcast' ); ?>
    </a>
</p>

<?php endif; ?>

<?php if ( count($this->podcasts) ) : ?>

    <ul>
        
    <?php foreach ($this->podcasts as $currentPodcast): ?>
        
        <?php if( $currentPodcast['visibility'] == 'visible' || claro_is_allowed_to_edit() ) :?>
        
        <li>
            
            <?php if ( $currentPodcast['visibility'] == 'visible'): ?>
            
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=visit&podcastId='.(int)$currentPodcast['id'] ) );?>">
                <?php echo claro_htmlspecialchars($currentPodcast['title']); ?>
                </a>
            
            <?php else: ?>
            
                <a class="invisible" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=visit&podcastId='.(int)$currentPodcast['id'] ) );?>">
                <?php echo claro_htmlspecialchars($currentPodcast['title']); ?>
                </a>
            
            <?php endif;?>
            
            <?php if (claro_is_allowed_to_edit()): ?>
            
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditPodcast&podcastId='.(int)$currentPodcast['id'] ) ); ?>">
                <img src="<?php echo get_icon_url('feed_edit'); ?>" alt="<?php echo get_lang( 'Edit feed' ); ?>" />'
                </a>
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeletePodcast&podcastId='.(int)$currentPodcast['id'] ) ); ?>">
                <img src="<?php echo get_icon_url('feed_delete'); ?>" alt="<?php echo get_lang( 'Delete feed' ); ?>" />'
                </a>
                
                <?php if ($currentPodcast['visibility'] == 'visible'): ?>
            
                    <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvisible&podcastId='.(int)$currentPodcast['id'] ) ); ?>">
                    <img src="<?php echo get_icon_url('visible'); ?>" alt="" />'
                    </a>
            
                <?php else: ?>
            
                    <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVisible&podcastId='.(int)$currentPodcast['id'] ) ); ?>">
                    <img src="<?php echo get_icon_url('invisible'); ?>" alt="" />'
                    </a>
            
                <?php endif; ?>
                
            <?php endif; ?>
            
        </li>
        
        <?php endif; ?>
        
    <?php endforeach; ?>
        
    </ul>

    <?php if ( claro_is_allowed_to_edit() && count($this->podcasts) > 10 ): ?>

    <p>
        <a class="claroCmd" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddPodcast' ) ); ?>">
        <img src="<?php echo get_icon_url('feed_add'); ?>" alt="" />
        <?php echo get_lang( 'Add a podcast'); ?>
        </a>
    </p>
    
    <?php endif; ?>
    
<?php else : ?>
    
<p>
    <em><?php echo get_lang( 'No podcast for now...' ); ?></em>
</p>

<?php endif; ?>