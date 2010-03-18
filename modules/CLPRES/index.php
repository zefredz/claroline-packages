<?php // $Id$
/**
 * CLPRES tool
 * Tableau de liste de présence
 * 
 * @version     1.0
 * @author      Lambert Jérôme <lambertjer@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2.0
 * @package     CLPRES
 *
 */
/*=====================================================================
   Initialisation
  =====================================================================*/
//FR Nom de l'outil
$tlabelReq = 'CLPRES';
//FR Chargement des fonctions, librairies et classes prédéfinis dans Claroline.
require '../../claroline/inc/claro_init_global.inc.php';
add_module_lang_array($tlabelReq);
//FR Refuse l'affichage si on est pas dans un cours ou si on a pas la permission d'aller dans ce cours.
if (! claro_is_in_a_course() || ! claro_is_course_allowed() || ! get_init('is_authenticated'))
    claro_disp_auth_form(true);
    //FR Préparer le module a afficher en page.
claro_set_display_mode_available(true);
/*=====================================================================
   Library
  =====================================================================*/
//FR récupère les librairies
require_once get_path('incRepositorySys') . '/lib/admin.lib.inc.php';
require_once get_path('incRepositorySys') . '/lib/user.lib.php';
require_once get_path('incRepositorySys') . '/lib/course_user.lib.php';
require_once get_path('incRepositorySys') . '/lib/pager.lib.php';
require_once get_path('incRepositorySys') . '/lib/form.class.php';
require_once get_path('incRepositorySys') . '/lib/utils/input.lib.php';
require_once get_path('incRepositorySys') . '/lib/core/linker.lib.php';
require_once 'lib/attendance.lib.php';
/*=====================================================================
   Config
  =====================================================================*/
$nameTools = ucfirst(get_lang('attendance'));
//FR charge la config
include claro_get_conf_repository() . 'CLPRES.conf.php';
//FR prépare les affichages d'informations
$dialogBox = new DialogBox();
//FR On prépare l'affichage du Display
ClaroBreadCrumbs::getInstance()->setCurrent(ucfirst(get_lang('attendance')));
// Initalisation du Linker
// ResourceLinker::init();
// $currentLocator = ResourceLinker::$Navigator->getCurrentLocator( array('id'=>1) );
/*----------------------------------------------------------------------
  DB tables definition
  ----------------------------------------------------------------------*/
// run course installer for on the fly table creation
install_module_in_course('CLPRES', claro_get_current_course_id());
/*=====================================================================
   Variables
  =====================================================================*/
//FR On prépare les variables
$userPerPage = get_conf('nbUsersPerPage', 50);
$can_export_attendance_list = get_conf('allow_export_csv');
$cmd = (isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '');
$offset = (int) isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
$tbl_mdb_names = claro_sql_get_main_tbl();
$is_already_attendance = false;
$is_allowedToEdit = claro_is_allowed_to_edit();
//FR Gestion des données utilisateurs
// $userInput = Claro_UserInput::getInstance();
/*=====================================================================
   Main Section
  =====================================================================*/
/*----------------------------------------------------------------------
   Get User List
  ----------------------------------------------------------------------*/
//FR Sélectionne tous les utilisateurs du cours actuellement ouvert.
$sqlGetUsers = "SELECT `user`.`user_id`      AS `user_id`,
                       `user`.`nom`          AS `nom`,
                       `user`.`prenom`       AS `prenom`,
                       `user`.`email`        AS `email`,
                       `course_user`.`profile_id`,
                       `course_user`.`isCourseManager`,
                       `course_user`.`tutor`  AS `tutor`,
                       `course_user`.`role`   AS `role`
				FROM `" . $tbl_mdb_names['user'] . "`           AS user,
                    `" . $tbl_mdb_names['rel_course_user'] . "` AS course_user
               WHERE `user`.`user_id`=`course_user`.`user_id`
               AND   `course_user`.`code_cours`='" . claro_sql_escape(claro_get_current_course_id()) . "' 
			   AND `course_user`.`isCourseManager`= 0";
