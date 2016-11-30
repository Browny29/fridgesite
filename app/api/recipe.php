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

//Post data to add a new recipe
$app->post('/api/recipe', function($request) {
    $name = $request->getParsedBody()['name'];
    $description = $request->getParsedBody()['description'];
    $user_id = $request->getParsedBody()['user_id'];
    $steps = $request->getParsedBody()['steps'];
    $ingredients = $request->getParsedBody()['ingredients'];

    $steps = json_decode($steps);
    $ingredients = json_decode($ingredients);

    InsertRecipe($name, $description, $user_id, $steps, $ingredients);
});

//Insert a new recipe into the database 
function InsertRecipe($name, $description, $user_id, $steps, $ingredients) {
    require_once('dbconnect.php');

    //Insert the recipe information
    $query = "INSERT INTO `recipe` (`user_id`, `recipe_name`, `recipe_description`) VALUES (?,?,?); ";
    $stmt = $mysqli->prepare($query);    
    $stmt->bind_param("sss", $user_id, $name, $description);

    $stmt->execute();
   
    $recipe_id = $mysqli->insert_id;

    //Insert all the recipe steps
    $query = "INSERT INTO `recipe_step` (`recipe_id`, `step_number`, `step_description`) VALUES (?,?,?)";
    $stmt = $mysqli->prepare($query);
    for($i = 1; $i <= sizeof($steps); $i++){
        $step = (string)$steps[$i-1];        
        $stmt->bind_param("iis", $recipe_id, $i, $step);
                    
        $stmt->execute();
    }

    //Insert all the recipe ingredients
    $query = "INSERT INTO `ingredient` (`name`, `unit`) VALUES (?,?)";
    $stmtNewIngredient = $mysqli->prepare($query);
    $query = "INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `amount`) VALUES (?,?,?)";
    $stmtIngredient = $mysqli->prepare($query);
    for($i = 0; $i < sizeof($ingredients); $i++){
        $name = strtolower($ingredients[$i]->name);
        $unit = strtolower($ingredients[$i]->unit);
        $amount = (int)$ingredients[$i]->amount;

        $query = "SELECT id FROM `ingredient` WHERE name = '$name' AND unit = '$unit'";
        $result = $mysqli->query($query);
        $data = $result->fetch_assoc();
        print_r($data);
        $ingredientId = null;

        if(sizeof($data) == 0){       
            $stmtNewIngredient->bind_param("ss", $name, $unit);      
            $stmtNewIngredient->execute();

            $ingredientId = $mysqli->insert_id;
        }
        else{
            $ingredientId = $data['id'];    
        }
        $stmtIngredient->bind_param("iii", $recipe_id, $ingredientId, $amount);      
        $stmtIngredient->execute();
    }
};