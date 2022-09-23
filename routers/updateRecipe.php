<?php

function route($method, $urlList, $requestData)
{
    global $Link;
    if ($method === 'PATCH' && count($urlList) === 1 && gettype($urlList[0] === "integer")) {
        $authHeader = apache_request_headers()['Authorization'];
        $token = explode(' ', $authHeader)[1];


        $query = "SELECT Nickname FROM authorizedusers WHERE token = ?";
        $stmt = $Link->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($currentNickname);
        $stmt->fetch();

        if ($currentNickname) {

            $title = $requestData->body->title;
            $description = $requestData->body->description;
            $cookTime = $requestData->body->cookTime;
            $direction = $requestData->body->direction;
            $imageURL = $requestData->body->imageURL;

            $query = "SELECT RoleName FROM users WHERE Nickname = ?";
            $stmt = $Link->prepare($query);
            $stmt->bind_param("s", $currentNickname);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($currentRole);
            $stmt->fetch();

            if ($currentRole === "admin") {
                $query = "UPDATE `recipes` SET `Title`=?,`Description`=?,`CookTime`=?,`Direction`=?,`ImageURL`=? WHERE `ID`=?";

                $stmt = $Link->prepare($query);
                $stmt->bind_param("ssisss", $title, $description, $cookTime, $direction, $imageURL, $urlList[0]);
                $stmt->execute();
            } else if ($currentRole === "user" && checkAuthor($currentNickname, $urlList[0],  $Link)) {
                $query = "UPDATE `recipes` SET `Title`=?,`Description`=?,`CookTime`=?,`Direction`=?,`ImageURL`=? WHERE `ID`=?";

                $stmt = $Link->prepare($query);
                $stmt->bind_param("ssisss", $title, $description, $cookTime, $direction, $imageURL, $urlList[0]);
                $stmt->execute();
            } else {
                setHTTPStatus("403", 'Вы можете обновить только свои рецепты');
                return;
            }
        } else {
            setHTTPStatus("403", 'Вы не авторизованы.');
        }
    }
}
