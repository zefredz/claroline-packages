<?php
if ( count( get_included_files() ) == 1 ) die( '---' );

/**
 * CLAROLINE
 *
 * Subscription tool for Claroline - new form.lib.php
 * This file was created to add the possibility to preselect several option in a multiple list
 *
 * @author Pierre Raynaud <pierre.raynaud@u-clermont1.fr>
 *
 * @package SUBSCRIBE
 *
 */
 
 /**
 *
 * @param string $select_name name of the form (other param can be adds with $attr
 * @param array $list_option 2D table where key are labels and value are values
 *  with reverted set to false (default) or key are values and value are labels
 *  with reverted set to true
 * @param string $preselect name of the key in $list_option would be preselected
 * @param bool $reverted set the function in reverted mode to use value => label
 *  instead of label => value arrays (default false)
 * @return html output from a 2D table where key are name and value are label
 *
 * @author Christophe Gesché <moosh@claroline.net>
 *
 */
function CLFDclaro_html_form_select($select_name,$list_option,$preselect=null,$attr=null, $reverted = false)
{
    $html_select = '<select name="' . $select_name . '" ';
    if (is_array($attr)) foreach($attr as $attr_name=>$attr_value)
    $html_select .=' ' . $attr_name . '="' . $attr_value . '" ';
    $html_select .= '>' . "\n"
    .                CLFDclaro_html_option_list($list_option,$preselect, $reverted)
    .               '</select>' . "\n"
    ;

    return $html_select;
}


/**
 * return a string as html form option list to plce in a <select>
 * @param array $list_option 2D table where key are labels and value are values
 *  with reverted set to false (default) or key are values and value are labels
 *  with reverted set to true
 * @param string $preselect array of selected keys
 * @param bool $reverted set the function in reverted mode to use value => label
 *  instead of label => value arrays (default false)
 * @return html output of the select options
 *
 * @author Christophe Gesché <moosh@claroline.net>
 * Modification Pierre Raynaud <pierre.raynaud@u-clermont1.fr>
 *
 */
function CLFDclaro_html_option_list($list_option, $preselect, $reverted = false)
{
    if (!$preselect)
    $preselect = array();

    $html_option_list = '';
    if(is_array($list_option))
    {
        if ( ! $reverted )
        {
            foreach($list_option as $option_label => $option_value)
            {
                $html_option_list .= '<option value="' . $option_value . '"'
                .                    (in_array($option_value, $preselect) ?' selected="selected" ':'') . '>'
                .                    htmlspecialchars($option_label)
                .                    '</option >' . "\n"
                ;
            }
        }
        else
        {
            foreach($list_option as $option_value => $option_label)
            {
                $html_option_list .= '<option value="' . $option_value . '"'
                .                    (in_array($option_value, $preselect) ?' selected="selected" ':'') . '>'
                .                    htmlspecialchars($option_label)
                .                    '</option >' . "\n"
                ;
            }
        }
        return $html_option_list;
    }
    else
    {
        trigger_error('$list_option would be array()', E_USER_NOTICE);
        return false;
    }

}
?>
