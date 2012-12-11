<?php
session_start();
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use src\Entities\Comment;
use src\Entities\Questions;
use src\Entities\Players;

require_once (BASE_DIR . '/src/Entities/Comment.php');
require_once (BASE_DIR . '/src/Entities/Questions.php');
require_once (BASE_DIR . '/src/Entities/Players.php');




$app->get('/gTk.{format}', function(Request $request) use($app){
    
   $length = 20;
   $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $random_string = "";    
   
   for ($p = 0; $p < $length; $p++) {
		$random_string .= $characters[rand(0, strlen($characters)-1)];
	}
  
   $_SESSION["token"] = $random_string;
   $random_string = encrypt($random_string,"wopidom");
   return new Response($random_string, 200); 
  
   // $a= array("J", "Q", "K", "A");
	//shuffle($a);
  //  return new Response($a[0], 200); 
    
});


$app->get('/gQ.{format}', function(Request $request) use($app){
    
	//$token = $request->get('token');
	$encoded = $request->get('param');
    $token=decrypt($encoded,"wopidom");
	
	if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		$sql = Questions::getQuestions();
		
		//-- Obtenemos los datos de la base de datos y aplicamos el utf8_encode() 
		//   a cada item del array llamado a nuestro método utf8_converter() definido
		//   en src\util.php
			
		$q = $app['db']->fetchAll($sql);
		 
		$q = utf8_converter($q);
		
		//-- Una vez encontrados los datos, los retornamos con un código HTTP 200 - OK
		//return new Response(encrypt(json_encode($q),"wopidom"), 200); 
		//encrypt($q[0]["answer1"]
		for($i=0;$i<sizeof($q);$i++)
		{
		
		//$toMix = array($q[$i]["answer1"], $q[$i]["answer2"], $q[$i]["answer3"],$q[$i]["answer4"]);
		$t = array($q[$i]["answer1"], $q[$i]["answer2"], $q[$i]["answer3"],$q[$i]["answer4"]);
		shuffle($t);
		$right = array_search($q[$i]["answer1"], $t)+1; 
		$q[$i]["answer1"] = $t[0];
		$q[$i]["answer2"] = $t[1];
		$q[$i]["answer3"] = $t[2];
		$q[$i]["answer4"] = $t[3];
		
		
		$_SESSION["answers"][$i] = array($q[$i]["id"],$right);
		}
		
		//return new Response($toMix[0] . " - " . $toMix[1] . " - " .$toMix[2] . " - " . $toMix[3] . "\r" . $t[0] . " - " . $t[1] . " - " .$t[2] . " - " .$t[3] . " - right:" . $right, 200); 
		return new Response(encrypt(json_encode($q),"wopidom"), 200); 
	}
	else
	{
		return new Response("Invalid Token", 200);
	}	
    
});



$app->get('/gPi.{format}', function(Request $request) use($app){
    
    $param = $request->get('param');
    $encoded=decrypt($param,"wopidom");
 
	$data = explode(",",$encoded);	
	$idPlayer= $data[0];
	$token = $data[1];
	


    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		//$idPlayer = $request->get('idPlayer');
		$sql = Players::getInfo($idPlayer);
		
		//-- Obtenemos los datos de la base de datos y aplicamos el utf8_encode() 
		//   a cada item del array llamado a nuestro método utf8_converter() definido
		//   en src\util.php
		
		$q = $app['db']->fetchAll($sql);
		$q = utf8_converter($q);
		
		//-- Una vez encontrados los datos, los retornamos con un código HTTP 200 - OK
		return new Response(encrypt(json_encode($q),"wopidom"), 200); 
	}
	else
		return new Response("Invalid Token", 200); 		
    
});

$app->get('/gT.{format}', function(Request $request) use($app){
   
    $encoded = $request->get('param');
    $token=decrypt($encoded,"wopidom");
  
   // $token = $request->get('token');
   if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		//$d = date('Y-m-d H:i:s');
		date_default_timezone_set('UTC');
		$format = 'Y-m-d H:i:s';
		$str = date($format);
		//echo $str . "<br>";
		$dt = DateTime::createFromFormat($format, $str);
		$timestamp = $dt->format('U');
		//echo $timestamp;
		return new Response(encrypt($timestamp,"wopidom"), 200); 
		//return new Response("hola", 200); 
	}
	else
		return new Response("Invalid Token", 200); 

	//return new Response($token , 200); 	
    
});

