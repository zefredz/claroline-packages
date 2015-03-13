<div id="mainContent">
    <fieldset id="exercices">
        <legend><?php echo get_lang( 'Exercises' ); ?></legend>
        <span class="sectionDesc">
            <?php echo get_lang( 'Here, you can export you exercises to MoodleXML format in order to import them in a Moodle platform.' ); ?>
        </span>
        <?php include 'exerciselist.tpl.php'; ?>
    </fieldset>
<?php if( $this->podcastActivated ) : ?>
    <fieldset id="videos">
        <legend><?php echo get_lang( 'Videos' ); ?></legend>
        <span class="sectionDesc">
            <?php echo get_lang( 'Here, you can obtain for each video podcast a textfile with direct links of each video they contain, so you can easily copy-paste them into Moodle.' ); ?>
        </span>
        <?php include 'videolist.tpl.php'; ?>
    </fieldset>
<?php endif; ?>
    <fieldset id="documents">
        <legend><?php echo get_lang( 'Documents and links' ); ?></legend>
        <span class="sectionDesc">
            <?php echo get_lang( 'Obvious' ); ?>
        </span>
        <?php include 'documentlist.tpl.php'; ?>
    </fieldset>
</div>