//FR On garde en mémoire (dans la variable myPager) la liste des utilisateurs obtenus, on va chercher la variable userPerPage
//FR qui contient le nombre d'utilisateur a afficher par page (voir fichier : ./conf/def/xxx.php)
$myPager = new claro_sql_pager($sqlGetUsers, $offset, $userPerPage);
//FR on garde dans les variables la liste des utilisateurs et le nombre total d'utilisateur.
$userList = $myPager->get_result_list();
$userTotalNb = $myPager->get_total_item_count();
//FR on construit la barre d'outil
//FR on crée un lien pour visualiser les présences antérieures
$userMenu = NULL;
if ($is_allowedToEdit || get_conf('allow_users_to_see')) {
    //FR permet de visualiser le tableau récapitulatif des présences
    $userMenu[] = claro_html_cmd_link(htmlspecialchars(Url::Contextualize(get_module_url('CLPRES') . '/view_attendance.php')), '<img src="' . get_icon_url('class') . '" alt="" />' . ucfirst(get_lang('view attendance')));
}
if ($is_allowedToEdit && get_conf('allow_export_csv')) {
    // Export CSV file of attendance
    $userMenu[] = claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=export')), '<img src="' . get_icon_url('export') . '" alt="" />' . ucfirst(get_lang('export attendance list')));
}
//FR Récupération des commandes
if ($is_allowedToEdit) {
    if ($cmd == 'export' && $can_export_attendance_list) {
        require_once (dirname(__FILE__) . '/lib/exportAttList.lib.php');
        // contruction of XML flow
        $csv = export_attendance_list(claro_get_current_course_id());
        if (! empty($csv)) {
            header("Content-type: application/csv");
            header('Content-Disposition: attachment; filename="' . claro_get_current_course_id() . '_attendancelist.csv"');
            echo $csv;
            exit();
        }
    }
    //FR affiche message de confirmation si valider
    if ($cmd == 'exUpdateDate') {
        $is_already_attendance = true;
        $dialogBox->success('Validation enregistrée !!!');
        foreach ($userList as $thisUser) {
            if (isset($_POST['attendance_' . $thisUser['user_id'] . ''])) {
                set_attendance($thisUser['user_id'], date("Y-m-d"));
                //$out .= is_attendance($thisUser['user_id'], date("Y-m-d"))."set. ";
            //$out .= "on met ". $thisUser['nom']." présent à la date ".date('dMy');
            } else {
                unset_attendance($thisUser['user_id'], date("Y-m-d"));
                //$out .= is_attendance($thisUser['user_id'], date("Y-m-d"))."unset. ";
            //$out .= "on met ". $thisUser['nom']." absent à la date ".date('dMy');
            }
        }
    }
}
/*=====================================================================
Display section
  =====================================================================*/
//FR $out est notre variable où l'on va tapper tout le code html pour l'affichage
$out = '';
//FR barre d'outil on va afficher le nom du module et le nombre d'utilisateurs obtenus
$out .= claro_html_tool_title($nameTools . ' (' . get_lang('number') . ' : ' . $userTotalNb . ')');
// Display Forms or dialog box(if needed)
//FR Affiche le formulaire ou la fenêtre si nécessaire
$out .= $dialogBox->render();
// Display tool links
//FR Affiche les liens (options) dans une barre horizontale
if ($userMenu != NULL) {
    $out .= claro_html_menu_horizontal($userMenu);
}
/*----------------------------------------------------------------------
   Display pager
  ----------------------------------------------------------------------*/
$out .= $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF']);
//$sortUrlList = $myPager->get_sort_url_list($_SERVER['PHP_SELF']);
/*----------------------------------------------------------------------
   Display table header
  ----------------------------------------------------------------------*/
