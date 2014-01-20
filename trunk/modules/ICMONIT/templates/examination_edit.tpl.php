<?php // $Id$
/**
 * Examinations / Student Monitoring Tool
 *
 * @version     ICEXAM 1.0.0 / ICMONIT 1.0.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICEXAM/ICMONIT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php if ( isset( $this->sessionId ) ) : ?>
<form method="post" action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exEdit' ) ); ?>">
    <input type="hidden" name="sessionId" value="<?php echo $this->sessionId; ?>" />
    <table>
        <tr>
            <td align="right"><?php echo get_lang( 'Title' ); ?> :</td>
            <td><input type="text" name="title" value="<?php echo $this->title; ?>" /></td>
        </tr>
        <tr>
            <td align="right"><?php echo get_lang( 'Max value' ); ?> :</td>
            <td><input type="text" name="maxValue" value="<?php echo $this->maxValue; ?>" /><br /></td>
        </tr>
    </table>
<?php else : ?>
<form method="post" action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exCreate' ) ); ?>">
    <table>
        <tr>
            <td><?php echo get_lang( 'Title' ); ?> :</td>
            <td><input type="text" name="title" value="" /></td>
        </tr>
        <tr>
            <td><?php echo get_lang( 'Max value' ); ?> :</td>
            <td><input type="text" name="maxValue" value="20" /></td>
        </tr>
    </table>
<?php endif; ?>
    <input id="submit" type="submit" name="submitReport" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
</form>
