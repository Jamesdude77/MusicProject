<?php
session_start();
if(isset($_GET['action']) && !empty($_GET['action']))
{
    $action = $_GET['action'];
    switch($action) {
        case 'getPlaybackStats' :
			if(isset($_GET['playbackId']) && !empty($_GET['playbackId']))
			{
				getPlaybackStats($_GET['playbackId']);
			}
			else
				getPlaybackStats($_SESSION['playbackId']);
			break;
        case 'getPiecesList' :
			getPiecesList($_SESSION['userId']);
			break;
		case 'getPieceDuration' :
			getPieceDuration($_GET['pieceId']);
			break;
		case 'getPlaybackList' :
			getPlaybackList($_SESSION['userId'], $_GET['pieceId']);
			break;
		case 'getOtherPlaybackList' :
			getPlaybackList($_GET['userId'], $_GET['pieceId']);
			break;
		case 'getOtherUsersInfo' :
			getOtherUsersInfo($_SESSION['userId']);
			break;
		default: die("not recognised");
	}
}

function getOtherUsersInfo($userId)
{
	$sql = "SELECT `user`.`UserId`, `user`.`UserName` FROM `musicprojectdb`.`user` WHERE `user`.`UserId` != ".$userId;
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
	
	$rows = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows[] = $row;
    }
	
    header('Content-Type: application/json');
	echo json_encode($rows);
}

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

function getPiecesList($userId)
{
	// get distinct pieces per user, with total viewings count
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}

	$sql="SELECT `piece`.`PieceId`, `piece`.`PieceName`, COUNT(`playback`.`PlaybackId`) AS PlaybackCount, MAX(`playback`.`creationDate`) AS LatestPlayback, `piece`.`PieceLength`" .
			" FROM `musicprojectdb`.`playback` JOIN `musicprojectdb`.`piece` on playback.PieceId = piece.Pieceid" .
			" WHERE `playback`.`UserId` = ".$userId." GROUP BY playback.PieceId ORDER BY LatestPlayback DESC";
	$result = mysqli_query($con,$sql);
	if (!$result) {
		$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}
	
	$rows = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows[] = $row;
    }
	header('Content-Type: application/json');
	echo json_encode($rows);
}

function getPlaybackList($userId, $pieceId)
{
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}

	$sql="SELECT `playback`.`creationDate`, COUNT(playbackevent.playbackEventid) AS EventCount, `playback`.`PlaybackId`
		FROM `musicprojectdb`.`playback` JOIN `musicprojectdb`.`playbackevent` ON playback.PlaybackId = playbackevent.PlaybackId
		WHERE playback.PieceId = ".$pieceId." AND playback.userId = ".$userId."
		GROUP BY PlaybackId ORDER BY PlaybackId DESC";
			
	$result = mysqli_query($con,$sql);
	if (!$result) {
		$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}
	
	$rows = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows[] = $row;
    }
	
    header('Content-Type: application/json');
	echo json_encode($rows);
	
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
	header('Content-Type: application/json');
	echo json_encode($rows);
}

function getPieceDuration($pieceId)
{
	// get distinct pieces per user, with total viewings count
	$con=mysqli_connect("localhost","root","","MusicProjectDB");
	if (!$con) {
	  die('Could not connect: ' . mysqli_error($con));
	}

	$sql="SELECT `piece`.`PieceLength`, `piece`.`YoutubeId`" .
			" FROM `musicprojectdb`.`piece`" .
			" WHERE `piece`.`PieceId` = ".$pieceId;
	$result = mysqli_query($con,$sql);
	if (!$result) {
		$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}
	
	$rows = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $rows[] = $row;
    }
	header('Content-Type: application/json');
	echo json_encode($rows);
}