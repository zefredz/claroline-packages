<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 0.9.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>
 <?php if ( claro_is_allowed_to_edit() ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowReport') ); ?>">
        <img src="<?php echo get_icon_url( 'statistics' ); ?>" alt="preview" />
        <?php echo get_lang( 'Generate the preview' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditReport') ); ?>">
        <img src="<?php echo get_icon_url( 'settings' ); ?>" alt="edit" />
        <?php echo get_lang( 'Report settings' ); ?>
    </a>
</span>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exEditScores' ) ); ?>" >
    <table class="claroTable emphaseLine" style="width: 100%;">
        <thead>
            <tr class="headerX">
                <th><?php echo get_lang( 'User'); ?></th>
                <th><?php echo get_lang( 'Score' ); ?></th>
                <th><?php echo get_lang( 'Comment' ); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach( $this->reportDataList as $userId => $scoreList ) : ?>
            <tr>
                <td>
                    <?php echo $this->userList[ $userId ][ 'firstname' ] . ' ' . $this->userList[ $userId ][ 'lastname' ]; ?>
                </td>
                <td>
                    <input type="text" size="2" name="score[<?php echo $userId; ?>]" value="<?php echo isset( $scoreList[ Report::EXAMINATION_ID ] ) ? $scoreList[ Report::EXAMINATION_ID ] : ''; ?>" />
                </td>
                <?php if ( isset( $scoreList[ Report::EXAMINATION_ID ] ) ) : ?>
                <td>
                    <input type="text" size="80" name="comment[<?php echo $userId; ?>]" value="<?php echo isset( $this->userList[ $userId ][ 'comment' ] ) ? $this->userList[ $userId ][ 'comment' ] : ''; ?>" />
                </td>
                <?php else : ?>
                <td>
                    <span class="empty"><?php echo get_lang( 'You must give a score to add a comment' ); ?></span>
                    <input type="hidden" name="comment[<?php echo $userId; ?>]" value="" />
                </td>
                <?php endif; ?>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <input id="submit" type="submit" name="submitReport" value="<?php echo get_lang( 'OK' ); ?>" />
        <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exResetScores' ) ) , get_lang( 'Reset scores' ) ); ?>
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
</form>
<?php endif; ?>