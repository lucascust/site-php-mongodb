<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../CSS/loginusers.css">
<link rel="stylesheet" href="../CSS/main.css">
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
    
                $xmlEmail = $xml->medico[$i]->email;

                if ($email == $xmlEmail) {
                    if($password == $xml->medico[$i]->password){
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

        // Verificar se já tem um cookie para médico
        if(isset($_COOKIE['medico'])){
            redirect("../Profiles/medicos.php");
        }


        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            
            if(empty($_POST["email"]) or empty($_POST["password"])){
                $loginErr = "Insira email e senha.";
            } else {
                if (verifyNamePassword($_POST["email"], $_POST["password"], "medicos")){
                    // 1 dia de validade
                    setcookie("medico", "{$_POST["email"]}", time() + 86400, "/" );

                    redirect("../Profiles/medicos.php");
                }
            }   
        }
    ?>
    <div class="container">
        <div class="header">

            <h1>Login</h1>
        </div>
        
        <hr>
        
        <h3>Médico</h3>
        
        <form method="POST" action="<?php $_SERVER["PHP_SELF"];?>">
            
            <p>
                <label for="email">E-mail</label>
                <input type="text" name="email" value="<?php echo $email;?>">
                <span class="error"> <?php echo $loginErr;?></span>
            </p>
            
            <p>
                <label for="password">Senha</label>
                <input type="password" name="password" value="<?php echo $password;?>">
                <span class="error"> <?php echo $loginErr;?></span>
            </p>

            <input type="submit" name="submit" value="Enviar">
        </form>
        
        <form action="../index.php">
        <input type="submit" value="Retornar à pagina inicial" />
    </form>
    
</div>


</body>
</html>