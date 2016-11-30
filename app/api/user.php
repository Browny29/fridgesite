<?php
//Fetch all users
$app->get('/api/user', function() {
    require_once('dbconnect.php');
    
    $query = "SELECT `id`, `username`, `name`, `surname`, `date_of_birth`, `address`, `city`, `postalcode`, `phone` FROM `user` order by `id`";
    $result = $mysqli->query($query);

    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }

    if(isset($data)){
        header('Content-Type: application/json');
        echo json_encode($data);
    }    
});

//Fetch a single user
$app->get('/api/user/{id}', function($request) {
    require_once('dbconnect.php');
    $id = $request->getAttribute('id');

    $query = "SELECT `id`, `username`, `name`, `surname`, `date_of_birth`, `address`, `city`, `postalcode`, `phone` FROM `user` WHERE id=".$id;
    $result = $mysqli->query($query);

    $data[] =  $result->fetch_assoc();
    
    if(isset($data)){
        header('Content-Type: application/json');
        echo json_encode($data);
    }    
});

//Post data and create a new user
$app->post('/api/user', function($request) {

    require_once('dbconnect.php');
    
    $username = $request->getParsedBody()['username'];
    $password = $request->getParsedBody()['password'];
    $name = $request->getParsedBody()['name'];
    $surname = $request->getParsedBody()['surname'];
    $dateOfBirth = $request->getParsedBody()['birthday'];
    $address = $request->getParsedBody()['address'];
    $city = $request->getParsedBody()['city'];
    $postalcode = $request->getParsedBody()['postalcode'];
    $phone = $request->getParsedBody()['phone'];
    
    $password = hash("sha256", $password);
    
    $query = "INSERT INTO `user` (`username`, `password`, `name`, `surname`, `date_of_birth`, `address`, `city`, `postalcode`, `phone`) VALUES (?,?,?,?,?,?,?,?,?)";
    $stmt = $mysqli->prepare($query);    
    $stmt->bind_param("sssssssss", $username, $password, $name, $surname, $dateOfBirth, $address, $city, $postalcode, $phone);

    $stmt->execute();
});

//Put Data and update a user
$app->put('/api/user/{id}', function($request) {
    require_once('dbconnect.php');

    $id = $request->getAttribute('id');
    $password = $request->getParsedBody()['password'];
    $name = $request->getParsedBody()['name'];
    $surname = $request->getParsedBody()['surname'];
    $dateOfBirth = $request->getParsedBody()['birthday'];
    $address = $request->getParsedBody()['address'];
    $city = $request->getParsedBody()['city'];
    $postalcode = $request->getParsedBody()['postalcode'];
    $phone = $request->getParsedBody()['phone'];
    
    $password = hash("sha256", $password);

    $query = "UPDATE `user` SET `password` = ?, `name` = ?, `surname` = ?, `date_of_birth` = ?, `address` = ?, `city` = ?, `postalcode` = ?, `phone` = ? WHERE `user`.`id` = $id";
    $stmt = $mysqli->prepare($query);    
    $stmt->bind_param("ssssssss", $password, $name, $surname, $dateOfBirth, $address, $city, $postalcode, $phone);
    
    $stmt->execute();    
});
