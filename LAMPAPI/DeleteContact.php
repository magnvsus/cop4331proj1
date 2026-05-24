<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $inData = getRequestInfo();

    $contactId = $inData["contactId"];
    $userId = $inData["userId"];

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error) 
    {
        returnWithError( $conn->connect_error );
    } 
    else
    {
        // The secure SQL statement checking BOTH the ContactID and UserID
        $stmt = $conn->prepare("DELETE FROM Contacts WHERE ContactID = ? AND UserID = ?");
        
        // Bind the variables
        $stmt->bind_param("ii", $contactId, $userId);
        
        // Execute the hit
        $stmt->execute();

        // Close the vault
        $stmt->close();
        $conn->close();
        
        // Return a clean success message
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