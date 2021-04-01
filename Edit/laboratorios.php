<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../CSS/main.css">
<link rel="stylesheet" href="../CSS/cadastro.css">
</head>

<body>
    <?php
        include('../config.php');

        $anyErr = false;
        $nameErr = $emailErr = $ageErr = $phoneErr = $examErr = $CNPJErr = "";
        
        $name = $adress = $email = $exame = $password = $phone = $CNPJ = $specialty = "";

        
        $DBManager = new MongoDB\Driver\Manager(server);
                
        $filter = ['email' => $_COOKIE['laboratorio']]; 
        $query = new MongoDB\Driver\Query($filter); 
            
        $res = $DBManager->executeQuery("planoSaude.laboratorios", $query);
    


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
        
            // Perimeiro verifica se o input foi do botão de remover exame
            if(!empty($_POST["remove"])){
                si = 0; $i < $xml->laboratorio[$laboratorioId]->exames->count(); $i++) {
    
                    $exameXml = $xml->laboratorio[$laboratorioId]->exames->exame[$i];
                    echo $exameXml;
                    if ($_POST["remove"] == $exameXml) {
                        unset($xml->laboratorio[$laboratorioId]->exames->exame[$i]);
                    }
                }
                $xml->saveXML();
                $xml->saveXML("../Dados/laboratorios.xml");
                
                alertBox("Exame " . $_POST["remove"]. " removido com sucesso.");
                redirect('../Edit/laboratorios.php');
                return 0;
            }


            // Verifica se foi enviado o parâmetro, testa validade e substitui no XML
            if(!empty($_POST["name"])){

                $name = test_input($_POST["name"]);

                if (!preg_match("/^[a-zA-Z ]*$/",$name)){
                    $nameErr = "Somente letras e espaços são permitidos";
                    $anyErr = true;
                } else {
                    $xml->laboratorio[$laboratorioId]->name = $name;
                }
            }

            if(!empty($_POST["CNPJ"])){

                $CNPJ = test_input($_POST["CNPJ"]);

                if (!preg_match("/^[0-9 ]*$/",$CNPJ)){
                    $CNPJErr = "Somente números são permitidos!";
                    $anyErr = true;
                } else {
                    $xml->laboratorio[$laboratorioId]->CNPJ =  $CNPJ;
                }
            }
            
            if(!empty($_POST["adress"])){
                $adress = test_input($_POST["adress"]);
                $xml->laboratorio[$laboratorioId]->adress =  $adress;
            } 

            if(!empty($_POST["password"])){
                $password = test_input($_POST["password"]);
                $xml->laboratorio[$laboratorioId]->password =  $password;
            }
            
            if (!empty($_POST["email"])) {

                $email = test_input($_POST["email"]);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Formato de Email inválido!";
                    $anyErr = true;
                } else {
                    $xml->laboratorio[$laboratorioId]->email = $email;
                }
            }

            if (!empty($_POST["phone"])) {

                $phone = test_input($_POST["phone"]);

                if (!preg_match("/^[0-9 ]*$/",$age)){
                    $ageErr = "Somente números são permitidos!";
                    $anyErr = true;
                } else {
                    $xml->laboratorio[$laboratorioId]->phone = $phone;
                }
            }

            if(!empty($_POST["exame"])){
                $xml->laboratorio[$laboratorioId]->exames->addChild('exame',  $_POST["exame"]);;
            }



        if( !$anyErr ){
            
            // Configuração para identar corretamente
            $xml->saveXML();
            $xml->saveXML("../Dados/laboratorios.xml");

            alertBox("Dados atualizados com sucesso!");

            redirect('../Edit/laboratorios.php');    
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
        <?php foreach ($xml->laboratorio as $laboratorio):
            if($_COOKIE["laboratorio"] == $laboratorio->email):?>
                    Seu Nome: <?php echo $laboratorio->name; ?></br>
                    <input type="text" name="name" placeholder="Insira aqui para editar" value="<?php echo $name;?>">
                    <span class="error"> <?php echo $nameErr;?></span>
                    <br><br>
                    
                    Seu Email: <?php echo $laboratorio->email; ?></br>
                    <input type="text" name="email" placeholder="Insira aqui para editar" value="<?php echo $email;?>">
                    <span class="error"> <?php echo $emailErr;?></span>
                    <br><br>
                    
                    Seu Endereço: <?php echo $laboratorio->adress; ?></br>
                    <input type="text" name="adress" placeholder="Insira aqui para editar" value="<?php echo $adress;?>">
                    <br><br>
                    
                    Seu Telefone: <?php echo $laboratorio->phone; ?></br>
                    <input type="text" name="phone" placeholder="Insira aqui para editar" value="<?php echo $phone;?>">
                    <span class="error"> <?php echo $phoneErr;?></span>
                    <br><br>
                    
                    Sua Senha: <?php echo $laboratorio->password; ?></br>
                    <input type="password" name="password" placeholder="Insira aqui para editar" value="<?php echo $password;?>">
                    <br><br>
                    
                    
                    Seus Tipos de Exame: <?php echo $laboratorio->exames; ?></br>                 
                    <p id="exam-list" name="exam" value="<?php echo $exam;?>">
                    <?php foreach ($xml->laboratorio as $laboratorio):
                        if($_COOKIE["laboratorio"] == $laboratorio->email):
                            foreach ($laboratorio->exames->exame as $exam):?>
                                <?php echo $exam; ?> 
                                <button id="remove-button" name="remove" value="<?php echo $exam;?>">Remover</button>
                                <br>
                    <?php  endforeach;endif; endforeach; ?>
                    </p>
                    <input type="text" name="exame" placeholder="Insira aqui para adicionar novo exame" value="<?php echo $exame;?>">
                    <span class="error"> <?php echo $examErr;?></span>
                    <br><br>
                    
                    Seu CNPJ: <?php echo $laboratorio->CNPJ; ?></br>
                    <input type="text" name="CNPJ" placeholder="Insira aqui para editar" value="<?php echo $CNPJ;?>">
                    <span class="error"> <?php echo $CNPJErr;?></span>
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