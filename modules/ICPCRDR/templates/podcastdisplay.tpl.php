<!--
    $Id$
    
    Podcast display template
    * @version     ICPCRDR 1.0 $Revision$ - Claroline 1.9
    * @copyright   2001-2011 Universite catholique de Louvain (UCL)
    * @author      Frederic Minne <zefredz@claroline.net>
    * @license     http://www.gnu.org/copyleft/gpl.html
    *              GNU GENERAL PUBLIC LICENSE version 2 or later
    * @package     ICPCRDR
-->
<?php if ( claro_is_allowed_to_edit() ) { include_textzone('icpcrdr_display_top.html',''); } ?>
<h3 class="channelTitle">
    <?php echo claro_htmlspecialchars(claro_utf8_decode($this->channel['title']));?>
</h3>

<p>
    <a href="<?php echo claro_htmlspecialchars($this->url);?>">
        <img src="<?php echo get_icon_url('feed'); ?>" alt="rss" /> 
        <?php echo get_lang('Suscribe to podcast');?>
    </a>
</p>

<p>
    <?php if( $this->rsort ) : ?>
    <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] . '?cmd=visit&podcastId=' . $this->id ) );?>">
        <img src="<?php echo get_icon_url('go_up'); ?>" alt="up" /> 
        <?php echo get_lang('Sort items in rss order (the newest ones first)');?>
    </a>
    <?php else : ?>
    <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER[ 'PHP_SELF' ] . '?cmd=visit&sort=chrono&podcastId=' . $this->id ) );?>">
        <img src="<?php echo get_icon_url('go_down'); ?>" alt="down" /> 
        <?php echo get_lang('Sort items in chronological order (the oldest ones first)');?>
    </a>
    <?php endif; ?>
</p>

<p class="channelPubDate">
        <?php echo claro_htmlspecialchars($this->channel['pubDate']);?>
</p>

<p class="channelDescription">
        <?php echo claro_utf8_decode(strip_tags( $this->channel['description'] )); ?>
</p>

<!-- display the podcast list -->
<?php
    $videoId = 1;
    
    foreach( $this->items as $item ):
 ?>

        <h4 class="itemTitle">
            <?php echo claro_htmlspecialchars( claro_utf8_decode($item->metadata['title']) ); ?>
        </h4>

        <p class="itemPubDate">
            <?php echo claro_htmlspecialchars( $item->metadata['pubDate'] ); ?>
        </p>
        
        <p class="itemDescription">
            <?php echo claro_utf8_decode(strip_tags($item->metadata['description'])); ?>
        </p>
        
        <?php if (strncmp($item->enclosure['type'], 'audio/', 6) == 0): ?> <!-- audio -->
        
        <audio 
            type="<?php echo $item->enclosure['type']; ?>" 
            src="<?php echo claro_htmlspecialchars( str_replace( "'", rawurlencode("%27"), $item->enclosure['url'] ) ); ?>"
            controls="controls"
            id="player<?php echo "_{$videoId}"?>">
        </audio>
        
        <?php else: ?> <!-- video -->
        
        <video 
            type="<?php echo $item->enclosure['type']; ?>" 
            src="<?php echo claro_htmlspecialchars( str_replace( "'", rawurlencode("%27"), $item->enclosure['url'] ) ); ?>" 
            controls="controls"
            id="player<?php echo "_{$videoId}"?>">
        </video>
        
        <?php endif; ?>
        
        <script>
        $('video,audio').mediaelementplayer(/* Options */);
        </script>
        
        <?php if ( $this->downloadLink == 'visible' ): ?>
        <p>
            <!-- em>
                <?php echo get_lang('If the media doesn\'t play correctly, you can download it using the following link (right-click Save Link As or ctrl+click Save Link As)' );?>
            </em>
            <br / -->
            <em>
                <a href="<?php echo claro_htmlspecialchars( $item->enclosure['url'] );?>">
                    <img src="<?php echo get_icon_url('download'); ?>" alt="download" />
                    <?php echo get_lang( 'Download this video' ); ?>
                </a>
            </em>
        </p>
        <?php endif; ?>
        
<?php
        $videoId++;
    
    endforeach;
?>
<!-- end of podcast list -->
