<?php // $Id$
/**
 * Examination report
 *
 * @version     UCEXAM 0.3.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCEXAM
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ); ?>">
        <img src="<?php echo get_icon_url( 'go_left' ); ?>" alt="back" />
        <?php echo get_lang( 'Back to the examination list' ); ?>
    </a>
</span>
<?php if ( claro_is_allowed_to_edit() ) : ?>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exModifyMark&sessionId=' . $this->examination->getSessionId() ) ); ?>" >
    <table class="claroTable emphaseLine" style="width: 100%;">
        <thead>
            <tr class="headerX">
                <th><?php echo get_lang( 'User'); ?></th>
                <th><?php echo get_lang( 'Mark' ); ?></th>
                <th><?php echo get_lang( 'Comment' ); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach( $this->examination->getScoreList() as $userId => $mark ) : ?>
            <tr>
                <td>
                    <?php echo $mark[ 'lastName' ] . ' ' . $mark[ 'firstName' ]; ?>
                </td>
                <td>
                    <input type="text" size="2" name="mark[<?php echo $userId; ?>]" value="<?php echo $mark[ 'score' ]; ?>" />
                    &nbsp;/&nbsp;
                    <?php echo $this->examination->getMaxScore(); ?>
                </td>
                <td>
                    <input type="text" size="80" name="comment[<?php echo $userId; ?>]" value="<?php echo $mark[ 'comment' ]; ?>" />
                </td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <input id="submit" type="submit" name="submitReport" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
</form>
<?php else : ?>
    <?php if ( isset( $this->markList[ $this->currentUserId ] ) ) : ?>
<p>
    <?php if ( $this->markList[ $this->currentUserId ][ 'mark' ] || $this->markList[ $this->currentUserId ][ 'comment' ] ) : ?>
        <?php echo $this->markList[ $this->currentUserId ][ 'mark' ]
        . ' / '
        . $this->examination->getMaxValue()
        . ' : '
        . $this->markList[ $this->currentUserId ][ 'comment' ]; ?>
    <?php else : ?>
        <?php echo get_lang( 'You have no mark yet for this session' ); ?>
    <?php endif; ?>
</p>
    <?php else : ?>
<p><?php echo get_lang( 'You are not a course member' ); ?><p>
    <?php endif; ?>
<?php endif; ?>