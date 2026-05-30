<?php
    ini_set('display_Errors', 1);
    ini_set('display_startup_Errors', 1);
    Error_reporting(E_ALL);

    // 1. Read the JSON from Postman
    $inData = getRequestInfo();

    // 2. Extract the data into distinct variables to prevent null Errors
    $userID = $inData["UserID"];
    $firstName = $inData["FirstName"];
    $lastName = $inData["LastName"];
    $phone = $inData["Phone"];
    $email = $inData["Email"];

    // 3. Prevent empty inputs
    if(empty(trim($firstName))|| empty(trim($lastName))||
        empty(trim($phone))|| empty(trim($email)))
    {
        returnWithError("All fields must be filled.");
        exit;
    }

    // 4. Connect to Michael's database
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");

    if ($conn->connect_Error) 
    {
        returnWithError( $conn->connect_Error );
    } 
    else 
    {
        // 5. Insert into the Contacts table
        $stmt = $conn->prepare("INSERT into Contacts (UserID, FirstName, LastName, Phone, Email) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userID, $firstName, $lastName, $phone, $email);
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
        $retValue = '{"Error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
?>