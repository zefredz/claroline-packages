<?php // $Id$
/**
 * Claroline Poll Tool
 *
 * @version     UCREPORT 0.9.3 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>
<?php if ( count( $this->reportDataList ) ) : ?>
<?php include dirname( __FILE__ ) . '/menu.tpl.php'; ?>
<table id="report" class="claroTable emphaseLine" style="width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'User'); ?></th>
            <?php foreach( $this->assignmentDataList as $id => $assignment ) : ?>
                <?php if ( $assignment[ 'active' ] ) : ?>
            <th>
            <?php echo $assignment[ 'title' ]; ?><br />
            <em>[<?php echo get_lang( 'weight' ) . ' : ' . 100 * $assignment[ 'proportional_weight' ]; ?> % ]</em>
            </th>
                <?php endif; ?>
            <?php endforeach; ?>
            <th>
                <?php echo get_lang( 'Weighted global score' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
    <tr class="average">
        <td class="cell">
            <?php echo get_lang( 'Average' ); ?>
        </td>
        <?php foreach( $this->assignmentDataList as $assignment ) : ?>
            <?php if ( $assignment[ 'active' ] ) : ?>
        <td class="cell">
                <?php if ( isset( $assignment[ 'average'] ) ) : ?>
                <?php echo $assignment[ 'average']; ?>
                <?php else :?>
            <span class="empty"><?php echo get_lang( 'empty' ); ?></span>
                <?php endif; ?>
        </td>
            <?php endif; ?>
        <?php endforeach; ?>
        <td class="cell">
            <?php echo $this->averageScore; ?>
        </td>
    </tr>
    <?php foreach( $this->reportDataList as $userId => $userReport ) : ?>
        <?php if ( $userId == claro_get_current_user_id() || claro_is_allowed_to_edit() ) : ?>
        <tr>
            <td>
                <?php echo $this->userList[ $userId ][ 'lastname' ] . ' ' . $this->userList[ $userId ][ 'firstname' ]; ?>
            </td>
            <?php foreach( $this->assignmentDataList as $id => $assignment ) : ?>
                <?php if ( $assignment[ 'active' ] ) : ?>
            <td class="cell">
                    <?php if ( isset( $userReport[ $id ] ) ) : ?>
                    <?php echo $userReport[ $id ]; ?>
                    <?php else :?>
                <span class="empty"><?php echo get_lang( 'empty' ); ?></span>
                    <?php endif; ?>
            </td>
                <?php endif; ?>
            <?php endforeach; ?>
            <td class="cell final">
                <?php if ( isset( $this->userList[ $userId ][ 'final_score' ] ) ) : ?>
                <?php echo $this->userList[ $userId ][ 'final_score' ]; ?>
                <?php else : ?>
                <span class="empty"><?php echo get_lang( 'incomplete' ); ?></span>
                    <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
    <?php if ( $this->comment ) :?>
<p class="exam"><strong><?php echo get_lang( 'Comment' ); ?> :</strong> <?php echo $this->comment; ?></p>
    <?php endif; ?>
<?php else : ?>
<p class="empty"><?php echo get_lang( 'No result at this time' ); ?></p>
<?php endif; ?>