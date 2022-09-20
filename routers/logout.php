<?php
function route($method, $urlList, $requestData)
{
    global $Link;
    if ($method === 'POST' && count($urlList) === 0) {

        $authHeader = apache_request_headers()['Authorization'];
        $token = explode(' ', $authHeader)[1];


        $query = "SELECT Nickname FROM authorizedusers WHERE token = ?";
        $stmt = $Link->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($output);
        $stmt->fetch();

        if($output){
            $query = "DELETE FROM authorizedusers WHERE token = ?";
            $stmt = $Link->prepare($query);
            $stmt->bind_param("s", $token);
            $stmt->execute();
        }
    }
}
