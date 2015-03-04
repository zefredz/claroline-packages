<div id="mainContent">
    <fieldset id="exercices">
        <legend><?php echo get_lang( 'Exercises' ); ?></legend>
        
        <?php include 'exerciselist.tpl.php'; ?>
    </fieldset>
<?php if( $this->podcastActivated ) : ?>
    <fieldset id="videos">
        <legend><?php echo get_lang( 'Videos' ); ?></legend>
        <?php include 'videolist.tpl.php'; ?>
    </fieldset>
<?php endif; ?>
</div>