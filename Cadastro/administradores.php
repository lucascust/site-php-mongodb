<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../CSS/main.css">
</head>

<body>
    
    <?php

    include("../config.php")

    $anyErr = false;
    $nameErr = $emailErr = $passwordErr = "";
    $name = $email = $password = "";


    function alertBox($alertText) {
        echo "<script>alert('${alertText}');</script>";
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
    return $data;
    }  

    function verifyExistance($signUpName, $collection)
    {
        $signUpName = strtolower($signUpName);
        
        $DBManager = new MongoDB\Driver\Manager(server);
        
        $filter = [ 'email' => $signUpName ]; 
        $query = new MongoDB\Driver\Query($filter); 

         
        $res = $DBManager->executeQuery("planoSaude.${collection}", $query);
        
        $res = current($res->toArray());

        if(!empty($res)){
            return true;
        }
        return false;
    }



    
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        
        
        

        if(empty($_POST["name"])){
            $nameErr = "Campo \"Nome\" é obrigatório!";
            $anyErr = true;
        } else {
            $name = test_input($_POST["name"]);
            if (!preg_match("/^[a-zA-Z ]*$/",$name)){
                $nameErr = "Somente letras e espaços são permitidos";
            }
        }

        
        if (empty($_POST["email"])) {
            $emailErr = "Campo \"Email\" é obrigatório!";
            $anyErr = true;
        } else {
            $email = test_input($_POST["email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Formato de Email inválido!";
                $anyErr = true;
            }
        }
        
        if(empty($_POST["password"])){
            $passwordErr = "Campo \"Senha\" é obrigatório!";
            $anyErr = true;
        } else {
            $password = test_input($_POST["password"]);
        }

        if( !$anyErr ){
            if( !verifyExistance($name, 'administradores') ){
                
                //geração de id como pk
                $id =  uniqid();
                
                $DBManager = new MongoDB\Driver\Manager(server);
                $bulk = new MongoDB\Driver\BulkWrite;
                

                $doc = [
                    "id" => $id,
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ];
                
                $bulk->insert($doc);
                
                $DBManager->executeBulkWrite('planoSaude.administradores', $bulk);

                alertBox("Cadastro realizado com sucesso!");

                echo "<script> window.location.href = '../Login/administradores.php'; </script>";

            } else {
                alertBox("O administrador já existe!");
            }           
        } else {
            alertBox("O Cadastro não pôde ser efetuado!");
    }


    }
        
    ?>

    <h1>Cadastro de Administrador</h1>

    <form method="POST" action="<?php $_SERVER["PHP_SELF"];?>">

    Nome:
    <input type="text" name="name" value="<?php echo $name;?>">
    <span class="error"> <?php echo $nameErr;?></span>
    <br><br>

    E-mail: 
    <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error"> <?php echo $emailErr;?></span>
    <br><br>

    Senha:
    <input type="password" name="password" value="<?php echo $password;?>">
    <span class="error"> <?php echo $passwordErr;?></span>
    <br><br>
    


    <input type="submit" name="submit" value="Enviar">
    </form>
    
    <form action="../index.php">
        <input type="submit" value="Retornar à pagina inicial" />
    </form>

    

</body>
</html>

