<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $inData = getRequestInfo();
    
    $searchResults = "";
    $searchCount = 0;

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error) 
    {
        returnWithError( $conn->connect_error );
    } 
    else
    {
        // 1. Select ALL columns (*) so the frontend gets the full contact info
        $sql = "SELECT * FROM Contacts WHERE (FirstName LIKE ? 
                            OR LastName LIKE ? 
                            OR Phone LIKE ? 
                            OR Email LIKE ?) 
                            AND UserID = ?";
                            
        $stmt = $conn->prepare($sql);
        
        // 2. Set up the wildcards
        $searchQuery = "%" . $inData["search"] . "%";        
        
        // 3. Pull the userId
        $userId = $inData["userId"];
        
        // 4. Proper MySQLi binding (4 Strings, 1 Integer = "ssssi")
        $stmt->bind_param("ssssi", $searchQuery, $searchQuery, $searchQuery, $searchQuery, $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc())
        {
            if( $searchCount > 0 )
            {
                $searchResults .= ",";
            }
            $searchCount++;
            
            // 5. Package the data as a complete JSON object, using ContactID
            $searchResults .= '{"ContactID":' . $row["ContactID"] . ',"FirstName":"' . $row["FirstName"] . '","LastName":"' . $row["LastName"] . '","Phone":"' . $row["Phone"] . '","Email":"' . $row["Email"] . '"}';
        }
        
        if( $searchCount == 0 )
        {
            returnWithError( "No Records Found" );
        }
        else
        {
            returnWithInfo( $searchResults );
        }
        
        $stmt->close();
        $conn->close();
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
        // Cleaned up the error so it returns an empty array to the frontend
        $retValue = '{"results":[],"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
    
    function returnWithInfo( $searchResults )
    {
        $retValue = '{"results":[' . $searchResults . '],"error":""}';
        sendResultInfoAsJson( $retValue );
    }
?>
