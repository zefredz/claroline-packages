<?php
/**
 * CLAROLINE
 *
 * Functions file of the Subscription tool for Claroline
 *
 * @author Pierre Raynaud <pierre.raynaud@u-clermont1.fr>
 *
 * @package SUBSCRIBE
 *
 */
 
 
/**
 * This function displays the subscription list.
 *
 *    @param string $course_code,boolean $now_only
 * @return string
 */ 
function CLFDdisplayList($course_code,$every_sessions,$order='endDate',$asc='DESC')
{
    if (!$order)
    $order='endDate';
    if (!$asc)
    $asc='DESC';


    global $tbl_subscription;
    
    // Get the subscription list
    $sql_subscriptions = "SELECT id,intro_text,UNIX_TIMESTAMP(startDate) as startDate,UNIX_TIMESTAMP(endDate) as endDate,title,allow_modification 
                                 FROM `$tbl_subscription`
                                 WHERE course_code = '$course_code' ";
                                 
    if ($every_sessions == false)
    $sql_subscriptions .= "AND startDate < NOW()
                AND endDate > NOW()";
    
    $sql_subscriptions .= "ORDER BY ".$order." ".$asc;


    $infos = claro_sql_query_fetch_all_rows($sql_subscriptions);

    return $infos;
} 


/**
 * This function creates / edits a new session in the database
 *
 *    @param array $infos,int $session_id (opt)
 * @return boolean
 */
 function CLFDcreateEditSession($infos,$session_id=false)
 {
    global $tbl_subscription;
    global $_uid;
    global $_cid;
    
    if (isset($session_id))
    CLFDdeleteIncompatibility($_cid,$session_id);
 
     if (isset($session_id))
     $sql = "UPDATE `$tbl_subscription` SET "; 
     else
    $sql = "INSERT INTO `$tbl_subscription` SET "; 
 
     foreach ($infos as $nom_champ => $valeur)
     {
         // If it's an array, we have to process the date included
         if (is_array($valeur))
         {
             $valeur = "FROM_UNIXTIME(".CLFDarrayToTimestamp($valeur).")"; //"
         }
         else
         $valeur = "'".addslashes($valeur)."'";
         
         $sql .= "`".$nom_champ."` = ".$valeur.", ";
     }

     $sql .= "creator_id = ".$_uid.", course_code = '".$_cid."'";
     
     if (isset($session_id))
     $sql .= " WHERE id = ".$session_id;
     
    if (!isset($session_id))
    return claro_sql_query_insert_id($sql);
    
    elseif (claro_sql_query($sql))
     return $session_id;
     
 } 
 

/**
 *    Creates a timestamp from an array (mktime)
 *    @param array $date_array
 *    @return int
 */
 function CLFDarrayToTimestamp($dates_array)
 {
     return mktime($dates_array[0],$dates_array[1],$dates_array[2],$dates_array[3],$dates_array[4],$dates_array[5]);
 }
 
 
 /**
 *    Returns infos about a session
 *    @param int $session_id
 *    @return array
 */
 
 function CLFDinfoSession($session_id)
 {
     global $tbl_subscription;
     
     $sql_infos = "SELECT id,intro_text,UNIX_TIMESTAMP(startDate) as startDate,UNIX_TIMESTAMP(endDate) as endDate,title,allow_modification,max_users
                         FROM `$tbl_subscription`
                         WHERE id = $session_id";
     $infos = claro_sql_query_fetch_single_row($sql_infos);
     return $infos;
 }
 
 
 
 /**
 *    Returns the number of places available for a session
 *    @param int $session_id
 *    @return int
 */
 
 function CLFDgetRemainingPlaces($session_id)
 {
     global $tbl_subscriptionUsers;
     
     $infos = CLFDinfoSession($session_id);

     $max_users = $infos['max_users'];
     
     $sql_subscribers = "SELECT count(user_id)
                               FROM `$tbl_subscriptionUsers`
                               WHERE subscription_id = $session_id";
     $subscribe_users = claro_sql_query_get_single_value($sql_subscribers);
     
     return $max_users-$subscribe_users;
 }
 
 
 /**
 *    Deletes a session
 *    @param int $session_id
 *    @return boolean
 */
 function CLFDremoveSession($session_id,$course_code)
 {
     global $tbl_subscription;
     global $tbl_subscriptionUsers;
     
     $sql_remove = "DELETE FROM `$tbl_subscription`
                         WHERE id = $session_id
                         AND course_code = '".$course_code."'";
     if (!claro_sql_query($sql_remove))
     $error = true;
     
     $sql_remove_users = "DELETE FROM `$tbl_subscriptionUsers`
                                 WHERE subscription_id = $session_id";
     if (!claro_sql_query($sql_remove_users))
     $error = true;
     
     if(!CLFDdeleteIncompatibility($course_code,$session_id))
    $error = true;     
     
     if (isset($error))
     return false;
     else
     return true;
 }
 
 
 /**
 *    Checks if the user is allowed to subscribe to a session
 *    @param int $user_id,$session_id,$allow_user_to_modify
 *    @return boolean
 */
 function CLFDisAllowedToSubscribe($user_id,$session_id,$allow_user_to_modify)
 {
    global $_cid; 
 
    // Check for incompatibilities
    $incompat_list = CLFDcheckIncompatibilities($_cid,$session_id); 
     if (is_array($incompat_list))
    {    
        foreach ($incompat_list as $session_incompat)
        {
        //print $session_id." : ".$session_incompat."<br>";
            if (CLFDuserSubscription($user_id,$session_incompat))
            return false;
        } 
     }
     
     if (CLFDgetRemainingPlaces($session_id) && $allow_user_to_modify==1)
     return true;
     else
     return false;
 }
 
 /**
 *    Looks for the user's subscription infos
 *    @param int $user_id,$session_id
 *    @return boolean
 */
 function CLFDuserSubscription($user_id,$session_id)
 {
     global $tbl_subscriptionUsers;
     $sql_infos = "SELECT UNIX_TIMESTAMP(subscription_date) AS subscription_date FROM `$tbl_subscriptionUsers` WHERE user_id = $user_id AND subscription_id = $session_id";
     $infos = claro_sql_query_fetch_single_value($sql_infos);
     return $infos;
 }


