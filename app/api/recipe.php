<?php
//Fetch all recipes WIP
$app->get('/api/recipe', function() {
    require_once('dbconnect.php');
    
    $query = "SELECT `id`, `username`, `name`, `surname`, `date_of_birth`, `address`, `city`, `postalcode`, `phone` FROM `recipe` order by `id`";
    $result = $mysqli->query($query);

    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }

    if(isset($data)){
        header('Content-Type: application/json');
        echo json_encode($data);
    }    
});

$app->post('/api/recipe', function() {
    require_once('dbconnect.php');
    
    $name = $request->getParsedBody()['name'];
    $description = $request->getParsedBody()['description'];
    $user_id = $request->getParsedBody()['user_id'];
    
    $query = "INSERT INTO `recipe` (`user_id`, `recipe_name`, `recipe_description`) VALUES (?,?,?)";
    $stmt = $mysqli->prepare($query);    
    $stmt->bind_param("sss", $user_id, $name, $description, $surname);

    $stmt->execute();
});