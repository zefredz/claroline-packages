<div id="mainContent">
    <fieldset id="documents">
        <legend><?php echo get_lang( 'Documents and links' ); ?></legend>
        <span class="sectionDesc">
            <?php echo get_lang( 'Here, you can export all documents stored in your course into a single zip file.' ); ?>
        </span>
        <?php include 'documentlist.tpl.php'; ?>
    </fieldset>
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
</div>
<div class="addInfos">
    <?php echo get_lang( 'Additionnal info' ); ?>
</div>
<div class="addInfos">
    <?php echo get_lang( 'Additionnal info 2' ); ?>
</div>