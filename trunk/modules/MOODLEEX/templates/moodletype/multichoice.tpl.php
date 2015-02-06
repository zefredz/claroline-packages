<?php foreach( $this->question->answerList as $answer ) : ?>
<answer fraction="<?php echo $answer[ 'fraction' ]; ?>">
    <text><?php echo MOODLEEX_clear( $answer[ 'content' ] ); ?></text>
    <feedback>
        <text><?php echo MOODLEEX_clear( $answer[ 'feedback' ] ); ?></text>
    </feedback>
</answer>
<?php endforeach; ?>
