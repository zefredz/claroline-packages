<?php
    
	claro_html_tool_title(get_lang('Participations'));
	CssLoader::getInstance()->load('LVSURVEY');
	
	$cmd_menu = array();
    $cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$this->survey->id.'">'.get_lang('View results of this survey').'</a>';
echo '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>';
?>

<div>
	<?php  echo $this->survey->description; ?>
</div>
<div class="LVSURVEYParticipation">
	<div class="LVSURVEYParticipationTitle">
		<?php  echo get_lang('Participations'); ?>
	</div>
	<div class="LVSURVEYParticipationBloc">
		<div class="LVSURVEYParticipationBlocTitle">
			<?php  echo get_lang('Course members who have participated'); ?>
		</div>
		<?php foreach($this->participantsMap[ShowParticipationPage::IN_COURSE_PARTICIPANTS] as $participant) :?>
			<div>
				<?php echo $participant['firstName']; ?> <?php echo $participant['lastName']; ?>
			</div>
		<?php endforeach;?>	
	</div>
	<div class="LVSURVEYParticipationBloc">
		<div class="LVSURVEYParticipationBlocTitle">
			<?php echo get_lang('Course members who haven\'t yet participated'); ?>
		</div>
		<?php foreach($this->participantsMap[ShowParticipationPage::IN_COURSE_NOT_PARTICIPANTS] as $participant) :?>
			<div>
				<?php echo $participant['firstName']; ?> <?php echo $participant['lastName']; ?>
			</div>
		<?php endforeach;?>		
	</div>
	<div class="LVSURVEYParticipationBloc">
		<div class="LVSURVEYParticipationBlocTitle">
			<?php echo get_lang('Participants who are not members of the course'); ?>
		</div>
		<?php foreach($this->participantsMap[ShowParticipationPage::OFF_COURSE_PARTICIPANTS] as $participant) :?>
			<div>
				<?php echo $participant['firstName']; ?> <?php echo $participant['lastName']; ?>
			</div>
		<?php endforeach;?>	
	</div>

</div>

<div class="LVSURVEYBloc" >
	<div class="LVSURVEYBlocTitle">
		<?php echo get_lang('Send message to course members who have not yet participated'); ?>
	</div>
	<form method="post">
		<input type="hidden" name="surveyId" value="<?php echo $this->survey->id ?>" />
    	<input type="hidden" name="claroFormId" value="<?php echo uniqid(''); ?>" />
    	<input type="hidden" name ="cmd" value="sendRecallMail" />
		<div>
			<?php echo claro_html_textarea_editor('emailBody',$this->emailBody); ?>
		</div>
		<div>
			<input type="submit" value="<?php echo get_lang('Send recall message'); ?>"/>
		</div>
	</form>
</div>
