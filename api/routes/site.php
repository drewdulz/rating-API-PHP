<?php
ini_set('display_errors','on');

$app->get('/hello/:name', function ($name) {
	echo "Hello, $name";
});

$app->get('/fetch/userdata',function() use($app) {
	$req = $app->request();
	$requiredfields = array(
		'id'
	);
	
	// validate required fields
	if(!RequiredFields($req->get(), $requiredfields)){
		return false;
	}
	$id = $req->get("id");
	global $conn;
	$sql='SELECT * from users where id='.$id;
	$rs=$conn->query($sql);
	$arr = $rs->fetch_all(MYSQLI_ASSOC);
	
	echo json_encode(array(
		"error" => 0,
		"message" => "User data fetch successfully",
		"users" => $arr
	));
});

$app->get('/login',function() use($app) {
	$req = $app->request();
	$requiredfields = array(
		'email',
		'password'
	);
	
	// validate required fields
	if(!RequiredFields($req->get(), $requiredfields)){
		return false;
	}
	$email = $req->get("email");
	$password = $req->get("password");
	global $conn;
	$sql='SELECT * from users where EmailAddress="'.$email.'" and Password="'.$password.'"';
	$rs=$conn->query($sql);
	$arr = $rs->fetch_array(MYSQLI_ASSOC);
	if($arr == null){
		echo json_encode(array(
			"error" => 1,
			"message" => "Email-id or Password doesn't exist",
		));
		return;
	}
	
	echo json_encode(array(
		"error" => 0,
		"message" => "User logged in successfully",
		"users" => $arr
	));
});


/*
Get the rating of a specified item by passing in the ID.
URL/api/rating/#


*/
$app->get('/rating/:id',function($item_id) use($app) {
	$req = $app->request();
	
	global $conn;
	$sql='SELECT * from items where item_id='.$item_id;
	$rs=$conn->query($sql);
	$arr = $rs->fetch_array(MYSQLI_ASSOC);
	if($arr == null){
		echo json_encode(array(
			"error" => 1,
			"message" => "That item doesn't exist",
		));
		return;
	}
	
	echo json_encode(array(
		"error" => 0,
		"message" => "Item retrieved successfully",
		"item" => $arr
	));
});

/*
Post a new item to the database

*/ 
$app->post('/rating', function() use($app) {
	
	// reading post params
	$name = $app->request()->post('name');
	$rating = $app->request()->post('rating');

	// check for values 
	if( $name == null || $name == "" ) {
		echo json_encode(array(
			"error" => 1,
			"message" => "Please submit a name"
		));
		return 0;
	} else if($rating == null || $rating == "" ) {
		echo json_encode(array(
			"error" => 1,
			"message" => "Please submit a rating"
		));
		return 0;
	}

	// insert query
	global $conn;
	$sql='INSERT INTO items (name, num_ratings, rating) VALUES ("'.$name.'", 1, "'.$rating.'")';

	if ($conn->query($sql) === TRUE) {
			echo json_encode(array(
				"error" => 0,
				"message" => "Item posted successfully",
				"id" => $conn->insert_id
			));
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();

});

/*
Update an item in the database
*/ 
$app->post('/rating/:id', function($item_id) use($app) {
	
	// just get the new rating
	$rating = $app->request()->post('rating');

	// check for values 
	if( $rating == null || $rating == 0 ) {
		echo json_encode(array(
			"error" => 1,
			"message" => "Please submit a rating"
		));
		return 0;
	}

	// update query
	global $conn;
	// add to the number of ratings count and add to the total rating
	$sql='UPDATE items SET rating= rating + "'.$rating.'", num_ratings = num_ratings + 1 WHERE item_id="'.$item_id.'"';

	if ($conn->query($sql) === TRUE) {
			echo json_encode(array(
				"error" => 0,
				"message" => "Item posted successfully",
				"id" => $conn->insert_id
			));
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();

});


?>
