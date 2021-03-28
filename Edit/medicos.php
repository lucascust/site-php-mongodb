<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../CSS/main.css">
<link rel="stylesheet" href="../CSS/cadastro.css">
</head>

<body>
    <?php
        $anyErr = false;
        $nameErr = $emailErr = $ageErr = $phoneErr = $specialtyErr = "";
        
        $name = $adress = $email = $age = $password = $phone = $gender = $specialty = "";
        $CRM = $CRMErr = "";

        $xml = simplexml_load_file("../Dados/medicos.xml");
        
        for ($i = 0; $i < $xml->count(); $i++) {
    
            $medicoXml = $xml->medico[$i]->email;

            if ($_COOKIE['medico'] == $medicoXml) {
                $medicoId = $i;
            }
        }


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


        if ($_SERVER["REQUEST_METHOD"] == "POST"){
        

            // Verifica se foi enviado o parâmetro, testa validade e substitui no XML
            if(!empty($_POST["name"])){

                $name = test_input($_POST["name"]);

                if (!preg_match("/^[a-zA-Z ]*$/",$name)){
                    $nameErr = "Somente letras e espaços são permitidos";
                    $anyErr = true;
                } else {
                    $xml->medico[$medicoId]->name = $name;
                }
            }

            if(!empty($_POST["age"])){

                $age = test_input($_POST["age"]);

                if (!preg_match("/^[0-9 ]*$/",$age)){
                    $ageErr = "Somente números são permitidos!";
                    $anyErr = true;
                } else {
                    $xml->medico[$medicoId]->age =  $age;
                }
            }
            
            if(!empty($_POST["gender"])){
                $xml->medico[$medicoId]->gender =  $_POST["gender"];
            }
            
            if(!empty($_POST["adress"])){
                $adress = test_input($_POST["adress"]);
                $xml->medico[$medicoId]->adress =  $adress;
            } 

            if(!empty($_POST["password"])){
                $password = test_input($_POST["password"]);
                $xml->medico[$medicoId]->password =  $password;
            }
            
            if (!empty($_POST["email"])) {

                $email = test_input($_POST["email"]);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Formato de Email inválido!";
                    $anyErr = true;
                } else {
                    $xml->medico[$medicoId]->email = $email;
                }
            }

            if (!empty($_POST["phone"])) {

                $phone = test_input($_POST["phone"]);

                if (!preg_match("/^[0-9 ]*$/",$age)){
                    $ageErr = "Somente números são permitidos!";
                    $anyErr = true;
                } else {
                    $xml->medico[$medicoId]->phone = $phone;
                }
            }
        
            if(!empty($_POST["specialty"])){

                $specialty = test_input($_POST["specialty"]);

                if (!preg_match("/^[a-zA-Z ]*$/",$specialty)){
                    $specialtyErr = "Somente letras e espaços são permitidos";
                    $anyErr = true;
                } else {
                    $xml->medico[$medicoId]->specialty = $specialty;
                }   
            }
        
            if(!empty($_POST["CRM"])){

                $CRM = test_input($_POST["CRM"]);
                
                if (!preg_match("/^[0-9]*$/",$CRM)){
                    $CRMErr = "Somente números são permitidos";
                    $anyErr = true;
                } else {
                    $xml->medico[$medicoId]->CRM = $CRM;
                }                    
            }

        if( !$anyErr ){
            
            // Configuração para identar corretamente
            $xml->saveXML();
            $xml->saveXML("../Dados/medicos.xml");

            alertBox("Dados atualizados com sucesso!");

            redirect('../Edit/medicos.php');    
        } else {
            alertBox("O Cadastro não pôde ser efetuado!");
        }
    }

    ?>
            <h1>Informações de cadastro</h1>
        
        <hr>
        
        <p>
            Para editar suas informações, basta preencher o campo abaixo da respectiva informação e enviar.
        </p>

        <div class="form-group">

        <form method="POST" action="<?php $_SERVER["PHP_SELF"];?>">
        <?php foreach ($xml->medico as $medico):
                if($_COOKIE["medico"] == $medico->email):?>
                        Seu Nome: <?php echo $medico->name; ?></br>
                        <input type="text" name="name" placeholder="Insira aqui para editar" value="<?php echo $name;?>">
                        <span class="error"> <?php echo $nameErr;?></span>
                        <br><br>
                        
                        Seu Email: <?php echo $medico->email; ?></br>
                        <input type="text" name="email" placeholder="Insira aqui para editar" value="<?php echo $email;?>">
                        <span class="error"> <?php echo $emailErr;?></span>
                        <br><br>
                        
                        Seu Endereço: <?php echo $medico->adress; ?></br>
                        <input type="text" name="adress" placeholder="Insira aqui para editar" value="<?php echo $adress;?>">
                        <br><br>
                        
                        Seu Telefone: <?php echo $medico->phone; ?></br>
                        <input type="text" name="phone" placeholder="Insira aqui para editar" value="<?php echo $phone;?>">
                        <span class="error"> <?php echo $phoneErr;?></span>
                        <br><br>
                        
                        Sua Senha: <?php echo $medico->password; ?></br>
                        <input type="password" name="password" placeholder="Insira aqui para editar" value="<?php echo $password;?>">
                        <br><br>
                        
                        Seu Genero: <?php echo $medico->gender; ?></br>
                        <input type="radio" name="gender" <?php echo $gender=="feminino";?> value="feminino">Feminino
                        <input type="radio" name="gender" <?php echo $gender=="masculino";?> value="masculino">Masculino
                        <input type="radio" name="gender" <?php echo $gender=="outro";?> value="outro">Outro
                        <br><br>
                        
                        Sua Idade: <?php echo $medico->age; ?></br>
                        <input type="text" name="age" placeholder="Insira aqui para editar" value="<?php echo $age;?>">
                        <span class="error"> <?php echo $ageErr;?></span>
                        <br><br>
                        
                        Sua Especialidade: <?php echo $medico->specialty; ?></br>
                        <input type="text" name="specialty" placeholder="Insira aqui para editar" value="<?php echo $specialty;?>">
                        <span class="error"> <?php echo $specialtyErr;?></span>
                        <br><br>
                        
                        Seu CRM: <?php echo $medico->CRM; ?></br>
                        <input type="text" name="CRM" placeholder="Insira aqui para editar" value="<?php echo $CRM;?>">
                        <span class="error"> <?php echo $CRMErr;?></span>
                        <br><br>
                        
                        <?php endif; endforeach; ?>
        </div>

            <input type="submit" name="submit" value="Enviar">
        </form>
        
        
        <form action="../index.php">
         <input type="submit" value="Retornar à pagina inical" />
        </form>
    


</body>
</html>