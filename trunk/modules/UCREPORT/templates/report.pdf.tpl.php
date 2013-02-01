<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>
<h1>
    <?php echo $this->courseData[ 'name' ] . ' (' . $this->courseData[ 'sysCode' ] . ')'; ?>
</h1>
<h2>
    <?php echo get_lang( 'Student Report'); ?>
</h2>

<table class="claroTable emphaseLine" style="width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'User'); ?></th>
            <?php foreach( $this->datas[ 'items' ] as $id => $item ) : ?>
                <?php if ( $item[ 'selected' ] ) : ?>
            <th>
            <?php echo $item[ 'title' ]; ?><br />
            <em>[<?php echo get_lang( 'wt.' ) . ' : ' . 100 * $item[ 'proportional_weight' ]; ?> % ]</em>
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
        <?php foreach( $this->datas[ 'items' ] as $id => $item ) : ?>
            <?php if ( $item[ 'selected' ] ) : ?>
        <td>
                <?php if ( isset( $this->datas[ 'items' ][ $id ][ 'average'] ) ) : ?>
            <strong><?php echo $this->datas[ 'items' ][ $id ][ 'average']; ?></strong>
                <?php else :?>
            <span style="color: silver; font-style: italic;"><?php echo get_lang( 'empty' ); ?></span>
                <?php endif; ?>
        </td>
            <?php endif; ?>
        <?php endforeach; ?>
        <td>
            <strong><?php echo $this->datas[ 'average' ]; ?></strong>
        </td>
    </tr>
    <?php foreach( $this->datas[ 'users' ] as $userId => $userDatas ) : ?>
        <?php if ( $userId == claro_get_current_user_id() || claro_is_allowed_to_edit() || $this->is_public ) : ?>
        <tr>
            <td>
            <?php if ( $userId ) : ?>
                <?php echo $this->datas[ 'users' ][ $userId ][ 'lastname' ] . ' ' . $this->datas[ 'users' ][ $userId ][ 'firstname' ]; ?>
            <?php else : ?>
                <strong><?php echo get_lang( 'Average score' ); ?></strong>
            <?php endif; ?>
            </td>
            <?php foreach( $this->datas[ 'items' ] as $id  => $item ) : ?>
                <?php if ( $item[ 'selected' ] ) : ?>
            <td>
                    <?php if ( isset( $this->datas[ 'report' ][ $userId ][ $id ] ) ) : ?>
                <?php echo $this->datas[ 'report' ][ $userId ][ $id ]; ?>
                    <?php else :?>
                <span style="color: silver; font-style: italic;"><?php echo get_lang( 'empty' ); ?></span>
                    <?php endif; ?>
            </td>
                <?php endif; ?>
            <?php endforeach; ?>
            <td>
                <?php if ( isset( $this->datas[ 'users' ][ $userId ][ 'final_score' ] ) ) : ?>
                <strong><?php echo $this->datas[ 'users' ][ $userId ][ 'final_score' ]; ?></strong>
                <?php else : ?>
                <span style="color: silver; font-style: italic;"><?php echo get_lang( 'incomplete' ); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
    <?php if( ! empty( $this->commentList ) ) : ?>
    <h3><?php echo get_lang( 'Comments' ); ?></h3>
        <?php foreach( $this->commentList  as $itemId => $comment ) : ?>
<p class="exam"><strong><?php echo get_lang( 'Comment for ' ) . $this->datas[ 'items' ][ $itemId ][ 'title' ]; ?> :</strong> <?php echo $comment; ?></p>
        <?php endforeach; ?>
    <?php endif; ?>