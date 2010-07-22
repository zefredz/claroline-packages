<?php // $Id$
/**
 * Claroline Poll Tool
 *
 * @version     UCREPORT 0.8.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>
<strong><?php echo get_lang( 'Please, give a title to your new report...' ); ?></strong>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exCreateReport' ) ); ?>" >
    <input type="text" name="title" value="" />
    <input type="submit" name="create" value="<?php echo get_lang( 'Create' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=rqShowReport' ) ) , get_lang("Cancel") ); ?>
</form>