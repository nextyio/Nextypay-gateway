<?php 
    foreach ($_GET as $key => $value) {
        echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>"; //TEST
    }
?>