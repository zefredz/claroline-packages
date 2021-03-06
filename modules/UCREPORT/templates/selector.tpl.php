<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */?>

<script type="text/javascript">
    $(document).ready(function(){
        $("#selectAll" ).click(function(){
            var is_checked=$(this).prop('checked');
            $(".itemSelect").prop('checked',is_checked);
        });
    });
</script>

<form id="itemSelection"
      method="post"
      action="<?php echo claro_htmlspecialchars( 'report.php?cmd=exGenerate' ); ?>">
    <table class="claroTable emphaseLine" style="width: 100%;">
        <thead>
            <tr class="headerX">
                <th><?php echo get_lang( 'Title' ); ?></th>
                <th><?php echo get_lang( 'Number of marks' ); ?></th>
                <th><?php echo get_lang( 'Weight' ); ?></th>
                <th><!--<?php echo get_lang( 'Select' ); ?>--><input id="selectAll" type="checkbox" /></th>
            </tr>
        </thead>
        <tbody>
<?php foreach( $this->itemList as $toolLabel => $datas ) : ?>
        <tr>
            <td class="toolName" colspan="4"><?php echo get_lang( 'To import from' ) . ': ' . get_lang( $datas[ 'name' ] ); ?></td>
        </tr>
    <?php if ( isset( $datas[ 'item' ] ) ) : ?>
        <?php foreach ( $datas[ 'item' ] as $itemId => $itemDatas ) : ?>
        <tr>
            <td><?php echo $itemDatas[ 'title' ]; ?></td>
            <td align="center"><?php echo $itemDatas[ 'submission_count' ]; ?></td>
            <td align="center"><input type="text"
                                      name="item[<?php echo $itemId; ?>][weight]"
                                      value="<?php echo $itemDatas[ 'weight' ]; ?>"
                                      size="3" />
            </td>
            <td align="center">
                <input class="itemSelect" type="checkbox" name="item[<?php echo $itemId; ?>][selected]" <?php echo $itemDatas['selected'] ? 'checked="yes"' : ''; ?> />
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td class="empty" colspan="4"><?php echo get_lang( 'empty' ); ?></td>
        </tr>
    <?php endif; ?>
<?php endforeach; ?>
        </tbody>
    </table>
    <div class="rightSubmit">
        <input type="submit" name="" value="<?php echo get_lang( 'Import' ); ?>" />
        <?php echo claro_html_button( claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
    </div>
</form>