<?php if( $this->question->moodleType == 'gapselect' ) : ?>
<shownumcorrect/>
<?php foreach( $this->question->answerList as $answer ) : ?>
<selectoption>
    <text><?php echo $answer[ 'option' ]; ?></text>
    <group>1</group>
</selectoption>
<?php endforeach; ?>
<?php endif; ?>
