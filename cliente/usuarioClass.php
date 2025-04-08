<?php 

class Usuario{

    public function login($email, $senha){
        global $pdo;

        $sql = "SELECT * FROM cliente WHERE email = :email AND senha = :senha";

        $sql = $pdo->prepare($sql);
        $sql->bindValue("email", $email);
        $sql->bindValue("senha", md5($senha));
        $sql->execute();

       
        if($sql->rowCount() > 0){
            $dado = $sql->fetch();

            $_SESSION['id_cliente'] = $dado['id_cliente'];
            return true;
            // //mostrar o código do usuario
            // echo $dado['idusuario'];
        
        }else{
            return false;
        }

    }

    //buscar o usuario logado
    public function logged($id){
        global $pdo;

        $array = array();

        $sql = "SELECT nome, sobrenome FROM cliente WHERE id_cliente = :id_cliente";
        $sql = $pdo->prepare($sql);
        $sql ->bindValue("id_cliente", $id);
        $sql->execute();

        //verificar
        if($sql->rowCount() > 0){
            $array = $sql->fetch();
            //fetchAll()
            return $array;

        }


    }
}


?>