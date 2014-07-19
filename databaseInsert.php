<?php
session_start();
if(isset($_GET['action']) && !empty($_GET['action']))
{
    $action = $_GET['action'];
    switch($action) {
        case 'insertPlaybackEvent' : insertPlaybackEvent($_GET['playbackEventTime'],$_SESSION['playbackId']); break;
        case 'insertPieceDetails' : insertPieceDetails($_SESSION['youtubeId'],$_GET['pieceName'],$_GET['pieceLength']); break;
		default: die("not recognised");
	}
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

function insertPieceDetails($youtubeId, $pieceName, $pieceLength) {

	if ($_SESSION['pieceId'] == null){
		$sql = "INSERT INTO `musicprojectdb`.`piece` (`YoutubeId`,`PieceName`,`PieceLength`) VALUES (".$youtubeId.",".$pieceName.",".$pieceLength.");";
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
	}
}