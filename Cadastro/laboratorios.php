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
    $nameErr = $emailErr = $passwordErr = "";
    $phoneErr = $genderErr = $examTypesErr = $adressErr = "";
    $name = $adress = $email  = $password = $phone = $gender = $examTypes = "";
    $CNPJ = $CNPJErr = "";
    
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

    // Verifica existencia pelo nome
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
            if (!preg_match("/^[0-9 ]*$/",$phone)){
                $phoneErr = "Somente números são permitidos!";
                $anyErr = true;
            }
        }
    
        if(empty($_POST['check_list'])) {
            $examTypesErr = "Insira pelo menos um exame!";
        }
    
        if(empty($_POST["CNPJ"])){
            $CNPJErr = "Campo \"CNPJ\" é obrigatório!";
            $anyErr = true;
        } else {
            $CNPJ = test_input($_POST["CNPJ"]);
            if (!preg_match("/^[0-9]*$/",$CNPJ)){
                $CNPJErr = "Somente números são permitidos";
            }
        }

        if( !$anyErr ){
            if( !verifyExistance($email, 'laboratorio')){
                
                //geração de id como pk
                $id =  uniqid();
                
                $DBManager = new MongoDB\Driver\Manager(server);
                $bulk = new MongoDB\Driver\BulkWrite;
                

                $doc = [
                    "id" => $id,
                    'name' => $name,
                    'adress' => $adress,
                    'phone' => $phone,
                    'email' => $email,
                    'CNPJ' => $CNPJ,
                    'password' => $password,
                    'exames' => $_POST['check_list']
                ];
                
                $bulk->insert($doc);
                
                $DBManager->executeBulkWrite('planoSaude.laboratorios', $bulk);
                
                
                alertBox("Cadastro realizado com sucesso!");

                redirect('../Profiles/administradores.php');

            } else {
                alertBox("O laboratório já existe!");
            }  
            } else {
                alertBox("O Cadastro não pôde ser efetuado!");
            }

    }
        
    ?>

    <h1>Cadastro de Laboratório</h1>
    <hr><br>
    <form method="POST" action="<?php $_SERVER["PHP_SELF"];?>">


    <div class="form-group">

    <label for="name"> Nome:</label><br>
    <input type="text" name="name" value="<?php echo $name;?>">
    <span class="error"> <?php echo $nameErr;?></span>
    <br><br>
        
    <label for="email"> E-mail:</label><br>
    <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error"> <?php echo $emailErr;?></span>
    <br><br>

    <label for="password"> Senha:</label><br>
    <input type="password" name="password" value="<?php echo $password;?>">
    <span class="error"> <?php echo $passwordErr;?></span>
    <br><br>

    <label for="adress"> Endereço:</label><br>
    <input type="text" name="adress" value="<?php echo $adress;?>">
    <span class="error"> <?php echo $adressErr;?></span>
    <br><br>
    
    <label for="phone"> Telefone:</label><br>
    <input type="text" name="phone" value="<?php echo $phone;?>">
    <span class="error"> <?php echo $phoneErr;?></span>
    <br><br>
    
    <label for="exam" id="examTitle">Tipos de exame:</label> 
    <div class="examtypes">
    <label>Radiografia</label>
    <input type="checkbox" name="check_list[]" value="Radiografia">
    <br><label>Tomografia</label>
    <input type="checkbox" name="check_list[]" value="Tomografia">
    <br><label>Mamografia</label>
    <input type="checkbox" name="check_list[]" value="Mamografia">
    <br><label>Eletroencefalografia</label>
    <input type="checkbox" name="check_list[]" value="Eletroencefalografia">
    <br><label>COVID</label>
    <input type="checkbox" name="check_list[]" value="COVID">
    <span class="error"> <?php echo $examTypesErr;?></span>
    </div>
    <br><br>
    
    CNPJ:<br>
    <input type="text" name="CNPJ" value="<?php echo $CNPJ;?>">
    <span class="error"> <?php echo $CNPJErr;?></span>
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

