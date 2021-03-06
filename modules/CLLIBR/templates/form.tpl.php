<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>
<strong><?php echo $this->message ?></strong>
<form method="post" action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
<?php foreach( $this->xid as $index => $field ) : ?>
    <input type="<?php  echo isset( $field['type'] )    ? $field[ 'type' ]  : 'text'; ?>"
           name="<?php  echo isset( $field[ 'name' ] )  ? $field[ 'name' ]  : 'field_' . $index; ?>"
           value="<?php echo isset( $field[ 'value' ] ) ? $field[ 'value' ] : ''; ?>" />
<?php endforeach; ?>
    <input type="submit" name="create" value="<?php echo get_lang( 'Create' ); ?>" />
    <?php echo claro_html_button( claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlCancel ) ) , get_lang( 'Cancel' ) ); ?>
</form>