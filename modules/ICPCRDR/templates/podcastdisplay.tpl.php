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
<h3 class="channelTitle">
    <?php echo htmlspecialchars(claro_utf8_decode($this->channel['title']));?>
</h3>

<p>
    <a href="<?php echo htmlspecialchars($this->url);?>">
        <img src="<?php echo get_icon_url('feed'); ?>" alt="rss" /> 
        <?php echo get_lang('Suscribe');?></a>
</p>

<p class="channelPubDate">
        <?php echo htmlspecialchars($this->channel['pubDate']);?>
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
            <?php echo htmlspecialchars( claro_utf8_decode($item->metadata['title']) ); ?>
        </h4>
        
        <a href="<?php echo htmlspecialchars( $item->enclosure['url'] );?>">
            <img src="<?php echo get_icon_url('download'); ?>" alt="download" />
            <?php echo get_lang( 'Download this video' ); ?>
        </a>

        <p class="itemPubDate">
            <?php echo htmlspecialchars( $item->metadata['pubDate'] ); ?>
        </p>
        
        <p class="itemDescription">
            <?php echo claro_utf8_decode(strip_tags($item->metadata['description'])); ?>
        </p>
        
        <a
            href="<?php echo htmlspecialchars($item->enclosure['url']); ?>"
            style="display:block;width:400px;height:300px"
            id="player<?php echo "_{$videoId}"?>">
        </a>
        
        <script type="text/javascript">
            flowplayer( "player<?php echo "_{$videoId}"?>", "./flash/flowplayer-3.2.5.swf", {
                clip: {
                    autoPlay: <?php echo get_conf( 'flowplayer_autoPlay', false ) ? 'true' : 'false'; ?>,
                    autoBuffering: <?php echo get_conf( 'flowplayer_autoBuffering', false ) ? 'true' : 'false'; ?>
                } 
            } );
        </script>
        
<?php
        $videoId++;
    
    endforeach;
?>
<!-- end of podcast list -->
