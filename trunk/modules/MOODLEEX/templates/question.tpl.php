<question type="<?php echo $this->question->moodleType; ?>">
    <name>
        <text><?php echo MOODLEEX_bake( $this->question->title ); ?></text>
    </name>
    <questiontext <?php if( MOODLEEX_is_html( $this->question->description ) ) : ?>format="html"<?php endif; ?>>
        <text><?php echo MOODLEEX_bake( $this->question->description ); ?></text>
    </questiontext>
    <generalfeedback>
        <text></text>
    </generalfeedback>
    <defaultgrade><?php echo $this->question->grade; ?></defaultgrade>
    <penalty><?php echo $this->question->penalty; ?></penalty>
    <?php foreach( $this->question->optionList as $option => $value ) : ?>
    <?php echo '<' . $option . '>' . $value . '</' . $option . '>' . "\n"; ?>
    <?php endforeach; ?>
    <?php include( 'moodletype/' . $this->question->moodleType . '.tpl.php' ); ?>
</question>
