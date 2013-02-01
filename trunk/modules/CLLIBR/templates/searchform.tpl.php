<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php if ( $this->tagCloud ) : ?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#tagCloud").hide();
        $("#showTagCloud").click(function(){
            $("#tagCloud").toggle();
            $("#mainContent").toggleClass( 'galant' );
        });
    });
</script>
<?php endif; ?>

<form id="searchForm" method="post" action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqSearch') ); ?>">
    <input type="submit" value="<?php echo get_lang( 'Quick search' ); ?>" />
    <input type="text" name="searchString" value="" />
    <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?option=multisearch') ); ?>">
        <img src="<?php echo get_icon_url( 'plus' ); ?>" alt="<?php echo get_lang( 'Advanced search' ); ?>" />
    </a>
<?php if ( $this->tagCloud ) : ?>
    <a id="showTagCloud" href="#claroBody">
        <img src="<?php echo get_icon_url( 'tagcloud' ); ?>" alt="<?php echo get_lang( 'Show tagcloud' ); ?>" />
    </a>
<?php endif; ?>
</form>

<?php if ( $this->tagCloud ) : ?>
<div id="tagCloud"><?php echo $this->tagCloud; ?></div>
<?php endif; ?>