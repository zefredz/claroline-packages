<?php
function getItemsSpec($tbl){

	$exits = checkIfTableExists($tbl);
	if ($exists)
		$itemsAlreadyExist = checkIfItemsExists($tbl);
	else
	{
		createTable($tbl);
		$itemsAlreadyExist = checkIfItemsExists($tbl);
	}

	if (!$itemsAlreadyExist)
		setItemsInTbl($tbl);
	else
	{
		$getRank = "select id, visibility from ".$tbl." order by rank asc";
		$resRank =  mysql_query($getRank);
		while ($dataRank = mysql_fetch_assoc($resRank)){
			$spec[] .= $dataRank['id'];
			$spec[] .= $dataRank['visibility'];
		}
		return $spec;
	}

}

function checkIfTableExists($tbl){
	$req = "select id from ".$tbl." limit 1";
	if (mysql_query($req))
		return true;
	else
		return false;
}

function checkIfItemsExists($tbl){

	$checkItems = "select id from ".$tbl."";
	$resItems = mysql_query($checkItems) or die ("Cannot do that...".mysql_error());
	
	if (mysql_num_rows($resItems) >= 1)
		return true;
	else
		return false;

}

function createTable($tbl){
	mysql_query("CREATE TABLE IF NOT EXISTS ".$tbl." (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `rank` INT(11) NOT NULL,
  `visibility` ENUM('VISIBLE','INVISIBLE') NOT NULL DEFAULT 'VISIBLE',
  PRIMARY KEY(id)
) ENGINE=MyISAM");
}

function setItemsInTbl($tbl){

	$setDescription = "insert into ".$tbl." (description,rank,visibility) values ('description',1,'VISIBLE')";
	$setCalendar = "insert into ".$tbl." (description,rank,visibility) values ('calendar',2,'VISIBLE')";
	$setAnnouncement = "insert into ".$tbl." (description,rank,visibility) values ('announcement',3,'VISIBLE')";

	mysql_query($setDescription);
	mysql_query($setCalendar);
	mysql_query($setAnnouncement);

	getItemsSpec($tbl);

}

function displayItem($id,$rank,$visibility,$is_allowedToEdit){

	if ($id == 1)
	{
		$output = displayDescription($id,$rank,$is_allowedToEdit,$visibility);
	}
	elseif ($id == 2)
	{
		$output = displayCalendar($id,$rank,$is_allowedToEdit,$visibility);
	}
	elseif ($id == 3)
	{
		$output = displayAnnouncement($id,$rank,$is_allowedToEdit,$visibility);
	}

	return $output;

}

function displayDescription($id,$rank,$is_allowedToEdit,$visibility){

	$portlet = new CLDSC_Portlet();
	$outPortlet = $portlet->render();
	$outputDesc .= '<div style="background:#f5f5f5;">' . $outPortlet . $displayArrow = manageMv($id,$rank,$is_allowedToEdit) . $displayEye = manageVisibility($id,$is_allowedToEdit,$visibility) .'<hr></div>';

	return $outputDesc;

}

function displayAnnouncement($id,$rank,$is_allowedToEdit,$visibility){

	$portlet = new CLANN_Portlet();
	$outPortlet = $portlet->render();
	$outputAnn .= '<div style="background:#f5f5f5;">' . $outPortlet . $displayArrow = manageMv($id,$rank,$is_allowedToEdit) . $displayEye = manageVisibility($id,$is_allowedToEdit,$visibility) .'<hr></div>';

	return $outputAnn;

}

function displayCalendar($id,$rank,$is_allowedToEdit,$visibility){

	$portlet = new CLCAL_Portlet();
	$outPortlet = $portlet->render();
	$outputCal .= '<div style="background:#f5f5f5;">' . $outPortlet . $displayArrow = manageMv($id,$rank,$is_allowedToEdit) . $displayEye = manageVisibility($id,$is_allowedToEdit,$visibility) .'<hr></div>';
	return $outputCal;

}

function manageMv($id,$rank,$is_allowedToEdit){

	if (!$is_allowedToEdit)
		return false;
	else
	{
		if ($rank == 1)
			return '<a href='.$_SERVER['PHP_SELF'].'?action=mvDown&id='.$id.'><img src="../../module/CLCOURSE/img/move_down.png"></a>';
		elseif ($rank == 2)
			return '<a href='.$_SERVER['PHP_SELF'].'?action=mvUp&id='.$id.'><img src="../../module/CLCOURSE/img/move_up.png"></a><a href='.PHP_SELF().'?action=mvDown&id='.$id.'><img src="../../module/CLCOURSE/img/move_down.png"></a>';
		elseif ($rank == 3)
			return '<a href='.$_SERVER['PHP_SELF'].'?action=mvUp&id='.$id.'><img src="../../module/CLCOURSE/img/move_up.png"></a>';
	}

}

function setNewSpec($thisTable,$id,$action){

	if ($action == 'mvDown' || $action == 'mvUp')
		setNewRank($thisTable,$id,$action);
	elseif($action == 'mkVisible' || $action == 'mkInvisible')
		setNewVisibility($thisTable,$id,$action);

}

function setNewRank($thisTable,$id,$action){

	if ($action == 'mvDown')
	{
		$orderBy = 'rank asc';
	}
	elseif ($action == 'mvUp')
	{
		$orderBy = 'rank desc';
	}

	$boolTheGoodOne = false;

	$getCurrentRank = "select id, rank from ".$thisTable." order by ".$orderBy;
	$resCurrentRank = mysql_query($getCurrentRank);
	while ($dataCurrentRank = mysql_fetch_assoc($resCurrentRank))
	{
		if ($boolTheGoodOne)
		{
			$nextId = $dataCurrentRank['id'];
			$nextRank = $dataCurrentRank['rank'];

			$setDown = "update ".$thisTable." set rank = ".mysql_real_escape_string((int)$nextRank)." where id = ".$currentId;
			mysql_query($setDown);
			
			$setUp = "update ".$thisTable." set rank = ".mysql_real_escape_string((int)$currentRank)." where id = ".$nextId;
			mysql_query($setUp);

			$boolTheGoodOne = false;
		}

		if ($id == $dataCurrentRank['id'])
		{
			$boolTheGoodOne = true;
			$currentId = $dataCurrentRank['id'];
			$currentRank = $dataCurrentRank['rank'];
		}

	}

}

function manageVisibility($id,$is_allowedToEdit,$visibility){

	if (!$is_allowedToEdit)
		return false;
	else
	{
		if ($visibility == 'VISIBLE')
			return '<a href='.$_SERVER['PHP_SELF'].'?action=mkInvisible&id='.$id.'><img src="../../module/CLCOURSE/img/visible.png"></a>';
		elseif ($visibility == 'INVISIBLE')
			return '<a href='.$_SERVER['PHP_SELF'].'?action=mkVisible&id='.$id.'><img src="../../module/CLCOURSE/img/invisible.png"></a>';
	}

}

function setNewVisibility($thisTable,$id,$action){

	if ($action == 'mkInvisible')
	{
		$newVisibility = 'INVISIBLE';
	}
	elseif ($action == 'mkVisible')
	{
		$newVisibility = 'VISIBLE';
	}

	$setNewVisibility = "update ".$thisTable." set visibility = '".mysql_real_escape_string($newVisibility)."' where id = ".$id;
	mysql_query($setNewVisibility);

}
?>
