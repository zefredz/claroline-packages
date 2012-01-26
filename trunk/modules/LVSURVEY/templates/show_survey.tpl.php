<?php 
JavascriptLoader::getInstance()->load('jquery');
JavascriptLoader::getInstance()->load('jquery.limit-1.2');
$jslang = new JavascriptLanguage;
$jslang->addLangVar('__INCOMPLETE_SURVEY_ALERT__');
ClaroHeader::getInstance()->addInlineJavascript($jslang->render());

JavascriptLoader::getInstance()->load('surveyQuestionForm');
CssLoader::getInstance()->load('LVSURVEY');

$editIcon       = claro_html_icon('edit',       get_lang('Modify'),         get_lang('Modify'));


$currentUserId = claro_get_current_user_id();
$participation = $this->participation;
$surveyLineList = $this->survey->getSurveyLineList();
$allowChange = $participation->isNew() || $this->survey->isAllowedToChangeAnswers();

usort($surveyLineList, array('SurveyLine', 'cmp_surveyLines'));

echo claro_html_tool_title($this->survey->title);
$cmd_menu = array();

if($this->editMode)
{       
    $cmd_menu[] = '<a class="claroCmd" href="edit_survey.php?surveyId='.$this->survey->id.'">'.$editIcon.' '.get_lang('Edit survey properties').'</a>';
    $cmd_menu[] = '<a class="claroCmd" href="question_pool.php?surveyId='.$this->survey->id.'">'.get_lang('Add question').'</a>';
    $cmd_menu[] = '<a class="claroCmd" href="edit_separator.php?surveyId='.$this->survey->id.'">'.get_lang('Add separator').'</a>';
}
if($this->editMode || $this->survey->areResultsVisibleNow())
{
    $cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$this->survey->id.'">'.get_lang('View results of this survey').'</a>';
}

echo '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>';

$infoBox = new DialogBox();
if($this->survey->is_anonymous)
{
    $infoBox->info( get_lang('This survey is anonymous. We won\'t display your identification .'));
}
else
{
    $infoBox->info( get_lang('This survey is not anonymous. Your identification will be displayed.'));
}
if($this->survey->hasEnded())
{
    $infoBox->info( get_lang('This survey has ended. You cannot change your answers anymore.'));
}
if('VISIBLE_AT_END' == $this->survey->resultsVisibility && !$this->survey->hasEnded())
{
    $infoBox->info(get_lang('Results will be visible only at the end of the survey on %date.',
            array('%date'=>claro_html_localised_date(get_locale('dateFormatLong'), $this->survey->endDate))));
}
if('INVISIBLE' == $this->survey->resultsVisibility )
{
    $infoBox->info(get_lang('Results are not visible by the participants of this Survey.'));
}

echo $infoBox->render();
?>

<div><?php echo $this->survey->description; ?></div>

<?php  if(!$this->survey->hasEnded()) : ?>
<form id="surveyForm" method="post" action="show_survey.php?surveyId=<?php echo $this->survey->id; ?>">
    <input type="hidden" name="claroFormId" value="<?php echo uniqid(''); ?>" />
    <input type="hidden" name="cmd" value="saveParticipation" />
    <input type="hidden" name="surveyGoToConf" value="" />
    <input type="hidden" name="surveyId" value="<?php echo $this->survey->id; ?>" />
    <input type="hidden" name="participationId" value="<?php echo $participation->id; ?>" />
    <?php endif; ?>

    <div class="LVSURVEYQuestionList">
        <?php  if(empty($surveyLineList)) :?>
            <?php echo get_lang('No question in this survey'); ?>
        <?php else :?>
            <?php foreach($surveyLineList as $surveyLine) :?>
                <?php echo $surveyLine->render($this->editMode, $participation, $allowChange); ?>
            <?php endforeach;?>
        <?php endif; ?>
    </div>
    <?php  if(!$this->survey->hasEnded()) : ?>
    <?php if($allowChange) : ?>
        <input type="submit" value="<?php echo get_lang('Submit'); ?>" />
    <?php endif; ?>
</form>
<?php endif; ?>