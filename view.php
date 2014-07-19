<?php
session_start();

$row = getPlaybackStats($_SESSION['playbackId']);
header('Content-Type: application/json');
echo json_encode($row);

function getUserInfo($userId)
{
	$sql = "SELECT `user`.`UserName` FROM `musicprojectdb`.`user` WHERE `user`.`UserId` = ".$userId;
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}
	$result = mysqli_query($con,$sql);
	if (!$result) {
		$message  = 'Invalid query: ' . mysqli_error() . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}
	return mysqli_fetch_object($result)->UserName; 
}

function getPiecesHistory($userId)
{
	
}

function getPlaybackStats($playbackId)
{
	$sql = "SELECT `playbackevent`.`PlaybackEventTime` FROM `musicprojectdb`.`playbackevent` WHERE `PlaybackId` = ".$playbackId;
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
	
	$rows = array();
    while($row = $result->fetch_array(MYSQLI_NUM)[0]) {
        $rows[] = $row;
    }
    return $rows;
}