<?php
    ini_set('display_Errors', 1);
    ini_set('display_startup_Errors', 1);
    Error_reporting(E_ALL);

    $inData = getRequestInfo();

    $contactID = $inData["ContactID"];
    $userID = $inData["UserID"];

    // Prevent empty input
    if(empty(trim($userID))|| empty(trim($contactID)))
    {
        returnWithError("All fields must me filled.");
        exit;
    }

    // Prevent non-numeric or negative input.
    else if (!is_numeric($userID) || !is_numeric($contactID) ||
            $userID <= 0 || $contactID <= 0)
    {
        returnWithError("All fields must me filled.");
        exit;
    }


    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_Error) 
    {
        returnWithError( $conn->connect_Error );
    } 
    else
    {
        // The secure SQL statement checking BOTH the ContactID and UserID
        $stmt = $conn->prepare("DELETE FROM Contacts WHERE ContactID = ? AND UserID = ?");
        
        // Bind the variables
        $stmt->bind_param("ii", $contactID, $userID);
        
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
        $retValue = '{"Error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
?>