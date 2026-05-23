<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // 1. Read the JSON from Postman
    $inData = getRequestInfo();

    // 2. Extract the data into distinct variables to prevent null errors
    $userId = $inData["userId"];
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $phone = $inData["phone"];
    $email = $inData["email"];

    // 3. Connect to Michael's database
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");

    if ($conn->connect_error) 
    {
        returnWithError( $conn->connect_error );
    } 
    else 
    {
        // 4. Insert into the Contacts table
        $stmt = $conn->prepare("INSERT into Contacts (UserID, FirstName, LastName, Phone, Email) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $firstName, $lastName, $phone, $email);
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