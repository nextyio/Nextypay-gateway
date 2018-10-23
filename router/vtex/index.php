<?php 
    foreach ($_GET as $key => $value) {
        echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>"; //TEST
    }
    $request = $_GET['request'];
    $arr = explode("/vtex/", $request);
    $request = $arr[1];
    echo "<br>$request";
?>