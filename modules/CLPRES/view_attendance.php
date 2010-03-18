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
 */
/*=====================================================================
   Initialisation
  =====================================================================*/
//FR Nom de l'outil
$tlabelReq = 'CLPRES';
//FR Chargement des fonctions, librairies et classes prédéfinis dans 
//FR Claroline.
require '../../claroline/inc/claro_init_global.inc.php';
add_module_lang_array($tlabelReq);
//FR Refuse l'affichage si on est pas dans un cours ou si on a pas la 
//FR permission d'aller dans ce cours.
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
require_once 'lib/attendance.lib.php';
/*=====================================================================
   Config
  =====================================================================*/
//FR charge la config
include claro_get_conf_repository() . 'CLPRES.conf.php';
$dialogBox = new DialogBox();
ClaroBreadCrumbs::getInstance()->setCurrent(ucfirst(get_lang('attendance')));
/*----------------------------------------------------------------------
  DB tables definition
  ----------------------------------------------------------------------*/
// get Claroline course table names
$toolTables = get_module_course_tbl(array('clpres_attendance'), claro_get_current_course_id());
/*=====================================================================
   Variables
  =====================================================================*/
//FR On prépare les variables
$userPerPage = get_conf('nbUsersPerPage', 50);
$can_export_attendance_list = get_conf('allow_export_csv');
$cmd = (isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '');
$offset = (int) isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
$tbl_mdb_names = claro_sql_get_main_tbl();
$detailDate = NULL;
$is_already_attendance = false;
$is_allowedToEdit = claro_is_allowed_to_edit();
/*=====================================================================
   Main Section
  =====================================================================*/
/*----------------------------------------------------------------------
   Get User List
  ----------------------------------------------------------------------*/
//FR Sélectionne tous les utilisateurs du cours actuellement ouvert.
//FR on va sélectionner toutes les dates différentes pour le cours 
$sqlGetUsers = "SELECT `user`.`user_id`      AS `user_id`,
                       `user`.`nom`          AS `nom`,
                       `user`.`prenom`       AS `prenom`,
                       `user`.`email`        AS `email`,
                       `course_user`.`profile_id`,
                       `course_user`.`isCourseManager`,
                       `course_user`.`tutor`  AS `tutor`,
                       `course_user`.`role`   AS `role`,
						`attend`.`date_att`,
						`attend`.`is_att`
               FROM `" . $tbl_mdb_names['user'] . "`           AS user,
                    `" . $tbl_mdb_names['rel_course_user'] . "` AS course_user,
					`" . $toolTables['clpres_attendance'] . "` AS attend
               WHERE `user`.`user_id`=`course_user`.`user_id`
			   AND `user`.`user_id`=`attend`.`user_id`
               AND   `course_user`.`code_cours`='" . claro_sql_escape(claro_get_current_course_id()) . "' 
			   AND `course_user`.`isCourseManager`= 0 
			   ORDER BY date_att";
$sqlNbrUser = "SELECT COUNT(*) as `qty_stu`
				FROM  `" . $tbl_mdb_names['rel_course_user'] . "`
				WHERE `code_cours`  = '" . claro_sql_escape(claro_get_current_course_id()) . "'
				AND `isCourseManager` = 0
				GROUP BY code_cours";
