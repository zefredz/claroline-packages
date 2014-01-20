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
<p><?php echo $this->message ?></p>
<form method="post" action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
<?php foreach( $this->xid as $name => $value ) : ?>
    <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
<?php endforeach; ?>
    <input type="submit" name="" value="<?php echo get_lang( 'Yes' ); ?>" />
    <?php echo claro_html_button( claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlCancel ) ) , get_lang( 'Cancel' ) ); ?>
</form>
