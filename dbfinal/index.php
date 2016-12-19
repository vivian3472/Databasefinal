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
    'dbname' => 'dbfinal'
  ));
});
// echo "success";



$app = new \Phalcon\Mvc\Micro($di);


//echo $app->getBaseUri();
//$app->setBaseUri("/api/");
//echo "s2";
//GET user_error
$app->get('/api/user',function() use($app){
  // echo $app;
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
    // 'UserId' => '%'. $UserId .'%'
    'UserId' => $UserId
  ));
  // echo $users[0];

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
  echo json_encode($status);
  // $status = $app->modelsManager->executeQuery($phql);
  // echo"2";


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

//GET Post
$app->get('/api/news',function() use($app){

  $phql = "SELECT PostId, n.UserId, Text, n.Image as nImage, PostTime, Longitude, Latitude, Setting, LikeNum, UnlikeNum, u.Image as uImage FROM News as n left join User as u on n.UserId=u.UserId";
  $news = $app->modelsManager->executeQuery($phql);
  $data = array();
  foreach($news as $new){
    $data[] = array(
    'PostId' => $new->PostId,
    'UserId' => $new->UserId,
    'Text' => $new->Text,
    'nImage' => $new->nImage,
    'PostTime' => $new->PostTime,
    'Longitude' => $new->Longitude,
    'Latitude' => $new->Latitude,
    'Setting' => $new->Setting,
    'LikeNum' => $new->LikeNum,
    'UnlikeNum' => $new->UnlikeNum,
    'uImage' => $new->uImage
    );
  }
  echo json_encode($data);
});

//GET Post and comments
$app->get('/api/allnews',function() use($app){

  $phql = "SELECT n.PostId, UserId, Text, Image, PostTime, Longitude, Latitude, Setting, LikeNum, Author, SendTime, Content FROM News as n left join Comments as c on n.PostId=c.PostId";
  $news = $app->modelsManager->executeQuery($phql);
  echo json_encode($news);
  $data = array();
  foreach($news as $new){
    $data[] = array(
    'PostId' => $new->PostId,
    'UserId' => $new->UserId,
    'Text' => $new->Text,
    'Image' => $new->Image,
    'PostTime' => $new->PostTime,
    'Longitude' => $new->Longitude,
    'Latitude' => $new->Latitude,
    'Setting' => $new->Setting,
    'LikeNum' => $new->LikeNum,
    'Author' => $new->Author,
    'SendTime' => $new->SendTime,
    'Content' => $new->Content
    );
  }
  echo json_encode($data);
});

//search User's Post
$app->get('/api/news/order/{UserId}',function($UserId) use ($app){

  $phql = "SELECT * FROM News WHERE UserId = :UserId: ORDER BY UserId";
  $news = $app->modelsManager->executeQuery($phql,array(
    'UserId' => $UserId 
  ));

  $data = array();
  foreach($news as $new){
    $data[] = array(
    'PostId' => $new->PostId,
    'UserId' => $new->UserId,
    'Text' => $new->Text,
    'Image' => $new->Image,
    'PostTime' => $new->PostTime,
    'Longitude' => $new->Longitude,
    'Latitude' => $new->Latitude,
    'Setting' => $new->Setting,
    'LikeNum' => $new->LikeNum
   );
  }
  echo json_encode($data);
});

//GET post by setting
$app->get('/api/public/news',function($UserId) use ($app){

//   $phql = "SELECT * FROM News WHERE UserId IN (
// SELECT Friend From Relationship WHERE UserId = :UserId:)";
 $phql = "SELECT * FROM News WHERE Setting = 'Public'";
  $news = $app->modelsManager->executeQuery($phql,array(
    'UserId' => $UserId 
  ));

  $data = array();
  foreach($news as $new){
    $data[] = array(
    'PostId' => $new->PostId,
    'UserId' => $new->UserId,
    'Text' => $new->Text,
    'Image' => $new->Image,
    'PostTime' => $new->PostTime,
    'Longitude' => $new->Longitude,
    'Latitude' => $new->Latitude,
    'Setting' => $new->Setting,
    'LikeNum' => $new->LikeNum
   );
  }
  echo json_encode($data);
});

