<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Plugin' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Active / inactive' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
<?php foreach( $this->pluginList as $pluginName => $plugin ) : ?>
        <tr>
            <td><?php echo get_lang( $pluginName ); ?></td>
    <?php if( $plugin ) : ?>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exDesactivatePlugin&plugin='. $pluginName ) );?>">
                    <img src="<?php echo get_icon_url( 'plugin' ); ?>" alt="<?php echo get_lang( 'Plugin active : click to desactivate' ); ?>"/>
                </a>
            </td>
    <?php else : ?>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exActivatePlugin&plugin='. $pluginName ) );?>">
                    <img src="<?php echo get_icon_url( 'plugin_disabled' ); ?>" alt="<?php echo get_lang( 'Plugin inactive : click to activate' ); ?>"/>
                </a>
            </td>
    <?php endif; ?>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>