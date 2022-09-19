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

        $query = "SELECT Nickname FROM users WHERE Nickname = ?";
        $stmt = $Link->prepare($query);
        $stmt->bind_param("s", $nickname);
        $stmt->execute();

        $stmt->bind_result($output);
        $stmt->fetch();


        if (strlen($output) != 0) {
            setHTTPStatus(400, 'Пользователь с таким никнеймом уже существует');
            return;
        }


        $query = "INSERT INTO users (`Nickname`, `Password`) VALUES (?,?)";

        $stmt = $Link->prepare($query);
        $stmt->bind_param("ss", $nickname, password_hash($password, PASSWORD_DEFAULT));
        $stmt->execute();

        setHTTPStatus("200", 'Done');
    }
}
