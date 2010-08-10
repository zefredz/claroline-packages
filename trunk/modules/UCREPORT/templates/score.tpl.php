<?php // $Id$
/**
 * Claroline Poll Tool
 *
 * @version     UCREPORT 0.9.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>
<?php if ( claro_is_allowed_to_edit() ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) . '&cmd=rqShowReport&reportId=' . $this->reportId ); ?>">
        <img src="<?php echo get_icon_url( 'go_left' ); ?>" alt="back" />
        <?php echo get_lang( 'Back to the report' ); ?>
    </a>
</span>
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
            <td >
                <?php echo $this->userList[ $userId ][ 'firstname' ] . ' ' . $this->userList[ $userId ][ 'lastname' ]; ?>
            </td>
    <?php if ( isset( $scoreList[ Report::EXAMINATION_ID ] ) ) : ?>
            <td class="cell">
                <?php echo $scoreList[ Report::EXAMINATION_ID ]; ?>
            </td>
        <?php if ( isset( $this->userList[ $userId ][ 'comment' ] ) ) : ?>
            <td>
                <?php echo $this->userList[ $userId ][ 'comment' ]; ?>
            </td>
        <?php else : ?>
            <td class="empty"><?php echo get_lang( 'empty' ); ?></td>
        <?php endif; ?>
    <?php else : ?>
            <td class="empty" colspan="2"><?php echo get_lang( 'empty' ); ?></td>
    <?php endif; ?>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>