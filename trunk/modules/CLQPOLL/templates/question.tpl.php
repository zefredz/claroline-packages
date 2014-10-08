<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 1.2.2 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<p><?php echo $this->msg ?></p>
<form method="post" action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
    <input type="hidden" name="pollId" value="<?php echo $this->pollId; ?>" />
    <input type="hidden" name="choiceId" value="<?php echo $this->choiceId; ?>" />
    <input type="hidden" name="userId" value="<?php echo $this->userId; ?>" />
    <input type="submit" name="" value="<?php echo get_lang( 'Yes' ); ?>" />
    <?php echo claro_html_button( claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlCancel . '&pollId=' . $this->pollId ) ) , get_lang("Cancel") ); ?>
</form>