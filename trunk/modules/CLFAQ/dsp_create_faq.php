<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

	// protect file
	if( count( get_included_files() ) == 1 )
	{
		die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
	}
	
	if($is_allowedToAdmin == false) 
	{
		claro_die( get_lang('Not allowed action !') );
	}
	
    /**
     * CLAROLINE
     *
     * @version 1.9 $Revision$
     *
     * @copyright 2001-2006 Universite catholique de Louvain (UCL)
     *
     * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
     * This program is under the terms of the GENERAL PUBLIC LICENSE (GPL)
     * as published by the FREE SOFTWARE FOUNDATION. The GPL is available
     * through the world-wide-web at http://www.gnu.org/copyleft/gpl.html
     *
     * @author KOCH Gregory <gregk84@gate71.be>
     *
     * @package CLFAQ
     */
	
	//message d'erreur si il n'y a pas de catégorie
	
	$showEmptyCategories = $is_allowedToAdmin;
	$arr_category = array();
	
	$category = new category();
	$categoryCount = category::getCategoryCount();	
	if( !( $categoryList = $category->getCategoryList($showEmptyCategories) ) )
	{
		$errorMsg = ( get_lang('Invalid data !') );
	}
	else
	{
	    foreach($categoryList as $key=>$val)
		{
			$arr_category[$val['clfaq_category_id']] = $val['clfaq_category'];
		}
	}
    
    // Breadcrumps

    $interbredcrump[]= array ( 'url' => 'index.php', 'name' => get_lang('F.A.Q') );
	$interbredcrump[]= array ( 'url' => NULL, 'name' => get_lang('Create F.A.Q') );
	
	// --------- Claroline header and banner ----------------    

    require_once get_path('incRepositorySys') . "/claro_init_header.inc.php";
	
	// --------- Claroline body ----------------    
		
	// toolTitle
    $output->append(claro_html_tool_title( get_lang('F.A.Q') ) . "\n");
	
	if(isset($error)) 
	{
		if($error != 0) 
		{
			 $output->append('<ul class="error">');
			 	$output->append($error_message);
			 $output->append('</ul>');
		}
	}
	
	if(isset($errorMsg)) 
	{
		 $output->append('<ul class="error">');
			$output->append('<li>'.$errorMsg.'</li>');
		 $output->append('</ul>');
	}

	$output->append('
	
	<form action="'.$_SERVER['PHP_SELF'].'?fuseaction='.$form_xfa_post.'" method="post">
    <input type="hidden" name="claroFormId" value="'.uniqid('').'" />
    <input type="hidden" name="form_action" value="'.$form_action.'" />
	<input type="hidden" name="frm_id" value="'.$frm_id.'" />    
	');
	
	if(0 != $categoryCount)
	{
	    	
		$output->append('
		<table>
			<tr>
				<td>'.get_lang("Category").' : </td>
				<td>');
				
					$output->append('
					<select class="form" name="frm_category">
						<option>.:: '.get_lang("Select your category").' ::.</option>');
						foreach($arr_category as $key=>$val) {
							$isselected = '';
							$isselected = $key == $frm_category ? 'selected="selected"' : '';
							$output->append('<option value="'.$key.'" '.$isselected.'>'.$val.'</option>');
						}
					$output->append('
					</select>
				
				</td>
			</tr>
			<tr>
				<td>'.get_lang("Question").' : </td>
				<td><input class="form" name="frm_question" value="'.$frm_question.'" type="text" size="53" /></td>
			</tr>
			<tr>
				<td>'.get_lang("answer").' : </td>
				<td>
					<textarea class="form" name="frm_answer" cols="40" rows="8">'.$frm_answer.'</textarea>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<a href="'.dirname($_SERVER['PHP_SELF']).'/'.$form_xfa_cancel.'"><input class="buttom" type="button" value="'.get_lang("Cancel").'" onclick="document.location=\''.dirname($_SERVER['PHP_SELF']).'/'.$form_xfa_cancel.''.'\'" /></a>
					<input class="buttom" type="submit" value="'.get_lang("Save").'" />
				</td>
			</tr>
		</table> 
		</form>
		');
	
	}
	else
	{
		 $errorMsg = get_lang("Vous ne pouvez pas créer de F.A.Q car vous n'avez pas créer de catégorie");
	}

	// print display
	
	echo $output->getContents();

	// ------------ Claroline footer ---------------
	
	require_once get_path('incRepositorySys') . '/claro_init_footer.inc.php';	

?>