$result = claro_sql_query_get_single_row($sqlNbrUser);
$nbrUser = $result['qty_stu'];
//FR On garde en mémoire (dans la variable myPager) la liste des utilisateurs obtenus, on va chercher la variable userPerPage
//FR qui contient le nombre d'utilisateur a afficher par page (voir fichier : ./conf/def/xxx.php)
$myPager = new claro_sql_pager($sqlGetUsers, $offset, $userPerPage);
//FR on garde dans les variables la liste des utilisateurs et le nombre total d'utilisateur.
$userList = $myPager->get_result_list();
$userTotalNb = $myPager->get_total_item_count();
//FR on construit la barre d'outil
//FR on crée un lien pour revenir sur les présences d'aujourd'hui
$userMenu[] = claro_html_cmd_link(htmlspecialchars(Url::Contextualize(get_module_url('CLPRES') . '/index.php')), '<img src="' . get_icon_url('attendance') . '" alt="" />' . ucfirst(get_lang('back to attendance')));
//FR Récupération des commandes
if ($cmd == 'detailDate') {
    if (isset($_GET['dateDetail'])) {
        $detailDate = $_GET['dateDetail'];
    } else {
        $dialogBox->error(get_lang('no selected date'));
    }
}
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
    if ($cmd == 'exportDateChoice' && $can_export_attendance_list) {
        require_once (dirname(__FILE__) . '/lib/exportAttList.lib.php');
        //$inputText = new InputText("addDateInput",date("dMY"),get_lang("Add date: "),true);
        $dialogBox->form('<p>' . ucfirst(get_lang("add a date")) . '</p>' . '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n" . '<input name="cmd" type="hidden" value="exportDate" />' . "\n" . '<label for="dateToAdd">' . ucfirst(get_lang('start date')) . ': </label>' . claro_html_date_form('dayDateBegin', 'monthDateBegin', 'yearDateBegin') . '<br />' . "\n" . '<label for="dateToAdd">' . ucfirst(get_lang('end date')) . ': </label>' . claro_html_date_form('dayDateEnd', 'monthDateEnd', 'yearDateEnd') . '<br />' . "\n" . '<br />' . "\n" . '<input value="' . ucfirst(get_lang('continue')) . '" type="submit" />' . '</form>');
    }
    if ($cmd == 'exportDate' && $can_export_attendance_list) {
        require_once (dirname(__FILE__) . '/lib/exportAttList.lib.php');
        $dateBegin = claro_sql_escape($_POST['yearDateBegin']) . "-" . push_date_format(claro_sql_escape($_POST['monthDateBegin'])) . "-" . push_date_format(claro_sql_escape($_POST['dayDateBegin']));
        $dateEnd = claro_sql_escape($_POST['yearDateEnd']) . "-" . push_date_format(claro_sql_escape($_POST['monthDateEnd'])) . "-" . push_date_format(claro_sql_escape($_POST['dayDateEnd']));
        // contruction of XML flow
        $csv = export_attendance_list(claro_get_current_course_id(), $dateBegin, $dateEnd);
        if (! empty($csv)) {
            header("Content-type: application/csv");
            header('Content-Disposition: attachment; filename="' . claro_get_current_course_id() . '_attendancelist.csv"');
            echo $csv;
            exit();
        }
    }
    if ($cmd == 'addDate') {
        //$inputText = new InputText("addDateInput",date("dMY"),get_lang("Add date: "),true);
        $dialogBox->form('<p>' . ucfirst(get_lang("add a date")) . '</p>' . '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n" . '<input name="cmd" type="hidden" value="addDateFn" />' . "\n" . '<label for="dateToAdd">' . ucfirst(get_lang('date to add for attendance')) . ': </label>' . claro_html_date_form('dayDateToAdd', 'monthDateToAdd', 'yearDateToAdd') . '<br />' . "\n" . '<br />' . "\n" . '<input value="' . ucfirst(get_lang('continue')) . '" type="submit" />' . '</form>');
    }
    if ($cmd == 'addDateFn') {
        $dateToAdd = claro_sql_escape($_POST['yearDateToAdd']) . "-" . push_date_format(claro_sql_escape($_POST['monthDateToAdd'])) . "-" . push_date_format(claro_sql_escape($_POST['dayDateToAdd']));
        foreach ($userList as $thisUser) {
            //dateToAdd YYYY-MM-DD
            unset_attendance($thisUser['user_id'], $dateToAdd);
        }
        //FR On valide l'entrée pour rafraichir la page
        $dialogBox->success(ucfirst(get_lang("date add to the list")) . ' !<br/>
			<a href="' . $_SERVER['PHP_SELF'] . '">Ok</a>');
    }
    //FR affiche message de confirmation si valider
    if ($cmd == 'exUpdateDate') {
        if (isset($_POST['detailDate'])) {
            $is_already_attendance = true;
            $dialogBox->success(ucfirst(get_lang('validation stored')) . ' !!!');
            foreach ($userList as $thisUser) {
                if ($thisUser['date_att'] == $_POST['detailDate']) {
                    if (isset($_POST['attendance_' . $thisUser['user_id'] . '_' . $thisUser['date_att'] . ''])) {
                        set_attendance($thisUser['user_id'], $thisUser['date_att']);
                        //$out .= is_attendance($thisUser['user_id'], date("Y-m-d"))."set. ";
                    //$out .= "on met ". $thisUser['nom']." présent à la date ".date('dMy');
                    } else {
                        unset_attendance($thisUser['user_id'], $thisUser['date_att']);
                        //$out .= is_attendance($thisUser['user_id'], date("Y-m-d"))."unset. ";
                    //$out .= "on met ". $thisUser['nom']." absent à la date ".date('dMy');
                    }
                }
            }
        } else {
            $dialogBox->error(get_lang('no selected date'));
        }
    }
    //FR affiche validation de suppression de la date choisie
    if ($cmd == 'deleteDate') {
        $dialogBox->warning(ucfirst(get_lang('delete attendance date')) . ' !!!');
        if (isset($_GET['dateDel'])) {
            $dialogBox->form('<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n" . '<input name="cmd" type="hidden" value="deleteDateFn" />' . "\n" . '<input name="dateToDel" type="hidden" value="' . htmlspecialchars($_GET['dateDel']) . '" />' . "\n" . '<label for="dateToDel">' . ucfirst(get_lang('date to delete for attendance')) . ': ' . $_GET['dateDel'] . '</label>' . '<br />' . "\n" . '<br />' . "\n" . '<input value="' . ucfirst(get_lang('continue')) . '" type="submit" />' . '</form>');
        } else {
            $dialogBox->error(get_lang('no selected date'));
        }
    }
    //FR affiche validation de suppression de la date choisie
    if ($cmd == 'deleteDateFn') {
        if (isset($_POST['dateToDel'])) {
            $sql = "DELETE FROM `" . $toolTables['clpres_attendance'] . "` WHERE `date_att` = '" . claro_sql_escape($_POST['dateToDel']) . "'";
            claro_sql_query($sql);
            $dialogBox->success(ucfirst(get_lang('delete attendance date')) . ' !<br/>
			<a href="' . $_SERVER['PHP_SELF'] . '">Ok</a>');
        } else {
            $dialogBox->error(get_lang('error') . ' !!!');
        }
    }
    // Add a date for attendance
    $userMenu[] = claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=addDate')), '<img src="' . get_icon_url('calendar') . '" alt="calendar.png" />' . ucfirst(get_lang('add an attendance date')));
    if (get_conf('allow_export_csv')) {
        // Export CSV file of attendance
        $userMenu[] = claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=export')), '<img src="' . get_icon_url('export') . '" alt="" />' . ucfirst(get_lang('export attendance list')));
        // Export CSV file of attendance with date choice
        $userMenu[] = claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=exportDateChoice')), '<img src="' . get_icon_url('export') . '" alt="" />' . ucfirst(get_lang('export attendance list with date choice')));
    }
}
/*=====================================================================
Display section
  =====================================================================*/
//FR $out est notre variable où l'on va tapper tout le code html pour l'affichage
$out = '';
//FR barre d'outil on va afficher le nom du module et le nombre d'utilisateurs obtenus
$out .= claro_html_tool_title(get_lang('number') . ' : ' . $userTotalNb);
// Display tool links
// Affiche les liens (options) dans une barre horizontale
$out .= claro_html_menu_horizontal($userMenu);
/*----------------------------------------------------------------------
   Display pager
  ----------------------------------------------------------------------*/
$out .= $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF']);
//$sortUrlList = $myPager->get_sort_url_list($_SERVER['PHP_SELF']);
/*----------------------------------------------------------------------
   Display table header
  ----------------------------------------------------------------------*/
// Display Forms or dialog box(if needed)
//FR Affiche le formulaire ou la fenêtre si nécessaire (formulaire pour nous)
$out .= $dialogBox->render();
//FR Tableau pour la liste des utilisateurs
$out .= '<table class="claroTable emphaseLine" width="100%" cellpadding="2" cellspacing="1" ' . ' border="0" summary="' . ucfirst(get_lang('course users list')) . '">' . "\n";
//FR thead = entête du tableau, il s'agit de la première ligne => les titres des colonnes
$out .= '<thead>' . "\n" . '<tr class="headerX" align="center" valign="top">' . "\n";
//FR htmlspecialchars permet d'afficher les caractères spéciaux (éèâ, ....)
if ($detailDate != NULL) {
    $out .= '<th>' . ucfirst(get_lang('last name')) . '</th>' . "\n" . '<th>' . ucfirst(get_lang('first name')) . '</th>' . "\n";
}
$out .= '<th>' . ucfirst(get_lang('date')) . '</th>' . "\n";
if ($is_allowedToEdit) {
    $out .= '<th>' . ucfirst(get_lang('delete')) . '</th>' . "\n";
}
if ($detailDate != NULL) {
    $out .= '<th>' . ucfirst(get_lang('attendance')) . '</th>' . "\n";
}
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
$datePrec = NULL;
$dateToFormat = new DateTime();
$totalPresDate = 0;
$cptUser = 0;
//FR pour chaque date on va afficher les présences
//FR On va parcourir chaque utilisateur dans la liste un par un et appliquer le code qui suit pour chacun
foreach ($userList as $thisUser) {
    //FR on récupère les dates du type (YYYY-MM-DD) de la table pour les reformater
    $yearAtt = substr($thisUser['date_att'], 0, 4);
    $monthAtt = substr($thisUser['date_att'], 5, 2);
    $dayAtt = substr($thisUser['date_att'], - 2);
    $dateToFormat->setDate($yearAtt, $monthAtt, $dayAtt);
    if ($datePrec == NULL) {
        if ($is_allowedToEdit) {
            $out .= '<tr align="center" valign="top">' . "\n";
            if ($detailDate != NULL)
                $out .= '<td>&nbsp;</td><td>&nbsp;</td>';
            $out .= '<td>' . claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=detailDate&dateDetail=' . $thisUser['date_att'] . '')), date_format($dateToFormat, "d/m/Y")) . '</td>';
            $out .= '<td>' . claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=deleteDate&dateDel=' . $thisUser['date_att'] . '')), '<img src="' . get_icon_url('delete') . '" alt="' . get_lang("delete") . '" />') . '</td><td>&nbsp;</td></tr>' . "\n";
        } else {
            $out .= '<tr align="center" valign="top">' . "\n";
            if ($detailDate != NULL)
                $out .= '<td>&nbsp;</td><td>&nbsp;</td>';
            $out .= '<td>' . claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=detailDate&dateDetail=' . $thisUser['date_att'] . '')), date_format($dateToFormat, "d/m/Y")) . '</td>';
            $out .= '</tr>' . "\n";
        }
    } elseif ($datePrec != $thisUser['date_att']) {
        $cptUser = 0;
        $totalPresDate = 0;
        if ($is_allowedToEdit) {
            $out .= '<tr align="center" valign="top">' . "\n";
            if ($detailDate != NULL)
                $out .= '<td>&nbsp;</td><td>&nbsp;</td>';
            $out .= '<td>' . claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=detailDate&dateDetail=' . $thisUser['date_att'] . '')), date_format($dateToFormat, "d/m/Y")) . '</td>';
            $out .= '<td>' . claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=deleteDate&dateDel=' . $thisUser['date_att'] . '')), '<img src="' . get_icon_url('delete') . '" alt="' . get_lang("delete") . '" />') . '</td><td>&nbsp;</td></tr>' . "\n";
        } else {
            $out .= '<tr align="center" valign="top">' . "\n";
            if ($detailDate != NULL)
                $out .= '<td>&nbsp;</td><td>&nbsp;</td>';
            $out .= '<td>' . claro_html_cmd_link(htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=detailDate&dateDetail=' . $thisUser['date_att'] . '')), date_format($dateToFormat, "d/m/Y")) . '</td>';
            $out .= '</tr>' . "\n";
        }
    }
    $datePrec = $thisUser['date_att'];
    //FR on affiche icône de l'utilisateur et son numéro attitré
    if ($detailDate == $thisUser['date_att']) {
        $out .= '<tr align="center" valign="top">' . "\n" . '<td align="left">' . '<img src="' . get_icon_url('user') . '" alt="" />' . "\n" . '<small>' . $thisUser['user_id'] . '</small>' . "\n" . '&nbsp;';
        //FR On affiche le nom de l'utilisateur en mettant uniquement la première lettre en majuscule
        $out .= htmlspecialchars(ucfirst(strtolower($thisUser['nom'])));
        //FR On affiche son prénom
        $out .= '</td>' . '<td>' . htmlspecialchars($thisUser['prenom']) . '</td>';
    }
    if (is_attendance($thisUser['user_id'], $thisUser['date_att'])) {
        $totalPresDate ++;
    }
    // User attendance column
    $cptUser ++;
    if ($is_allowedToEdit) {
        if ($detailDate == $thisUser['date_att']) {
            $out .= '<td>&nbsp;</td>';
            $out .= '<td>&nbsp;</td><td>';
            if (is_attendance($thisUser['user_id'], $thisUser['date_att']) == - 1) {
                $out .= '<input type="checkbox" name="attendance_' . $thisUser['user_id'] . '_' . $datePrec . '"/>';
            } else 
                if (is_attendance($thisUser['user_id'], $thisUser['date_att'])) {
                    $out .= '<input type="checkbox" checked="true" name="attendance_' . $thisUser['user_id'] . '_' . $datePrec . '"/>';
                } else {
                    $out .= '<input type="checkbox" name="attendance_' . $thisUser['user_id'] . '_' . $datePrec . '"/>';
                }
            $out .= '</td>' . "\n";
        }
    } else {
        //rajouter elseif pour is_already_attendance afin de sélectionner les précédemment présent du jour
        if ($detailDate == $thisUser['date_att']) {
            $out .= '<td>&nbsp;</td><td>';
            if (is_attendance($thisUser['user_id'], $thisUser['date_att']) == - 1) {
                $out .= '<img src="' . get_icon_url('unknow') . '" alt="' . get_lang('unknow') . '" />';
            } else 
                if (is_attendance($thisUser['user_id'], $thisUser['date_att'])) {
                    $out .= '<img src="' . get_icon_url('valid') . '" alt="' . get_lang('present') . '" />';
                } else {
                    $out .= '<img src="' . get_icon_url('unvalid') . '" alt="' . get_lang('missing') . '" />';
                }
            $out .= '</td>' . "\n";
        }
    }
    if ($detailDate == $thisUser['date_att']) {
        if ($previousUser == $thisUser['user_id']) {
            $out .= '<td>&nbsp;</td>' . "\n";
        }
        $out .= '</tr>' . "\n";
        if ($cptUser == $nbrUser)
            $out .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align=center>' . get_lang('total') . ': ' . $totalPresDate . '</td></tr>';
    }
    $previousUser = $thisUser['user_id'];
} // END - foreach users
/*----------------------------------------------------------------------
   Display table footer
  ----------------------------------------------------------------------*/
$out .= '</tbody>' . "\n" . '</table>' . "\n";
$out .= '<input type="hidden" name="cmd" value="exUpdateDate"/>';
if ($detailDate != NULL)
    $out .= '<input type="hidden" name="detailDate" value="' . $detailDate . '"/>';
$out .= '<input type="submit" value="Valider"/></form>';
$out .= $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF']);
$claroline->display->body->appendContent($out);
echo $claroline->display->render();
?>