//FR Tableau pour la liste des utilisateurs
$out .= '<table class="claroTable emphaseLine" width="100%" cellpadding="2" cellspacing="1" ' . ' border="0" summary="' . ucfirst(get_lang('course users list')) . '">' . "\n";
//FR thead = entête du tableau, il s'agit de la première ligne => les titres des colonnes
$out .= '<thead>' . "\n" . '<tr class="headerX" align="center" valign="top">' . "\n" . //FR htmlspecialchars permet d'afficher les caractères spéciaux (éèâ, ....)
'<th>' . ucfirst(get_lang('last name')) . '</th>' . "\n" . '<th>' . ucfirst(get_lang('first name')) . '</th>' . "\n" . '<th>' . ucfirst(get_lang('attendance')) . ' (' . date("d/m/Y") . ')</th>' . "\n";
$out .= '</tr></thead><tbody><form action="' . $_SERVER['PHP_SELF'] . '" method="POST">';
//FR Fin de l'entête du tableau
/*----------------------------------------------------------------------
   Display users
  ----------------------------------------------------------------------*/
//FR On prépare les affichages
$i = $offset;
$previousUser = - 1;
//FR On remet au début le "pointeur" de la liste des utilisateurs
reset($userList);
//FR On va parcourir chaque utilisateur dans la liste un par un et appliquer le code qui suit pour chacun
foreach ($userList as $thisUser) {
    // User name column
    $i ++;
    //FR on affiche icône de l'utilisateur et son numéro attitré
    $out .= '<tr align="center" valign="top">' . "\n" . '<td align="left">' . '<img src="' . get_icon_url('user') . '" alt="" />' . "\n" . '<small>' . $i . '</small>' . "\n" . '&nbsp;';
    //FR On affiche le nom de l'utilisateur en mettant uniquement la première lettre en majuscule
    $out .= htmlspecialchars(ucfirst(strtolower($thisUser['nom'])));
    //FR On affiche son prénom
    $out .= '</td>' . '<td>' . htmlspecialchars($thisUser['prenom']) . '</td>' . //FR On affiche son id
    // User attendance column
    '<td>';
    if ($is_allowedToEdit) {
        if (is_attendance($thisUser['user_id'], date("Y-m-d")) == - 1) {
            $out .= '<input type="checkbox" name="attendance_' . $thisUser['user_id'] . '"/>';
        } else 
            if (is_attendance($thisUser['user_id'], date("Y-m-d"))) {
                $out .= '<input type="checkbox" checked="true" name="attendance_' . $thisUser['user_id'] . '"/>';
            } else {
                $out .= '<input type="checkbox" name="attendance_' . $thisUser['user_id'] . '"/>';
            }
    } else {
        //rajouter elseif pour is_already_attendance afin de sélectionner les précédemment présent du jour
        if (is_attendance($thisUser['user_id'], date("Y-m-d")) == - 1) {
            $out .= '<img src="' . get_icon_url('unknow') . '" alt="' . get_lang('unknow') . '" />';
        } else 
            if (is_attendance($thisUser['user_id'], date("Y-m-d"))) {
                $out .= '<img src="' . get_icon_url('valid') . '" alt="' . get_lang('present') . '" />';
            } else {
                $out .= '<img src="' . get_icon_url('unvalid') . '" alt="' . get_lang('missing') . '" />';
            }
    }
    $out .= '</td>' . "\n";
    if ($previousUser == $thisUser['user_id']) {
        $out .= '<td>&nbsp;</td>' . "\n";
    }
    $out .= '</tr>' . "\n";
    $previousUser = $thisUser['user_id'];
} // END - foreach users
/*----------------------------------------------------------------------
   Display table footer
  ----------------------------------------------------------------------*/
$out .= '</tbody>' . "\n" . '</table>' . "\n";
if ($is_allowedToEdit) {
    $out .= '<input type="hidden" name="cmd" value="exUpdateDate"/>';
    $out .= '<input type="submit" value="Valider"/></form>';
}
$out .= $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF']);
$claroline->display->body->appendContent($out);
echo $claroline->display->render();
?>