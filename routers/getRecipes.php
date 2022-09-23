<?php
function route($method, $urlList, $requestData)
{
    global $Link;
    if ($method === 'GET' && count($urlList) === 0) {
        $query = "SELECT * FROM recipes";
        $result = mysqli_query($Link, $query);
        $result = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
    }
}
