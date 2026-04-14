<?php
session_start();

function saveScore($user,$score){
    $file="leaderboard.json";
    if(!file_exists($file)) file_put_contents($file,json_encode([]));
    $data=json_decode(file_get_contents($file),true);

    $data[]=["user"=>$user,"score"=>$score,"status"=>"cashed_out"];
    usort($data,fn($a,$b)=>$b['score']-$a['score']);

    file_put_contents($file,json_encode($data,JSON_PRETTY_PRINT));
}

saveScore($_SESSION['user'], $_SESSION['score']);

session_destroy();

header("Location: leaderboard.php");
exit();
?>