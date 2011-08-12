<?php // $Id$
/**
 * Examination report
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCEXAM/UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<span>
    <a class="claroCmd" href="report.php">
        <img src="<?php echo get_icon_url( 'go_left' ); ?>" alt="back" />
        <?php echo get_lang( 'Back to the report list' ); ?>
    </a>
</span>

<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Session' ); ?>
            </th>
            <!--
            <th>
                <?php echo get_lang( 'Date' ); ?>
            </th>
            -->
            <th>
                <?php echo get_lang( 'Score' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Comment' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
<?php if ( ! empty( $this->result ) ) : ?>
    <?php foreach ( $this->result as $session ) : ?>
        <tr>
            <td>
                <?php echo $session[ 'title' ]; ?>
            </td>
            <!--
            <td>
                <?php echo $session[ 'date' ]; ?>
            </td>
            -->
            <td>
                <?php echo $session[ 'score' ]; ?>
            </td>
            <td>
                <?php echo $session[ 'comment' ]; ?>
            </td>
    <?php endforeach; ?>
<?php else: ?>
        <tr>
            <td style="color: #888888; text-align: center; font-style: italic;"colspan="4"><?php echo get_lang( 'No result at this time' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>