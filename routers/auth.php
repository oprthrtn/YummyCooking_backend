<?php
function route($method, $urlList, $requestData)
{
    global $Link;
    if ($method === 'POST' && count($urlList) === 0) {

        $nickname = $requestData->body->nickname;
        $password = $requestData->body->password;

        if (strlen($nickname) >= 30) {
            setHTTPStatus(400, 'Никнейм должен быть меньше 30-ти символов');
            return;
        }

        if (strlen($nickname) === 0 || strlen($password) === 0) {
            setHTTPStatus(400, 'Никнейм или пароль не могут быть пустыми');
            return;
        }

        $password = bin2hex($password);


        $query = "SELECT Nickname FROM users WHERE `Nickname` = ? AND `Password` = ?";
        $stmt = $Link->prepare($query);
        $stmt->bind_param("ss", $nickname, $password);
        $stmt->execute();

        $stmt->store_result();
        $stmt->bind_result($output);
        $stmt->fetch();


        if (strlen($output) === 0) {
            setHTTPStatus(400, 'Введён неверный логин или пароль');
            return;
        }

        $token = openssl_random_pseudo_bytes(16);
        $token = bin2hex($token);

        $query = "INSERT INTO authorizedusers (`Nickname`, `token`) VALUES (?,?)";

        $stmt = $Link->prepare($query);
        $stmt->bind_param("ss", $nickname, $token);
        $stmt->execute();


        setHTTPStatus("200", $token);
    }
}
