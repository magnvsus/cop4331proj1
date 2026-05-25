<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Decode the JSON payload from the frontend
    $inData = getRequestInfo();

    // Store all 6 pieces of data into PHP variables
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $phone = $inData["phone"];
    $email = $inData["email"];
    $userId = $inData["userId"];
    $contactId = $inData["contactId"];

    // Open the database vault
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error) 
    {
        returnWithError( $conn->connect_error );
    } 
    else
    {
        // The Secure SQL Statement
        // Use ? as placeholders to prevent SQL Injection. 
        // Include 'AND UserID = ?' so a hacker can't update someone else's contact.
        $stmt = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName = ?, Phone = ?, Email = ? WHERE ContactID = ? AND UserID = ?");
        
        // Bind the variables to the ? placeholders
        // "ssssii" tells MySQL the exact data types we are sending. 
        // s = String (First, Last, Phone, Email)
        // i = Integer (ContactID, UserID)
        $stmt->bind_param("ssssii", $firstName, $lastName, $phone, $email, $contactId, $userId);
        
        // Execute the update
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