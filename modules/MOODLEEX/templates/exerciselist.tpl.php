<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Exercises' ); ?></th>
            <th><?php echo get_lang( 'Export' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $this->itemList[ 'quiz' ] as $quiz ) : ?>
        <tr>
            <td><?php echo $quiz[ 'title' ]; ?></td>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exportQuiz&quizId='. $quiz[ 'id' ] ) );?>">
                    <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>