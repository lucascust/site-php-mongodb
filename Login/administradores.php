<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../CSS/main.css">
<link rel="stylesheet" href="../CSS/loginusers.css">
</head>

<body>
    <?php
        $loginErr = "";
        $email = $password = "";

        function alertBox($alertText) {
            echo "<script>alert('${alertText}');</script>";
        }

        function redirect($url){
            echo "<script> window.location.href = '{$url}'; </script>";
        }
        
        function verifyNamePassword($email, $password, $userType)
        {
    
            $xml = simplexml_load_file("../Dados/{$userType}.xml") or die("Error: Cannot create object");
    
            for ($i = 0; $i < $xml->count(); $i++) {
    
                $xmlEmail = $xml->administrador[$i]->email;

                if ($email == $xmlEmail) {
                    if($password == $xml->administrador[$i]->password){
                        alertBox("Logado com sucesso!");
                        return 1;
                    } else {
                        alertBox("Senha Incorreta!");
                        return 0;
                    }
                }
            }
    
            alertBox("Usuário não existe!");
    
            return 0;
        }


        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            if(empty($_POST["email"]) or empty($_POST["password"])){
                $loginErr = "Insira email e senha.";
            } else {
                if (verifyNamePassword($_POST["email"], $_POST["password"], "administradores")){
                    // 1 dia de validade
                    setcookie( "administrador", "{$_POST["email"]}", time() + 86400, "/" );

                    redirect("../Profiles/administradores.php");
                }
            }   
        }
    ?>

    <div id="adm-container" class="container">
        <div class="header">

            <h1>Login</h1>
        </div>
        
        <hr>
        
        <h3>Administrador</h3>
        
        <div id="adm-form">

            <form method="POST" action="<?php $_SERVER["PHP_SELF"];?>">
                <br>
                
                E-mail: 
                <input type="text" name="email" value="<?php echo $email;?>">
                <span class="error"> <?php echo $loginErr;?></span>
                <br><br>
                
                Senha:
                <input type="password" name="password" value="<?php echo $password;?>">
                <span class="error"> <?php echo $loginErr;?></span>
                <br><br>
                
                
                <input type="submit" id="adm-submit" name="submit" value="Enviar">
            </form>
        </div>
            
            <br>
        
        <h4>Ainda não está cadastrado?</h4>
        <form action="../Cadastro/administradores.php">
        <input id="adm-button" type="submit" value="Cadastro" />
    </form>
    
    <form action="../index.php">
    <input id="adm-button" type="submit" value="Retornar à pagina inicial" />
</form>



</div>
</body>
</html>