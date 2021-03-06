<?php // $Id$
/**
 * Student Monitoring Tool
 *
 * @version     ICMONIT 1.0.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICMONIT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php if ( count( $this->datas ) ) : ?>
<table id="report" class="claroTable emphaseLine" style="width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'User'); ?></th>
            <?php if ( ! $this->id ) : ?>
            <th><?php echo get_lang( 'Activate' ) ; ?></th>
            <?php endif; ?>
            <?php foreach( $this->datas[ 'items' ] as $id => $item ) : ?>
            <th>
            <?php echo $item[ 'title' ]; ?><br />
            <em>[<?php echo get_lang( 'weight' ) . ' : ' . 100 * $item[ 'proportional_weight' ]; ?> % ]</em>
            </th>
            <?php endforeach; ?>
            <th>
                <?php echo get_lang( 'Weighted global score' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php if ( ! $this->id ) : ?>
    <form method="post"
          action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exActualize' ) );?>" />
    <?php endif; ?>
    <tr class="average">
        <td class="cell">
            <?php echo get_lang( 'Average' ); ?>
        </td>
        <?php if ( ! $this->id ) : ?>
        <td class="cell">
            <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exReset' ) );?>">
                [<?php echo get_lang( 'Reset' ); ?>]
            </a>
        </td>
        <?php endif; ?>
        <?php foreach( $this->datas[ 'items' ] as $item ) : ?>
        <td class="cell">
                <?php if ( isset( $item[ 'average' ] ) ) : ?>
                <?php echo $item[ 'average' ]; ?>
                <?php else :?>
            <span class="empty"><?php echo get_lang( 'empty' ); ?></span>
                <?php endif; ?>
        </td>
        <?php endforeach; ?>
        <td class="cell">
            <?php echo $this->datas[ 'average' ]; ?>
        </td>
    </tr>
    <?php foreach( $this->datas[ 'users' ] as $userId => $userDatas ) : ?>
        <?php if ( $userId == claro_get_current_user_id() || claro_is_allowed_to_edit() || $this->is_public ) : ?>
        <tr>
            <td>
                <?php echo $userDatas[ 'lastname' ] . ' ' . $userDatas[ 'firstname' ]; ?>
            </td>
            <?php if ( ! $this->id ) : ?>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exActivate&userId='. $userId . '&active=' . ! $userDatas[ 'active' ] ) );?>">
                    <?php if ( $userDatas[ 'active' ] ) : ?>
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
                    <?php else: ?>
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible'); ?>"/>
                    <?php endif; ?>
                </a>
            </td>
            <?php endif; ?>
            <?php foreach( $this->datas[ 'items' ] as $id => $item ) : ?>
            <td class="cell">
                    <?php if ( isset( $this->datas[ 'report' ][ $userId ][ $id ] ) ) : ?>
                        <?php if ( ! $this->id ) : ?>
                            <input type="text"
                                   size="2"
                                   name="mark[<?php echo $userId; ?>][<?php echo $id; ?>]" value="<?php echo $this->datas[ 'report' ][ $userId ][ $id ]; ?>" />
                        <?php else : ?>
                            <?php echo $this->datas[ 'report' ][ $userId ][ $id ]; ?>
                        <?php endif; ?>
                    <?php else :?>
                <span class="empty"><?php echo get_lang( 'empty' ); ?></span>
                    <?php endif; ?>
            </td>
            <?php endforeach; ?>
            <td class="cell final">
                <?php if ( isset( $this->datas[ 'users' ][ $userId ][ 'final_score' ] ) ) : ?>
                <?php echo $this->datas[ 'users' ][ $userId ][ 'final_score' ]; ?>
                <?php else : ?>
                <span class="empty"><?php echo get_lang( 'inactive' ); ?></span>
                    <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<?php if ( ! $this->id ) : ?>
    <input type="submit" name="create" value="<?php echo get_lang( 'Actualize' ); ?>" />
<?php endif; ?>
    <?php if ( ! isset( $this->datas[ 'report' ][ claro_get_current_user_id() ] ) && ! claro_is_allowed_to_edit() ) : ?>
<p class="noscore"><?php echo get_lang( 'You don\'t have score in this report' ); ?></p>
    <?php endif; ?>
    <?php if( ! empty( $this->commentList ) ) : ?>
    <h3><?php echo get_lang( 'Comments' ); ?></h3>
        <?php foreach( $this->commentList  as $itemId => $comment ) : ?>
<p class="exam"><strong><?php echo get_lang( 'Comment for ' ) . $this->itemList[ $itemId ][ 'title' ]; ?> :</strong> <?php echo $comment; ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if ( ! $this->id ) : ?>
    </form>
    <?php endif; ?>
    
<?php else : ?>
<p class="empty"><?php echo get_lang( 'No result at this time' ); ?></p>
<?php endif; ?>
