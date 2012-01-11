<?php 
    echo claro_html_tool_title(get_lang('List of surveys'));
?>
<?php 
if($this->editMode)
{
    $cmd_menu = array();
    $cmd_menu[] = '<a class="claroCmd" href="edit_survey.php">'.get_lang('New survey').'</a>';
    $cmd_menu[] = '<a class="claroCmd" href="import_survey.php?cmd=submit">'.get_lang('Import survey').'</a>';
    $cmd_menu[] = '<a class="claroCmd" href="question_pool.php">'.get_lang('Question pool').'</a>';
    echo '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>';
}
?>
<table class="claroTable emphaseLine" border="0" align="center" cellpadding="2" cellspacing="2" width="100%">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang('Survey title'); ?></th>
            <th><?php echo get_lang('Access'); ?></th>
            <?php if( $this->editMode ) : ?>
                <th><?php echo get_lang('Export'); ?></th>
                <th><?php echo get_lang('Modify'); ?></th>
                <th><?php echo get_lang('Delete'); ?></th>
                <th><?php echo get_lang('Move'); ?></th>
                <th><?php echo get_lang('Visibility'); ?></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php $colspan = $this->editMode?7:2; ?>
    <?php if( empty($this->surveyList)): ?>
        <tr>
            <td <?php  echo 'colspan="'.($this->editMode?7:2).'"'?>><?php echo get_lang('Empty'); ?></td>
        </tr>
        <?php else :?>
        <?php 
            $surveyIcon     = claro_html_icon('survey');
            $exportIcon     = claro_html_icon('export');
            $editIcon       = claro_html_icon('edit',       get_lang('Modify'),         get_lang('Modify'));
            $deleteIcon     = claro_html_icon('delete');
            $moveUpIcon     = claro_html_icon('move_up',    get_lang('Move Up'),        get_lang('Move Up'));
            $moveDownIcon   = claro_html_icon('move_down',  get_lang('Move Down'),      get_lang('Move Down'));
            $visibleIcon    = claro_html_icon('visible',    get_lang('Make Visible'),   get_lang('Modify'));
            $invisibleIcon  = claro_html_icon('invisible',  get_lang('Make Invisible'), get_lang('Modify'));
        ?>    
        <?php foreach( $this->surveyList as $aSurvey ): ?>
        <?php  if(!$this->editMode && !$aSurvey->is_visible) continue;?>
            <tr <?php echo (!$aSurvey->is_visible)?'class="invisible"':''; ?>>
                <td>
                    <?php
                    $urlShow = "show_survey.php?surveyId=".$aSurvey->id;
                    echo claro_html_link($urlShow, $surveyIcon.' '.$aSurvey->title,array('class' => 'item'));
                    ?>
                </td>
                <td>
                    <?php if(0 == $aSurvey->startDate) : ?>
                        <?php  echo get_lang("Closed"); ?>
                        <?php if ($this->editMode) : ?>
                            <?php 
                            $actionText = get_lang('Start now');
                            $actionURL =  'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=surveyStart';
                            echo link_to($actionText,$actionURL);
                            ?>
                        <?php endif;?>
                    <?php elseif(time() < $aSurvey->startDate) : ?>
                        <?php  echo get_lang("Accessible from %date", array( '%date' => claro_html_localised_date(get_locale('dateFormatLong'), $aSurvey->startDate))); ?>
                        <?php if ($this->editMode) : ?>
                            <?php 
                            $actionText = get_lang('Start now');
                            $actionURL =  'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=surveyStart';
                            echo link_to($actionText,$actionURL);
                            ?>
                        <?php endif;?>
                    <?php elseif(0 ==  $aSurvey->endDate) : ?>
                        <?php  echo get_lang("Accessible"); ?>
                        <?php if ($this->editMode) : ?>
                            <?php 
                            $actionText = get_lang('Close now');
                            $actionURL =  'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=surveyStop';
                            echo link_to($actionText,$actionURL);
                            ?>
                        <?php endif;?>
                    <?php elseif(time() <  $aSurvey->endDate) : ?>
                        <?php  echo get_lang("Accessible until %date", array( '%date' => claro_html_localised_date(get_locale('dateFormatLong'), $aSurvey->endDate))); ?>
                        <?php if ($this->editMode) : ?>
                            <?php 
                            $actionText = get_lang('Close now');
                            $actionURL =  'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=surveyStop';
                            echo link_to($actionText,$actionURL);
                            ?>
                        <?php endif;?>
                    <?php else: ?>
                        <?php  echo get_lang("Closed since %date", array( '%date' => claro_html_localised_date(get_locale('dateFormatLong'), $aSurvey->endDate))); ?>
                        <?php if ($this->editMode) : ?>
                            <?php 
                            $actionText = get_lang('Reopen now');
                            $actionURL =  'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=surveyStart';
                            echo link_to($actionText,$actionURL);
                            ?>
                        <?php endif;?>
                    <?php endif;?>
                </td>
                <?php if( $this->editMode ) : ?>
                <td align="center">
                    <?php
                        $urlExport = 'export_survey.php?surveyId='.$aSurvey->id;
                        echo claro_html_link($urlExport, $exportIcon);
                    ?>
                </td>
                <td align="center">
                    <?php
                        $urlEdit = 'edit_survey.php?surveyId='.$aSurvey->id;
                        echo claro_html_link($urlEdit, $editIcon);
                    ?>
                </td>
                <td align="center">
                    <?php
                        $urlDelete = 'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=surveyDelete';
                        echo claro_html_link($urlDelete, $deleteIcon);
                    ?>
                </td>
                <td align="center">
                    <?php
                        $urlMoveUp = 'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=surveyMoveUp';
                        echo claro_html_link($urlMoveUp, $moveUpIcon);
                        $urlMoveDown = 'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=surveyMoveDown';
                        echo claro_html_link($urlMoveDown, $moveDownIcon);
                    ?>
                </td>
                <td align="center">
                    <?php
                        if($aSurvey->is_visible)
                        {
                            $urlMakeInvis = 'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=toggleSurveyVisibility'; 
                            echo claro_html_link($urlMakeInvis, $visibleIcon);
                        }else{
                            $urlMakeVisible = 'survey_list.php?surveyId='.$aSurvey->id.'&amp;cmd=toggleSurveyVisibility'; 
                            echo claro_html_link($urlMakeVisible, $invisibleIcon);
                        }
                    ?>
                </td>
                <?php endif;?>
            </tr>
        <?php endforeach;?>
    <?php endif; ?>
    </tbody>
</table>