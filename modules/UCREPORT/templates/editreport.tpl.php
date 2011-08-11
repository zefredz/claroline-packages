<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>
<?php if ( claro_is_allowed_to_edit() ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowReport') ); ?>">
        <img src="<?php echo get_icon( 'statistics' ); ?>" alt="current results" />
        <?php echo get_lang( 'Generate the preview' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddExam') ); ?>">
        <img src="<?php echo get_icon( 'new_item' ); ?>" alt="new item" />
        <?php echo get_lang( 'Add a mark' ); ?>
    </a>
</span>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exEditReport' ) ); ?>" >
    <table class="claroTable emphaseLine" style="width: 100%;">
        <thead>
            <tr class="headerX">
                <th><?php echo get_lang( 'Assignment' ); ?></th>
                <th><?php echo get_lang( 'Activated' ); ?></th>
                <th><?php echo get_lang( 'Weight' ); ?></th>
                <th><?php echo get_lang( 'Proportional weight' ); ?></th>
                <th><?php echo get_lang( 'Edit' ); ?></th>
                <th><?php echo get_lang( 'Delete' ); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach( $this->itemDataList as $itemId => $item ) : ?>
            <tr <?php if ( is_string( $itemId ) ) echo 'class="exam"'; ?>>
                <td>
                    <?php if ( is_string( $itemId ) ) echo get_lang( 'Additional mark' ) . ' : '; ?>
                    <?php echo $item[ 'title' ]; ?>
                </td>
                <td align="center">
                    <input type="checkbox" name="active[<?php echo $itemId; ?>]" <?php if ( $item[ 'active' ] ) echo 'checked="checked"'; ?> />
                </td>
                <td>
                    <input type="text" size="2" name="weight[<?php echo $itemId; ?>]" value="<?php echo $item[ 'weight' ]; ?>" />
                </td>
                <td>
                    <?php echo 100 * $item[ 'proportional_weight' ]; ?> %
                </td>
            <?php if ( is_string( $itemId ) ) : ?>
                <td align="center">
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditScores&examinationId=' . $itemId ) ); ?>">
                        <img src="<?php echo get_icon( 'edit' ); ?>" alt="edit" />
                    </a>
                </td>
                <td align="center">
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteExam&examinationId=' . $itemId ) ); ?>">
                        <img src="<?php echo get_icon( 'delete' ); ?>" alt="edit" />
                    </a>
                </td>
            <?php else : ?>
                <td align="center">
                    <img src="<?php echo get_icon( 'edit_disabled' ); ?>" alt="edit disabled" />
                </td>
                <td align="center">
                    <img src="<?php echo get_icon( 'delete_disabled' ); ?>" alt="delete disabled" />
                </td>
            <?php endif; ?>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <input id="submit" type="submit" name="submitReport" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
</form>
<?php endif; ?>