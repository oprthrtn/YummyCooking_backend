<?php
function checkAuthor($currentNickname, $id, $Link)
{
    $query = "SELECT AuthorNickname FROM recipes WHERE ID = ?";
    $stmt = $Link->prepare($query);
    $stmt->bind_param("i", intval($id));
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($AuthorNickname);
    $stmt->fetch();

    if ($currentNickname === $AuthorNickname) {
        return true;
    }

    return false;
}
