<h3 class="channelTitle"><?php echo htmlspecialchars(claro_utf8_decode($this->channel['title']));?></h3>
<p><a href="<?php echo htmlspecialchars($this->url);?>"><img src="<?php echo get_icon_url('feed'); ?>" /> <?php echo get_lang("S'abonner");?></a></p>
<p class="channelPubDate"><?php echo htmlspecialchars($this->channel['pubDate']);?></p>
<p class="channelDescription"><?php echo claro_utf8_decode(strip_tags( $this->channel['description'] )); ?></p>
<?php
    $videoId = 1;
    
    foreach( $this->items as $item ):
?>
<h4 class="itemTitle"><?php echo htmlspecialchars( claro_utf8_decode($item->metadata['title']) ); ?></h4>
<p class="itemPubDate"><?php echo htmlspecialchars( $item->metadata['pubDate'] ); ?></p>
<p class="itemDescription"><?php echo claro_utf8_decode(strip_tags($item->metadata['description'])); ?></p>
<a  
    href="<?php echo htmlspecialchars($item->enclosure['url']); ?>"  
    style="display:block;width:400px;height:300px"  
    id="player<?php echo "_{$videoId}"?>"> 
</a>
<script type="text/javascript">
    flowplayer( "player<?php echo "_{$videoId}"?>", "./flash/flowplayer-3.1.1.swf", { 
        clip: { 
            // these two configuration variables does the trick 
            autoPlay: false,  
            autoBuffering: true // <- do not place a comma here   
        } 
    } );
</script>
<?php
    $videoId++;
    
    endforeach;
?>