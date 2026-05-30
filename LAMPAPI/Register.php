<?php
    ini_set('display_Errors', 1);
    ini_set('display_startup_Errors', 1);
    Error_reporting(E_ALL);

    $inData = getRequestInfo();

    # Output JSON error
    #if (json_last_error() !== JSON_ERROR_NONE) {
    #echo "JSON Error: " . json_last_error_msg();
    #}

    $firstName = $inData["FirstName"];
    $lastName  = $inData["LastName"];
    $login     = $inData["Login"];
    $password  = $inData["Password"];

    // 1. Check for empty entries
    if(empty(trim($firstName))|| empty(trim($lastName))||
        empty(trim($login))|| empty(trim($password)))
    {
        http_response_code(400);
        returnWithError("All fields must be filled.");		
		exit;
    }

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");

    if ($conn->connect_Error)
    {
        http_response_code(500);
        returnWithError($conn->connect_Error);
    }
    else
    {
		
        // 2. Check for existing account
        $existCheck = $conn->prepare("SELECT * FROM Users WHERE Login = ?");
        $existCheck->bind_param("s", $login);
        $existCheck->execute();
        $existCheck->store_result();

        if ($existCheck->num_rows > 0)
        {
            $existCheck->close();
            $conn->close();
            http_response_code(400);
            returnWithError("Username already exists");
        }
        else
        {
            $existCheck->close();

            // 3. Insert the new user into the Users table
            $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $firstName, $lastName, $login, $password);
            //$stmt->execute();

			// 4. Check for database changes.
			if($stmt->affected_rows === 0)
			{
                http_response_code(400);
				returnWithError("Registration failed");
				$stmt->close();
            	$conn->close();
				exit;
			}

            $stmt->close();
            $conn->close();

            http_response_code(201);
            returnWithInfo();
        }
    }

    function getRequestInfo()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    function sendResultInfoAsJson($obj)
    {
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError($err)
    {
        $retValue = '{"UserID":0,"Error":"' . $err . '"}';
        sendResultInfoAsJson($retValue);
    }

    function returnWithInfo()
    {
        $retValue = '{"Error":""}';
        sendResultInfoAsJson($retValue);
    }
?>