<?php
session_start();
if(isset($_GET['action']) && !empty($_GET['action']))
{
    $action = $_GET['action'];
    switch($action) {
        case 'insertPlaybackEvent' : insertPlaybackEvent($_GET['playbackEventTime'],$_SESSION['playbackId']); break;
        case 'initialisePlayback' : initialisePlayback($_SESSION['youtubeId'],$_GET['pieceName'],$_GET['pieceLength']); break;
		default: die("not recognised");
	}
}

function initialisePlayback($youtubeId, $pieceName, $pieceLength) {

	$pieceId = getPieceId($youtubeId);
	echo $pieceId;
	if ($pieceId == null)
	{
		echo "was null";
		// create new piece
		$pieceId = insertPieceDetails($youtubeId, $pieceName, $pieceLength);
	}
	
	$_SESSION['pieceId'] = $pieceId;
	$_SESSION['playbackId'] = getPlaybackId();
}

function getPieceId($youtubeId) {
	$sql = "SELECT `piece`.`PieceId` FROM `musicprojectdb`.`piece` WHERE `piece`.`YoutubeId` = '".$youtubeId."'; ";
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}
	$result = mysqli_query($con,$sql);
	if (!$result) {
		$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}
	$pieceId = mysqli_fetch_object($result)->PieceId;
	return $pieceId;
}
function insertPieceDetails($youtubeId, $pieceName, $pieceLength) {

	$sql = "INSERT INTO `musicprojectdb`.`piece` (`YoutubeId`,`PieceName`,`PieceLength`) VALUES ('".$youtubeId."','".$pieceName."',".$pieceLength.");";
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}
	$result = mysqli_query($con,$sql);
	if (!$result) {
		$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}
	return mysqli_insert_id($con);

}
function getPlaybackId() {
	$sql = "INSERT INTO `musicprojectdb`.`playback` (`UserId`,`PieceId`) VALUES (".$_SESSION['userId'].",".$_SESSION['pieceId'].");";
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}
	$result = mysqli_query($con,$sql);
	if (!$result) {
		$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}
	return mysqli_insert_id($con);
}

function insertPlaybackEvent($playbackEventTime, $playbackId){
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}
	$sql = "INSERT INTO PlaybackEvent (playbackId, playbackEventTime) VALUES (".$playbackId.",".$playbackEventTime.")";
	
	$result = mysqli_query($con,$sql);
		if (!$result) {
			$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
			$message .= 'Whole query: ' . $sql;
			die($message);
		}}

