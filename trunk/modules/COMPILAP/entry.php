<?php
/*
Applet COMPILATIO v1.6 testé sur Claroline 1.8.11 et 1.9rc5
Compilatio - www.compilatio.net
*/

if ( $GLOBALS[ 'tlabelReq' ] == 'CLWRK' )
{
    // prépare la variable à afficher
    $html ='';
    if(get_conf('compi_active'))
        {
        /*Si on se trouve dans un cours*/
    if(claro_is_in_a_course())
        {
        /*Si on se trouve dans un assesment/travail */
        if(isset($_REQUEST['assigId']))
            {
            /*Si on est dans le module compilatio on propose le retour au module Travaux sinon on propose d'aller a la page concernant celui-ci du  module compilatio */
            if (claro_get_tool_name(claro_get_current_tool_id())=="Compilatio")
                {
                $html.="<a href='/claroline/work/workList.php?assigId=".$_REQUEST['assigId']."' ><img src='".get_module_url('COMPILAP')."/icon.png' alt=''/>".get_lang('Back to Assignments')."</a><br />";
                }
            else
                {
                $html.="<a href='".get_module_url('COMPILAT')."/compilist.php?assigId=".$_REQUEST['assigId']."' ><img src='".get_module_url('COMPILAP')."/icon.png' alt=''/>".get_lang('Analyse it with Compilatio')."</a><br />";
                }
            }
        else
            {
            /*Si on est dans le module compilatio on peut aller directement à l'accueil du module travaux sinon on peux aller directement à l'accueil du module compilatio*/
            if (claro_get_tool_name(claro_get_current_tool_id())=="Compilatio")
                {
                $html.="<a href='/claroline/work/work.php' ><img src='".get_module_url('COMPILAP')."/icon.png' alt=''/>".get_lang('Back to Assignments')."</a><br />";
                }
            else
                {
                $html.="<a href='".get_module_url('COMPILAT')."/entry.php'><img src='".get_module_url('COMPILAP')."/icon.png' alt=''/>".get_lang('Compilatio for Assignments')."</a><br />";
                }
            }
        }
    }
    /* on affiche la variable */
    $claro_buffer->append($html);
}