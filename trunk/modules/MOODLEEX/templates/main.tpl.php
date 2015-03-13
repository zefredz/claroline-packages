<div id="mainContent">
    <fieldset id="documents">
        <legend><a name="exportDocuments"></a><?php echo get_lang( 'Documents and links' ); ?></legend>
        <span class="sectionDesc">
            <?php echo get_lang( '[Documents export] %warning' ); ?>
        </span>
        <?php include 'documentlist.tpl.php'; ?>
    </fieldset>
    <fieldset id="exercices">
        <legend><a name="exportExercises"></a><?php echo get_lang( 'Exercises' ); ?></legend>
        <span class="sectionDesc">
            <?php echo get_lang( '[Exercices export] %warning' , array( '%warning' => $this->warningText ) ); ?>
        </span>
        <?php include 'exerciselist.tpl.php'; ?>
    </fieldset>
<?php if( $this->podcastActivated ) : ?>
    <fieldset id="videos">
        <legend><a name="exportVideos"></a><?php echo get_lang( 'Videos' ); ?></legend>
        <span class="sectionDesc">
            <?php echo get_lang( '[Videos export] %warning' , array( '%warning' => $this->warningText ) ); ?>
        </span>
        <?php include 'videolist.tpl.php'; ?>
    </fieldset>
<?php endif; ?>
</div>