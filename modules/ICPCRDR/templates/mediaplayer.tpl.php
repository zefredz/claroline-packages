<?php $videoId = md5($this->src); ?>

<?php if (strncmp($this->type, 'audio/', 6) == 0): ?> <!-- audio -->
        
<audio 
    type="<?php echo $this->type; ?>" 
    src="<?php echo claro_htmlspecialchars( str_replace( "'", rawurlencode("%27"), $this->src ) ); ?>"
    controls="controls"
    id="player<?php echo "_{$videoId}"?>">
</audio>

<?php else: ?> <!-- video -->

<video 
    type="<?php echo $this->type; ?>" 
    src="<?php echo claro_htmlspecialchars( str_replace( "'", rawurlencode("%27"), $this->src ) ); ?>" 
    controls="controls"
    id="player<?php echo "_{$videoId}"?>">
</video>

<?php endif; ?>

<script>
    $('#player<?php echo "_{$videoId}"?>').mediaelementplayer(/* Options */);
</script>

<?php if ( $this->displayDowloadLink ): ?>
<p>
    <?php echo get_lang('If the media doesn\'t play correctly, you can download it using the following link (right-click Save Link As or ctrl+click Save Link As)' );?>
    <br />
    <em>
        <a href="<?php echo claro_htmlspecialchars( $this->src );?>">
            <img src="<?php echo get_icon_url('download'); ?>" alt="download" />
            <?php echo get_lang( 'Download this video' ); ?>
        </a>
    </em>
</p>
<?php endif; ?>