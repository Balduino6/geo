<?php 

$servidor = "localhost";
$usuario = "root";
$senha = "";
$dbname ="geovane";

global $pdo;

try{

    //orientada a objectos com PDO
    $pdo = new PDO("mysql:dbname=".$dbname."; host=".$servidor, $usuario, $senha);

    $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "erro:".$e->getMessage();
    exit;
}


// $sql = $pdo -> query("SELECT * FROM usuarios");
// $sql->execute();

// echo $sql -> rowCount();

?>
