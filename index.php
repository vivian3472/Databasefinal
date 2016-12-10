<?php 


$loader = new \Phalcon\Loader();
$loader->registerDirs(array(
  __DIR__.'/models/'
))->register();
//connect database
$di = new \Phalcon\DI\FactoryDefault();
$di->set('db',function(){
  return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
    'host'  => 'localhost',
    'username'=> 'root',
    'password'=> 'root',
    'dbname' => 'final'
  ));
});
//echo "success";



$app = new \Phalcon\Mvc\Micro($di);


//echo $app->getBaseUri();
//$app->setBaseUri("/api/");
//echo "s2";
//GET user_error
$app->get('/api/user',function() use($app){
  $phql = "SELECT * FROM User";
  $users = $app->modelsManager->executeQuery($phql);
  $data = array();
  foreach($users as $user){
    $data[] = array(
    'UserId' => $user->UserId,
    'Age' => $user->Age,
    'Image' => $user->Image,
    'Email' => $user->Email,
    'Password' => $user->Password
    );
  }
  echo json_encode($data);
});



//GET User by Primary Key
$app->get('/api/user/{UserId}',function($UserId) use ($app){

  $phql = "SELECT * FROM User WHERE UserId LIKE :UserId: ORDER BY UserId";
  $users = $app->modelsManager->executeQuery($phql,array(
    'UserId' => '%'. $UserId .'%'
  ));

  $data = array();
  foreach($users as $user){
    $data[] = array(
    'UserId' => $user->UserId,
    'Age' => $user->Age,
    'Image' => $user->Image,
    'Email' => $user->Email,
    'Password' => $user->Password
   );
  }
  echo json_encode($data);

});

//POST User
$app->post('/api/user',function() use ($app){
  $user = $app->request->getJsonRawBody();

  $phql = "INSERT INTO User (UserId, Age, Image, Email, Password) VALUES (:UserId:, :Age:, :Image:, :Email:, :Password:)";
  $status = $app->modelsManager->executeQuery($phql,array(
    'UserId' => $user->UserId,
    'Age' => $user->Age,
    'Image' => $user->Image,
    'Email' => $user->Email,
    'Password' => $user->Password
  ));

  $response = new Phalcon\Http\Response();
  if($status->success() == true){
    $response->setStatusCode(201,'Create New User');
    $user->UserId = $status->getModel()->UserId;

    $response->setJsonContent(array('status'=>'ok','data'=>$user));
  }else{
    $response->setStatusCode(409,'Conflict');

    $errors = array();
    foreach($status->getMessages() as $message){
      $errors[] = $message->getMessage();
    }
    $response->setJsonContent(array('status'=>'ERROR','data'=>$errors));
  }
  return $response;

});


//echo json_encode('load');
//Update User
$app->put('/api/user/{UserId}',function($UserId) use ($app){
  $user = $app->request->getJsonRawBody();
  $phql = "UPDATE User SET Age = :Age:, Image = :Image:, Email = :Email:, Password = :Password: WHERE UserId = :UserId:";
  $status = $app->modelsManager->executeQuery($phql,array(
    'UserId' => $UserId,
    'Age' => $user->Age,
    'Image' => $user->Image,
    'Email' => $user->Email,
    'Password' => $user->Password
  ));

    $response = new \Phalcon\Http\Response();

    // Check if the insertion was successful
    if ($status->success() == true) {
        $response->setJsonContent(
            array(
                'status' => 'OK',
                'data' => $user
            )
        );
    } else {

        // Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response->setJsonContent(
            array(
                'status'   => 'ERROR',
                'messages' => $errors
            )
        );
    }

    return $response;

});

//DELETE
$app->delete('/api/users/{UserId}',function($UserId) use ($app){

  $phql = "DELETE FROM User WHERE UserId = :UserId: ";
  $status = $app->modelsManager->executeQuery($phql,array(
      'UserId' => $UserId
    ));
  $response = new \Phalcon\Http\Response();
  if($status->success() == true){
    $response = array('status'=>'OK');
  }else{
    $this->response->setStatusCode(500,'Internal Error')->setHeaders();

    $errors = array();
    foreach($status->getMessages() as $message){
      $errors[] = $message->getMessage();
    }
    $response = array('status'=>'Error','data'=>$errors);
  }
    return $response;
});




$app->handle();
