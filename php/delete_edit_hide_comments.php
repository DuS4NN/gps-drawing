<?php

    session_start();
    require '../config/db.php';

    if(!isset($_POST['idPost']) || empty($_POST['idPost']) || !isset($_POST['idComment']) || empty($_POST['idComment']) || !isset($_POST['action']) || !isset($_SESSION['id']) || empty($_SESSION['id'])){
        echo $_POST['idComment']." ".$_POST['idPost']." ".$_POST['action'];
        return;
    }

    $id = $_POST['idComment'];
    $post = $_POST['idPost'];
    $action = $_POST['action'];

    if($action==0){//Delete
        $stmt = $db->prepare("DELETE FROM comments WHERE id = ? AND id_user = ? AND id_post = ?");
        $stmt->bind_param("iii", $id, $_SESSION['id'], $post);
        $stmt->execute();

    }else if($action==1){//Edit

    }else if($action==2){//Hide
        $stmt = $db->prepare("INSERT INTO `blocked_comments` (`id`, `id_user`, `id_comment`) VALUES (NULL, ?, ?)");
        $stmt->bind_param("ii", $_SESSION['id'],$id);
        $stmt->execute();
    }