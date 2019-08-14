<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	exit(0);
}
$postdata = file_get_contents("php://input");
if (isset($postdata)) {
	$data = json_decode($postdata);
	$analise = !empty($_GET['analise']) ? addslashes(  $_GET['analise'] ) : "";
	
	$conexao = new mysqli("localhost","root","","conf");
	//$query = "select id, codigo, nome, total, media, porcen from analise where empresa like '%" . $empresa . "%' order by rand()";
	$query = "select id, codigo, nome, total, media, porcen, data from analise";
	class analiseprodconf{
		public $id;
		public $codigo;
		public $nome;
		public $total;
		public $media;
		public $porcen;
		public $data;
	}
	if( $result = $conexao->query($query) ){

		$analises = array();
	
	$jsonData = array();
	$jsonData = array("rows" => mysqli_num_rows($result));
	
	while ($obj = $result->fetch_object()) {
		$analiseprodconf = new analiseprodconf();
		$analiseprodconf->id = utf8_encode( $obj->id );
		$analiseprodconf->codigo = utf8_encode( $obj->codigo );
		$analiseprodconf->nome = utf8_encode( $obj->nome );
		$analiseprodconf->total = utf8_encode( $obj->total );
		$analiseprodconf->media = utf8_encode( $obj->media );
		$analiseprodconf->porcen = utf8_encode( $obj->porcen );
		$analiseprodconf->data = utf8_encode( $obj->data );
		$analises[] = (object)$analiseprodconf;
	}
	$result->close();
	$conexao->close();
		$jOut['analises'] = array_values($analises);
		$jsonData = array_merge($jOut, $jsonData);
		echo json_encode($jsonData);
	}
	else {
		echo "No have analise!";
	}
}
else{
	echo "No have parameter!";
}
?>