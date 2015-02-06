<?xml version="1.0" encoding="UTF-8"?>
<quiz>
<!-- question: 0  -->
  <question type="category">
    <category>
        <text>$course$/<?php echo get_lang( 'Questions from quiz "%quizTitle"' , array( '%quizTitle' => $this->title ) ); ?></text>
    </category>
  </question>
<?php foreach( $this->questionList as $question ) : ?>
    <?php echo $question->render(); ?>
<?php endforeach; ?>
</quiz>