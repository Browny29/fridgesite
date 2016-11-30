<?php
$app->get('/api/recipe', function() {
    require_once('dbconnect.php');
    echo "welcome to recipe"; 
});