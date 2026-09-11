<?php
	require_once __DIR__ . '/common.php';

	$inData = getRequestInfo();

	$token = isset($inData["token"]) ? $inData["token"] : "";
	$expiresAt = isset($inData["expiresAt"]) ? $inData["expiresAt"] : "";

	if( !verifySessionToken( $token, $expiresAt ) )
	{
		http_response_code(401);
		returnWithError( "Invalid or expired session token" );
		exit();
	}

	$color = $inData["color"];
	$userId = $inData["userId"];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("INSERT into Colors (UserId,Name) VALUES(?,?)");
		$stmt->bind_param("ss", $userId, $color);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithError("");
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>