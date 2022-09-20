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

        if ($output) {

            $title = $requestData->body->title;
            $description = $requestData->body->description;
            $cookTime = $requestData->body->cookTime;
            $direction = $requestData->body->direction;
            $imageURL = $requestData->body->imageURL;
            $authorNickname = $output;

            $query = "INSERT INTO `recipes`(`Title`, `Description`, `CookTime`, `Direction`, `AuthorNickname`, `ImageURL`) VALUES (?,?,?,?,?,?)";

            $stmt = $Link->prepare($query);
            $stmt->bind_param("ssisss", $title, $description, $cookTime, $direction, $authorNickname, $imageURL);
            $stmt->execute();

        } else {
            setHTTPStatus("403", 'Вы не авторизованы.');
        }
    }
}
