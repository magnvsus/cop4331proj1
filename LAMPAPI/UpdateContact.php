<?php
    ini_set('display_Errors', 1);
    ini_set('display_startup_Errors', 1);
    Error_reporting(E_ALL);

    // Decode the JSON payload from the frontend
    $inData = getRequestInfo();

    // Store all 6 pieces of data into PHP variables
    $firstName = $inData["FirstName"];
    $lastName = $inData["LastName"];
    $phone = $inData["Phone"];
    $email = $inData["Email"];
    $userID = $inData["UserID"];
    $contactID = $inData["ContactID"];

    // Open the database vault
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_Error) 
    {
        returnWithError( $conn->connect_Error );
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
        $stmt->bind_param("ssssii", $firstName, $lastName, $phone, $email, $contactID, $userID);
        
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
        $retValue = '{"Error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
?>