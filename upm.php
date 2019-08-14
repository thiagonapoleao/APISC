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
	$upm = !empty($_GET['upm']) ? addslashes(  $_GET['upm'] ) : "";
	
	$conexao = new mysqli("localhost","root","","linha");
	//$query = "select id, codigo, nome, total, media, porcen from analise where empresa like '%" . $empresa . "%' order by rand()";
	$query = "select id, indicador, meta, upm, erros from upm";
	class analiseupm{
		public $id;
		public $indicador;
		public $meta;
		public $upm;
		public $erros;		
	}
	if( $result = $conexao->query($query) ){

		$upms = array();
	
	$jsonData = array();
	$jsonData = array("rows" => mysqli_num_rows($result));
	
	while ($obj = $result->fetch_object()) {
		$analiseupm = new analiseupm();
		$analiseupm->id = utf8_encode( $obj->id );
		$analiseupm->indicador = utf8_encode( $obj->indicador );
		$analiseupm->meta = utf8_encode( $obj->meta );
		$analiseupm->upm = utf8_encode( $obj->upm );
		$analiseupm->erros = utf8_encode( $obj->erros );	
		$upms[] = (object)$analiseupm;
	}
	$result->close();
	$conexao->close();
		$jOut['upms'] = array_values($upms);
		$jsonData = array_merge($jOut, $jsonData);
		echo json_encode($jsonData);
	}
	else {
		echo "No have upms!";
	}
}
else{
	echo "No have parameter!";
}
?>