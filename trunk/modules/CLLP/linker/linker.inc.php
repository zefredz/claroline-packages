<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * @version 1.8 $Revision: 127 $
 * @copyright (c) 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author claroline Team <cvs@claroline.net>
 * @author Renaud Fallier <captren@gmail.com>
 * @author Frédéric Minne <minne@ipm.ucl.ac.be>
 *
 * @desc linker file adapted for learning path tool
 * @package CLLP
 *
 */
    // include for the linker
    require_once get_path('clarolineRepositorySys') . '/linker/resolver.lib.php';
    require_once get_path('clarolineRepositorySys') . '/linker/linker_sql.lib.php';
    require_once get_path('clarolineRepositorySys') . '/linker/CRLTool.php';
    require_once get_path('clarolineRepositorySys') . '/linker/linker.lib.php';
    require_once get_path('clarolineRepositorySys') . '/linker/jpspan.lib.php';

    $jpspanEnabled = claro_is_jpspan_enabled();

    if( claro_is_jpspan_enabled() )
    {
        require_once dirname(__FILE__) . '/linker_jpspan.lib.php';
    }
    else
    {
        require_once dirname(__FILE__) . '/linker_popup.lib.php';
    }

?>