$app->get('sA.{format}', function(Request $request) use($app){
    
    //-- Controlamos que los parámetros que deben llegar por POST efectivamente
    //   lleguen y en el caso de que no lo hagan enviamos un error con código 
    //   400 - Solicitud incorrecta
      
    //-- Controlamos que los parámetros que deben llegar por POST efectivamente
    //   lleguen y en el caso de que no lo hagan enviamos un error con código 
    //   400 - Solicitud incorrecta
    //-- También podemos usar directamente la Injección de dependecias para 
    //   obtener el request del contenedor a diferencia del ejemplo anterior.
	
	$param = $request->get('param');
    $encoded=decrypt($param,"wopidom");
 
	$data = explode(",",$encoded);	
	
	$idAnswer= $data[0];
    $idQuestion= $data[1];
	$token = $data[2];
	
	//$token = $request->get('token');
    if(isset($_SESSION["token"])  && $_SESSION["token"]==$token)
	{
		//$idQuestion = $request->get('idQuestion');
		//$idAnswer = $request->get('idAnswer');
		$result="0";
		for($i=0;$i<sizeof($_SESSION["answers"]);$i++)
		{
		if($_SESSION["answers"][$i][0]==$idQuestion && $_SESSION["answers"][$i][1]==$idAnswer)
		    $result="1";
		}
		
		//return new Response(encrypt("answer:" . $idAnswer . ", q: " . $idQuestion . ", result: " . $result,"wopidom"), 200);
		return new Response(encrypt($result,"wopidom"), 200);
	}
	else
		return new Response("false", 200); 		
 
    
});




$app->get('/ver-comentarios.{format}', function(Request $request) use($app){
    
    $sql = Comment::findAll();
    
    //-- Obtenemos los datos de la base de datos y aplicamos el utf8_encode() 
    //   a cada item del array llamado a nuestro método utf8_converter() definido
    //   en src\util.php
    $comentarios = $app['db']->fetchAll($sql);
    $comentarios = utf8_converter($comentarios);
    
    //-- Una vez encontrados los datos, los retornamos con un código HTTP 200 - OK
    return new Response(json_encode($comentarios), 200); 
    
});

$app->post('/crear-comentario.{format}', function(Request $request) use($app){
    
    //-- Controlamos que los parámetros que deben llegar por POST efectivamente
    //   lleguen y en el caso de que no lo hagan enviamos un error con código 
    //   400 - Solicitud incorrecta
    if (!$comment = $request->get('comment'))
    {
        return new Response('Parametros insuficientes', 400);
    }

    //-- Utilizamos como ejemplo un objeto Comentario para delegar la creación 
    //   del SQL utilizando el método PDO::quote() para no tener problemas con 
    //   SQL Injection.
    $c = new Comment();
    $c->author = $app['db']->quote($comment['author']);
    $c->email = $app['db']->quote($comment['email']);
    $c->content = $app['db']->quote($comment['content']);
    
    $sql = $c->getInsertSQL();
    
    //-- Ejecutamos la sentencia
    $app['db']->exec($sql);
    
    //-- En caso de exito retornamos el código HTTP 201 - Creado
    return new Response('Comentario creado', 201);
    
});

$app->put('actualizar-comentario/{id}.{format}', function($id) use($app){
    
    //-- Controlamos que los parámetros que deben llegar por POST efectivamente
    //   lleguen y en el caso de que no lo hagan enviamos un error con código 
    //   400 - Solicitud incorrecta
    //-- También podemos usar directamente la Injección de dependecias para 
    //   obtener el request del contenedor a diferencia del ejemplo anterior.
    
    if (!$comment = $app['request']->get('comment'))
    {
        return new Response('Parametros insuficientes', 400);
    }
    
    //-- Obtenemos el select para encontrar un comentario de acuerdo al $id y
    //   comprobar que lo que vamos a modificar realmente exista.
    $sql = Comment::find($id);
    
    $comentario = $app['db']->fetchAll($sql);
    
    //-- En caso de no existir el comentario a modificar retornamos un código
    //   HTTP 404 - No encontrado
    if(empty($comentario))
    {
        return new Response('Comentario no encontrado.', 404);
    }
    
    //-- Si existe el comentario a modificar obtenemos el SQL para el update y
    //   lo ejecutamos
    $content = $app['db']->quote($comment['content']);
    $sql = Comment::getUpdateSQL($id, $content);
    
    //-- Ejecutamos la sentencia
    $app['db']->exec($sql);
    
    //-- En caso de exito retornamos el código HTTP 200 - OK
    return new Response("Comentario con ID: {$id} actualizado", 200);
    
});

$app->delete('eliminar-comentario/{id}.{format}', function($id) use($app){
    
    //-- Obtenemos el select para encontrar un comentario de acuerdo al $id y
    //   comprobar que lo que vamos a eliminar realmente exista.
    $sql = Comment::find($id);
    
    $comentario = $app['db']->fetchAll($sql);
    
    //-- En caso de no existir el comentario a eliminar retornamos un código
    //   HTTP 404 - No encontrado
    if(empty($comentario))
    {
        return new Response('Comentario no encontrado.', 404);
    }
    
    //-- Obtenemos el SQL para eliminar el comentario y ejecutamos la sentencia
    $sql = Comment::getDeleteSQL($id);
    
    $app['db']->exec($sql);
    
    //-- En caso de exito retornamos el código HTTP 200 - OK
    return new Response("Comentario con ID: {$id} eliminado", 200);
    
}); 

return $app;