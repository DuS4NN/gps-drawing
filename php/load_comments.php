<?php

    session_start();
    require '../config/db.php';
    require '../config/lang.php';

    if(!isset($_POST['id']) || empty($_POST['id']) || !isset($_POST['num']) || !isset($_SESSION['id']) || empty($_SESSION['id'])){
        return;
    }

    $id = $_POST['id'];
    $num = $_POST['num'];

    $stmt = $db->prepare("SELECT comments.id as 'comid', comments.id_user, comments.id_post, comments.comment, comments.time, users.nick_name, users.profile_picture, 
CASE WHEN EXISTS (SELECT * FROM comments WHERE comments.id_user = ? AND comments.id = comid) 
                    THEN '1' 
                    ELSE '0'
                    END AS 'commented'
                    FROM comments 
                    INNER JOIN users ON users.id = comments.id_user 
                    WHERE comments.id_post = ? 
                    AND  comments.id NOT IN (SELECT id_comment FROM blocked_comments WHERE blocked_comments.id_user = ?)
                    ORDER BY comments.time 
                    DESC LIMIT ?");
    $stmt->bind_param("iiii", $_SESSION['id'], $id, $_SESSION['id'],$num);
    $stmt->execute();

    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        include("../html/comment.php");
    }

    $stmt = $db->prepare("SELECT COUNT(*) as 'count' FROM comments WHERE comments.id_post = ? ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()){
        if($row['count']<=$num){
            echo '<script>
                    var x = document.getElementById("loadmore-'.$num.'-'.$id.'");
                   
                    x.style.display = "none";
                  </script>
';
        }
    }