/**
 *    Records the user's subscription in the db
 *    @param int $user_id,$session_id
 *    @return boolean
 */
 function CLFDsubscribeUser($user_id,$session_id)
 {
     global $tbl_subscriptionUsers;
     $sql_subscribe = "INSERT INTO `$tbl_subscriptionUsers`
                             SET subscription_id = $session_id,
                                  user_id = $user_id,
                                  subscription_date = NOW()";
     if (claro_sql_query($sql_subscribe))
     return true;
     else
     return false;
 }
 
 
 /**
 *    Deletes the user's subscription in the db
 *    @param int $user_id,$session_id
 *    @return boolean
 */
 function CLFDunsubscribeUser($user_id,$session_id)
 {
     global $tbl_subscriptionUsers;
     $sql_unsubscribe = "DELETE FROM `$tbl_subscriptionUsers` WHERE subscription_id = $session_id AND user_id = $user_id";
     if (claro_sql_query($sql_unsubscribe))
     return true;
     else
     return false;
 }
 
 
 /**
 *    Displays the subscribers' list
 *    @param int $session_id
 *    @return array
 */
 function CLFDgetSubscribersList($session_id)
 {
     global $tbl_subscriptionUsers;

    $tbl_user = claro_sql_get_tbl('user'); 
    $tbl_user = $tbl_user['user'];
     
     $sql_list = "SELECT nom AS name,prenom AS firstname,username,officialCode,email,phoneNumber,`$tbl_subscriptionUsers`.user_id,UNIX_TIMESTAMP(subscription_date) AS subscription_date
             FROM `$tbl_subscriptionUsers`
             LEFT JOIN `$tbl_user`
             ON `$tbl_user`.user_id = `$tbl_subscriptionUsers`.user_id
             WHERE subscription_id = $session_id
             ORDER BY name,firstname";
     $list = claro_sql_query_fetch_all_rows($sql_list);

     return $list;
 }
 
 /**
 
 */
 function CLFDgetUsersInCourse($course_code)
 {
    $tbl_user = claro_sql_get_tbl('user'); 
    $tbl_user = $tbl_user['user'];
    
    $tbl_rel_course_user = claro_sql_get_tbl('cours_user'); 
    $tbl_rel_course_user = $tbl_rel_course_user['cours_user'];
 
     $sqlGetUsers = "SELECT `user`.`user_id`      AS `user_id`,
                       `user`.`nom`          AS `name`,
                       `user`.`prenom`       AS `firstname`
               FROM `" . $tbl_user . "`           AS user,
                    `" . $tbl_rel_course_user . "` AS course_user
               WHERE `user`.`user_id`=`course_user`.`user_id`
               AND   `course_user`.`code_cours`='" . $course_code . "'";
   $users_list = claro_sql_query_fetch_all_rows($sqlGetUsers);
   
   return $users_list;
 }
 
 /**
 *
 * Deletes all the incompatibilities for a session
 * return boolean
 */ 
 function CLFDdeleteIncompatibility($course_code,$session_id)
 {
         global $tbl_incompatibility;
         $sql_delete_compat = "DELETE FROM `$tbl_incompatibility` 
                                      WHERE (incompatible_session_id = $session_id 
                                      OR session_id = $session_id)
                                      AND course_code = '".$course_code."'";
         if (claro_sql_query($sql_delete_compat))
         return true;
         else
         return false;
 }
 
 /**
 *
 * Add incompatibilities for a session
 * return boolean
 */ 
 function CLFDaddIncompatibilities($course_code,$session_id,$incompat_list)
 {
     global $tbl_incompatibility;
     
     if (!isset($incompat_list))
     return true;
     
     
     foreach ($incompat_list as $session)
     {
         $sql_add_incompat = "INSERT INTO `$tbl_incompatibility`
                                     SET session_id = $session_id,
                                          incompatible_session_id = $session,
                                          course_code = '".$course_code."'";
         
         if (!claro_sql_query($sql_add_incompat))
         $error = true;
     }
     
     if (!isset($error))
     return true;
     else
     return false;
 }
 
 /**
 *
 * Get the list of incompatible session with the given one
 * return array
 */ 
 function CLFDcheckIncompatibilities($course_code,$session_id)
 {
     global $tbl_incompatibility;
     
     $sql_incompat_list = "SELECT incompatible_session_id,session_id 
                                  FROM `$tbl_incompatibility`
                                  WHERE (session_id = $session_id
                                            OR incompatible_session_id = $session_id)
                                  AND course_code = '".$course_code."'";

     $output_list = claro_sql_query_fetch_all_cols($sql_incompat_list);
     
    $incompat_list = array();     

     foreach ($output_list as $element => $sous_elem)
     {
         foreach ($sous_elem as $valeur)
         {
             if (!in_array($valeur, $incompat_list) && $valeur != $session_id)
             $incompat_list[] = $valeur;
        }     
     }
     
     return $incompat_list;
 }
?>
