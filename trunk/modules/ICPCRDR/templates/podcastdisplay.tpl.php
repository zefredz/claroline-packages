<h3 class="channelTitle"><?php echo htmlspecialchars($this->channel['title']);?></h3>
<p><a href="<?php echo htmlspecialchars($this->url);?>"><img src="<?php echo get_icon_url('feed'); ?>" /> <?php echo get_lang("S'abonner");?></a></p>
<p><?php echo htmlspecialchars($this->channel['pubDate']);?></p>
<p class="channelDescription"><?php echo strip_tags( $this->channel['description'] ); ?></p>
<?php
    $videoId = 1;
    
    foreach( $this->items as $item ):
?>
<h4 class="itemTitle"><?php echo htmlspecialchars( $item->metadata['title'] ); ?></h4>
<p class="itemDescription"><?php echo strip_tags($item->metadata['description']); ?></p>
<a  
    href="<?php echo htmlspecialchars($item->enclosure['url']); ?>"  
    style="display:block;width:400px;height:300px"  
    id="player<?php echo "_{$videoId}"?>"> 
</a>
<script type="text/javascript">
    flowplayer("player<?php echo "_{$videoId}"?>", "./flash/flowplayer-3.0.7.swf");
</script>
<?php
    $videoId++;
    
    endforeach;
?>