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
<h1>
    <?php echo $this->courseData[ 'name' ] . ' (' . $this->courseData[ 'sysCode' ] . ')'; ?>
</h1>
<h2>
    <?php echo get_lang( 'Student report'); ?>
</h2>

<table class="claroTable emphaseLine" style="width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'User'); ?></th>
            <?php foreach( $this->assignmentDataList as $id => $assignment ) : ?>
                <?php if ( $assignment[ 'active' ] ) : ?>
            <th>
            <?php echo $assignment[ 'title' ]; ?><br />
            <em>[<?php echo get_lang( 'wt.' ) . ' : ' . 100 * $assignment[ 'proportional_weight' ]; ?> % ]</em>
            </th>
                <?php endif; ?>
            <?php endforeach; ?>
            <th>
                <?php echo get_lang( 'Weighted global score' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
    <tr class>
        <td>
            <strong><?php echo get_lang( 'Average' ); ?></strong>
        </td>
        <?php foreach( $this->assignmentDataList as $id => $assignment ) : ?>
            <?php if ( $assignment[ 'active' ] ) : ?>
        <td>
                <?php if ( isset( $this->assignmentDataList[ $id ][ 'average'] ) ) : ?>
            <strong><?php echo $this->assignmentDataList[ $id ][ 'average']; ?></strong>
                <?php else :?>
            <span style="color: silver; font-style: italic;"><?php echo get_lang( 'empty' ); ?></span>
                <?php endif; ?>
        </td>
            <?php endif; ?>
        <?php endforeach; ?>
        <td>
            <strong><?php echo $this->averageScore; ?></strong>
        </td>
    </tr>
    <?php foreach( $this->reportDataList as $userId => $userReport ) : ?>
        <?php if ( $userId == claro_get_current_user_id() || claro_is_allowed_to_edit() ) : ?>
        <tr>
            <td>
            <?php if ( $userId ) : ?>
                <?php echo $this->userList[ $userId ][ 'lastname' ] . ' ' . $this->userList[ $userId ][ 'firstname' ]; ?>
            <?php else : ?>
                <strong><?php echo get_lang( 'Average score' ); ?></strong>
            <?php endif; ?>
            </td>
            <?php foreach( $this->assignmentDataList as $id  => $assignment ) : ?>
                <?php if ( $assignment[ 'active' ] ) : ?>
            <td>
                    <?php if ( isset( $userReport[ $id ] ) ) : ?>
                <?php echo $userReport[ $id ]; ?>
                    <?php else :?>
                <span style="color: silver; font-style: italic;"><?php echo get_lang( 'empty' ); ?></span>
                    <?php endif; ?>
            </td>
                <?php endif; ?>
            <?php endforeach; ?>
            <td>
                <?php if ( isset( $this->userList[ $userId ][ 'final_score' ] ) ) : ?>
                <strong><?php echo $this->userList[ $userId ][ 'final_score' ]; ?></strong>
                <?php else : ?>
                <span style="color: silver; font-style: italic;"><?php echo get_lang( 'incomplete' ); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
    <?php if ( $this->comment ) :?>
<p class="exam"><?php echo $this->comment; ?></p>
    <?php endif; ?>