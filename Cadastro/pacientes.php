<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../CSS/main.css">
<link rel="stylesheet" href="../CSS/cadastro.css">
</head>

<body>
    
    <?php

    $anyErr = false;
    $nameErr = $emailErr = $ageErr = $passwordErr = "";
    $phoneErr = $genderErr = $cpfErr = $adressErr = "";
    $name = $adress = $email = $age = $password = $phone = $gender = $cpf = "";

    function alertBox($alertText) {
        echo "<script>alert('${alertText}');</script>";
    }
    
    function redirect($url){
        echo "<script> window.location.href = '{$url}'; </script>";
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
    return $data;
    }  

    // Verifica existência pelo nome
    function verifyExistance($signUpName)
    {
        $signUpName = strtolower($signUpName);

        $xml = simplexml_load_file("../Dados/pacientes.xml");

        for ($i = 0; $i < $xml->count(); $i++) {

            $xmlName = $xml->paciente[$i]->name;
            $xmlName = strtolower($xmlName);

            if ($signUpName == $xmlName) {
                return true;
            } 
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
                $nameErr = "Somente letras e espaços são permitidos!";
            }
        }

        if(empty($_POST["age"])){
            $ageErr = "Campo \"Idade\" é obrigatório!";
            $anyErr = true;
        } else {
            $age = test_input($_POST["age"]);
            if (!preg_match("/^[0-9 ]*$/",$age)){
                $ageErr = "Somente números são permitidos!";
            }
        }

        if(empty($_POST["gender"])){
            $genderErr = "    Campo \"gênero\" é obrigatório!";
            $anyErr = true;
        } else {
            $gender = test_input($_POST["gender"]);
        }

        if(empty($_POST["adress"])){
            $adressErr = "Campo \"Endereço\" é obrigatório!";
            $anyErr = true;
        } else {
            $adress = test_input($_POST["adress"]);
        }

        if(empty($_POST["password"])){
            $passwordErr = "Campo \"Senha\" é obrigatório!";
            $anyErr = true;
        } else {
            $password = test_input($_POST["password"]);
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

        if (empty($_POST["phone"])) {
            $phoneErr = "Campo \"Telefone\" é obrigatório!";
            $anyErr = true;
          } else {
            $phone = test_input($_POST["phone"]);
            if (!preg_match("/^[0-9 ]*$/",$age)){
                $phoneErr = "Somente números são permitidos!";
                $anyErr = true;
            }
        }
    

        
        if(empty($_POST["cpf"])){
            $cpfErr = "Campo \"CPF\" é obrigatório!";
            $anyErr = true;
        } else {
            $cpf = test_input($_POST["cpf"]);
            if (!preg_match("/^[0-9]*$/",$cpf)){
                $cpfErr = "Somente números são permitidos!";
                $anyErr = true;
            }
        }

        if( !$anyErr ){
            if( !verifyExistance($name) ){
                
                //geração de id como pk
                $id =  uniqid();
                
                
                $xml = simplexml_load_file("../Dados/pacientes.xml");
                
                echo $xml;
                
                //Cria um elemento
                $child = $xml->addChild('paciente');
                
                //Adiciona "Colunas"
                $child->addAttribute("id", $id);
                $child->addChild('name',$name);
                $child->addChild('adress',$adress);
                $child->addChild('phone',$phone);
                $child->addChild('email',$email);
                $child->addChild('gender',$gender);
                $child->addChild('age',$age);
                $child->addChild('cpf',$cpf);
                $child->addChild('password',$password);
                
                // Configuração para identar corretamente
                $dom = dom_import_simplexml($xml)->ownerDocument;
                $dom->formatOutput = true;
                $dom->preserveWhiteSpace = false;
                $dom->loadXML( $dom->saveXML());
                $dom->save("../Dados/pacientes.xml");

                alertBox("Cadastro realizado com sucesso!");

                redirect('../Profiles/administradores.php');

            } else {
                alertBox("O paciente já existe!");
            }           
        } else {
            alertBox("O Cadastro não pôde ser efetuado!");
    }


    }
        
    ?>

    <h1>Cadastro de Paciente</h1>
    <hr><br>


    <form method="POST" action="<?php $_SERVER["PHP_SELF"];?>">

    <div class="form-group">

    <label for="name"> Nome:</label><br>
    <input type="text" name="name" value="<?php echo $name;?>">
    <span class="error"> <?php echo $nameErr;?></span>
    <br><br>
    
    <label for="age"> Idade:</label><br>
    <input type="text" name="age" value="<?php echo $age;?>">
    <span class="error"> <?php echo $ageErr;?></span>
    <br><br>
    
    <label for="gender"> Gênero:</label><br>
    <input type="radio" name="gender" <?php echo $gender=="feminino";?> value="feminino">Feminino
    <input type="radio" name="gender" <?php echo $gender=="masculino";?> value="male">Masculino
    <input type="radio" name="gender" <?php echo $gender=="outro";?> value="other">Outro
    <span class="error"> <?php echo $genderErr;?></span>
    <br><br>
    
    <label for="password"> Senha:</label><br>
    <input type="password" name="password" value="<?php echo $password;?>">
    <span class="error"> <?php echo $passwordErr;?></span>
    <br><br>
    
    <label for="email"> E-mail:</label><br>
    <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error"> <?php echo $emailErr;?></span>
    <br><br>
    
    <label for="adress"> Endereço:</label><br>
    <input type="text" name="adress" value="<?php echo $adress;?>">
    <span class="error"> <?php echo $adressErr;?></span>
    <br><br>
    
    <label for="phone"> Telefone:</label><br>
    <input type="text" name="phone" value="<?php echo $phone;?>">
    <span class="error"> <?php echo $phoneErr;?></span>
    <br><br>
    
    <label for="cpf"> CPF:</label><br>
    <input type="text" name="cpf" value="<?php echo $cpf;?>">
    <span class="error"> <?php echo $cpfErr;?></span>
    <br><br>
    </div>

    <input type="submit" name="submit" value="Enviar">
    </form>
    
    <form action="../index.php">
        <input type="submit" value="Retornar à pagina inicial" />
    </form>

        
    <form action="../Profiles/administradores.php">
        <input type="submit" value="Voltar ao perfil" />
    </form>

</body>
</html>