//GET post by postID(update later)
$app->get('/api/news/{PostId}',function($PostId) use ($app){

  $phql = "SELECT * FROM News WHERE PostId = :PostId: ORDER BY PostId";
  $news = $app->modelsManager->executeQuery($phql,array(
    'PostId' => $PostId
  ));

  $data = array();
  foreach($news as $new){
    $data[] = array(
    'PostId' => $new->PostId,
    'UserId' => $new->UserId,
    'Text' => $new->Text,
    'Image' => $new->Image,
    'PostTime' => $new->PostTime,
    'Longitude' => $new->Longitude,
    'Latitude' => $new->Latitude,
    'Setting' => $new->Setting,
    'LikeNum' => $new->LikeNum,
    'UnlikeNum' => $new->UnlikeNum
   );
  }
  echo json_encode($data);
});

//Update Post
$app->put('/api/news/{PostId}',function($PostId) use ($app){
  $news = $app->request->getJsonRawBody();
  // echo "1";
  // echo json_encode($new);
  $phql = "UPDATE News SET UserId = :UserId:, Text = :Text:, Image = :Image:, PostTime = :PostTime:, Longitude = :Longitude:, Latitude = :Latitude:, Setting = :Setting:, LikeNum = :LikeNum: , UnlikeNum = :UnlikeNum:WHERE PostId = :PostId:";
  // echo json_encode($news);
  $status = $app->modelsManager->executeQuery($phql, array(
    'PostId' => $PostId,
    'UserId' => $news->UserId,
    'Text' => $news->Text,
    'Image' => $news->Image,
    'PostTime' => $news->PostTime,
    'Longitude' => $news->Longitude,
    'Latitude' => $news->Latitude,
    'Setting' => $news->Setting,
    'LikeNum' => $news->LikeNum,
    'UnlikeNum' => $news->UnlikeNum
  ));  

  // Create a response
    $response = new \Phalcon\Http\Response();

    // Check if the insertion was successful
    if ($status->success() == true) {
        $response->setJsonContent(
            array(
                'status' => 'OK',
                'data' => $news
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

//GET post likenum by postID(update later)
$app->get('/api/news/{PostId}/{likenum}',function($PostId) use ($app){

  $phql = "SELECT PostId, LikeNum FROM News WHERE PostId = :PostId: ORDER BY PostId";
  $news = $app->modelsManager->executeQuery($phql,array(
    'PostId' => $PostId
  ));

  $data = array();
  foreach($news as $new){
    $data[] = array(
    'PostId' => $new->PostId,
    'LikeNum' => $new->LikeNum
   );
  }
  echo json_encode($data);
});

//Update Likenum
$app->put('/api/news/{PostId}',function($PostId) use ($app){
// echo json_encode('success');
  $news = $app->request->getJsonRawBody();
//  echo json_encode($PostId);
  $phql = "UPDATE News SET LikeNum = :LikeNum:, UnlikeNum = :UnlikeNum: WHERE PostId = :PostId:";
  // echo json_encode()
  $status = $app->modelsManager->executeQuery($phql, array(
    'PostId' => $PostId,
    'LikeNum' => $news->LikeNum,
    'UnlikeNum' => $news->UnlikeNum
  ));  

  // Create a response
    $response = new \Phalcon\Http\Response();

    // Check if the insertion was successful
    if ($status->success() == true) {
        $response->setJsonContent(
            array(
                'status' => 'OK',
                'data' => $news
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

//Post Post
$app->post('/api/news',function() use ($app){
  // echo "1";
  $new = $app->request->getJsonRawBody();
  $phql = "INSERT INTO News (PostId, UserId, Image, Text, PostTime, Longitude, Latitude, Setting, LikeNum) VALUES (:PostId:, :UserId:, :Image:, :Text:, :PostTime:, :Longitude:, :Latitude:, :Setting:, :LikeNum:)";
  // echo json_encode($new);
  // $phql = "INSERT INTO `news` (`PostId`, `UserId`, `Text`, `Image`, `PostTime`, `Longitude`, `Latitude`, `Setting`, `LikeNum`) VALUES ('another', 'vivian', 'I\'m happy', 'http://www.alienskin.com/site/wp-content/uploads/2015/02/ChristinaRamseyAdventuresAwait.jpg', '2016-12-09 20:14:54', '42.55', '124.24', 'public', '2');"
  $status = $app->modelsManager->executeQuery($phql,array(
    'PostId' => $new->PostId,
    'UserId' => $new->UserId,
    'Text' => $new->Text,
    'Image' => $new->Image,
    'PostTime' => $new->PostTime,
    'Longitude' => $new->Longitude,
    'Latitude' => $new->Latitude,
    'Setting' => $new->Setting,
    'LikeNum' => $new->LikeNum
  ));
echo json_encode($status);
 // echo json_encode($status->success());
  $response = new Phalcon\Http\Response();
    if ($status->success() == true) {
        $response->setStatusCode(201, "Created");

        $news->PostId = $status->getModel()->PostId;

        $response->setJsonContent(
            array(
                'status' => 'OK',
                'data'   => $new
            )
        );

    } else {
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

//GET comments
$app->get('/api/comments',function() use($app){
  $phql = "SELECT * FROM Comments";
  $comments = $app->modelsManager->executeQuery($phql);
  $data = array();
  foreach($comments as $comment){
    $data[] = array(
      'PostId' => $comment->PostId,
      'Author' => $comment->Author,
      'Recipient' => $comment->Recipient,
      'Content' => $comment->Content,
      'SendTime' => $comment->SendTime
    );
  }
  echo json_encode($data);
});
//GET Comments by postid
$app->get('/api/comments/{PostId}',function($PostId) use ($app){

  $phql = "SELECT * FROM Comments WHERE PostId = :PostId:";
  $comments = $app->modelsManager->executeQuery($phql,array(
    'PostId' => $PostId 
  ));

  $data = array();
  foreach($comments as $comment){
    $data[] = array(
      'PostId' => $comment->PostId,
      'Author' => $comment->Author,
      'Recipient' => $comment->Recipient,
      'Content' => $comment->Content,
      'SendTime' => $comment->SendTime
   );
  }
  echo json_encode($data);

});

$app->post('/api/comments',function() use ($app){
  $comments = $app->request->getJsonRawBody();
  echo "1";
  $phql = "INSERT INTO Comments (PostId, Author, Recipient, Content, SendTime) VALUES (:PostId:, :Author:, :Recipient:, :Content:, :SendTime:)";
  $status = $app->modelsManager->executeQuery($phql,array(
      'PostId' => $comments->PostId,
      'Author' => $comments->Author,
      'Recipient' => $comments->Recipient,
      'Content' => $comments->Content,
      'SendTime' => $comments->SendTime
  ));

  $response = new Phalcon\Http\Response();
  if($status->success() == true){
    $response->setStatusCode(201,'Create New Comments');
    $comment->PostId = $status->getModel()->PostId;

    $response->setJsonContent(array('status'=>'ok','data'=>$comment));
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

//GET request
$app->get('/api/invite',function() use($app){
  // echo "success";
  $phql = "SELECT * FROM Invitation";
  $invites = $app->modelsManager->executeQuery($phql);
  $data = array();
  // echo $data;
  foreach($invites as $invite){
    $data[] = array(
      'UserId' => $invite->UserId,
      'Invitor' => $invite->Invitor
    );
  }
  echo json_encode($data);
});
// SELECT DISTINCT invitor FROM Invitation i JOIN relationship r Where i.UserId = r.UserId and i.UserId = "vivi" and i.invitor not in(SELECT Friend from relationship where UserId = "vivi")
//get request by name
$app->get('/api/invite/{UserId}',function($UserId) use ($app){

  $phql = "SELECT DISTINCT invitor FROM Invitation i JOIN relationship r Where i.UserId = r.UserId and i.UserId = :UserId: and invitor IN (SELECT Friend from relationship where Friend IS NOT NULL)";
  $invites = $app->modelsManager->executeQuery($phql,array(
    'UserId' => $UserId 
  ));

  $data = array();
  foreach($invites as $invite){
    $data[] = array(
    'Invitor' => $invite->Invitor
   );
  }
  echo json_encode($data);

});

//post request
$app->post('/api/invite',function() use ($app){
  $invite = $app->request->getJsonRawBody();
  $phql = "INSERT INTO Invitation (UserId, Invitor) VALUES (:UserId:, :Invitor:)";
  $status = $app->modelsManager->executeQuery($phql,array(
    'UserId' => $invite->UserId,
    'Invitor' => $invite->Invitor
  ));

  $response = new Phalcon\Http\Response();
    if ($status->success() == true) {
        $response->setStatusCode(201, "Created");


        $response->setJsonContent(
            array(
                'status' => 'OK',
                'data'   => $invite
            )
        );

    } else {
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
$app->delete('/api/invite/{UserId}/{Invitor}',function($UserId, $Invitor) use ($app){

  $phql = "DELETE FROM Invitation WHERE UserId = :UserId: and Invitor = :Invitor: ";
  // echo json_encode($Invitor);
  $status = $app->modelsManager->executeQuery($phql,array(
      'UserId' => $UserId,
      'Invitor' => $Invitor
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

//GET friendship
$app->get('/api/relationship',function() use($app){
  $phql = "SELECT * FROM Relationship";
  $relations = $app->modelsManager->executeQuery($phql);
  $data = array();
  foreach($relations as $relation){
    $data[] = array(
      'UserId' => $relation->UserId,
      'Friend' => $relation->Friend
    );
  }
  echo json_encode($data);
});

//post relationship
$app->post('/api/relationship',function() use ($app){
  $relation = $app->request->getJsonRawBody();
  $phql = "INSERT INTO Relationship (UserId, Friend) VALUES (:UserId:, :Friend:)";
  $status = $app->modelsManager->executeQuery($phql,array(
    'UserId' => $relation->UserId,
    'Friend' => $relation->Friend
  ));

  $response = new Phalcon\Http\Response();
    if ($status->success() == true) {
        $response->setStatusCode(201, "Created");


        $response->setJsonContent(
            array(
                'status' => 'OK',
                'data'   => $relation
            )
        );

    } else {
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
//get friend from user
$app->get('/api/relationship/{UserId}',function($UserId) use ($app){

  $phql = "SELECT * FROM Relationship WHERE UserId LIKE :UserId: ORDER BY UserId";
  $relations = $app->modelsManager->executeQuery($phql,array(
    'UserId' => '%'. $UserId .'%'
  ));

  $data = array();
  foreach($relations as $relation){
    $data[] = array(
    'UserId' => $relation->UserId,
    'Friend' => $relation->Friend
   );
  }
  echo json_encode($data);

});

//get friend from user's friend
$app->get('/api/relationship/friend/{UserId}',function($UserId) use ($app){

  $phql = "SELECT DISTINCT Friend FROM Relationship WHERE UserId IN (SELECT Friend FROM Relationship WHERE UserId = :UserId:) and Friend not in (SELECT Friend FROM Relationship WHERE UserId = :UserId:) and Friend != :UserId:";
  $relations = $app->modelsManager->executeQuery($phql,array(
    'UserId' => $UserId 
  ));

  $data = array();
  foreach($relations as $relation){
    $data[] = array(
    'Friend' => $relation->Friend
   );
  }
  echo json_encode($data);

});


$app->handle();
