<?php
    ini_set('display_Errors', 1);
    ini_set('display_startup_Errors', 1);
    Error_reporting(E_ALL);

    $inData = getRequestInfo();
    
    $searchResults = "";
    $searchCount = 0;

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_Error) 
    {
        returnWithError( $conn->connect_Error );
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
        
        // 2. Check for empty search
        // If search query is empty, %% will match everything.
        // Refuse empty searches
        if(empty(trim($inData["Search"]))){
            returnWithError("Search term cannot be empty");
            exit;
        }

        // 3. Set up the wildcards
        $searchQuery = "%" . $inData["Search"] . "%";    
               
        
        // 4. Pull the userID
        $userID = $inData["UserID"];
        
        // 5. Proper MySQLi binding (4 Strings, 1 Integer = "ssssi")
        $stmt->bind_param("ssssi", $searchQuery, $searchQuery, $searchQuery, $searchQuery, $userID);
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
        // Cleaned up the Error so it returns an empty array to the frontend
        $retValue = '{"Results":[],"Error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
    
    function returnWithInfo( $searchResults )
    {
        $retValue = '{"Results":[' . $searchResults . '],"Error":""}';
        sendResultInfoAsJson( $retValue );
    }
?>
