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
    $emailErr = $dateErr = $hourErr = "";
    $email = $date = $hour = "";


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

    // Entrada: (Email do usuário, tipo: paciente, laboratorio, medico)
    // Saída: nome do usuário | false
    function getName($email, $userType){

        $DBManager = new MongoDB\Driver\Manager(server);

        $filter = [ 'email' => $email ];
        $query = new MongoDB\Driver\Query($filter); 
         
        $res = $DBManager->executeQuery("planoSaude.${userType}s", $query);

        $name = false;
        foreach($res as $document) {
            if ($document->name){
                $name = $document->name;
            }
        }

        return $name;
    
    }


    function verifyExistance($date, $hour)
    {
        
        $DBManager = new MongoDB\Driver\Manager(server);
        
        $filter = [ 'date' => $date, 'hour' => $hour]; 
        $query = new MongoDB\Driver\Query($filter); 

         
        $res = $DBManager->executeQuery("planoSaude.consulta", $query);
        $res = current($res->toArray());

        if(!empty($res)){
            return true;
        }
        return false;
    }

    
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        

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

        if(empty($_POST["hour"])){
            $hourErr = "Campo \"Horário\" é obrigatório!";
            $anyErr = true;
        } else {
            $hour = $_POST["hour"];
        }
        
        if(empty($_POST["date"])){
            $dateErr = "Campo \"Data\" é obrigatório!";
            $anyErr = true;
        } else {
            $date = $_POST["date"];
        }


        if( !$anyErr ){
            if( !verifyExistance($date, $hour) ){
                
                //geração de id como pk
                $id =  uniqid();

                $user_name = getName($email, "paciente");
                $doctor_name = getName($_COOKIE["medico"], "medico");

                if(!$user_name){
                    alertBox("O paciente não cadastrado. Por favor, contate o administrador.");

                    redirect("../Profiles/medicos.php");
                    return 0;
                }
                

                $DBManager = new MongoDB\Driver\Manager(server);
                $bulk = new MongoDB\Driver\BulkWrite;
                

                $doc = [
                    "id" => $id,
                    'namePatient'=>$user_name,
                    'nameDoctor'=>$doctor_name,
                    'emailPatient'=>$email,
                    'emailDoctor'=>$_COOKIE["medico"],
                    'date'=>$date,
                    'hour'=>$hour,
                    'prescription'=> '',
                    'observations'=> '',
                ];
                
                $bulk->insert($doc);
                
                $DBManager->executeBulkWrite('planoSaude.consultas', $bulk);

                alertBox("Consulta marcada com sucesso!");
                redirect("../Profiles/medicos.php");

            } else {
                alertBox("Já existe reserva neste horário.");
            }           
        } else {
            alertBox("O Cadastro não pôde ser efetuado!");
    }


    }
        
    ?>

    <h1>Cadastro de Consulta</h1>
    <hr> <br>
    <form method="POST" action="<?php $_SERVER["PHP_SELF"];?>">

    <div class="form-group">
    E-mail do paciente: <br>
    <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error"> <?php echo $emailErr;?></span>
    <br><br>
    
    Data da Consulta: <br>
    <input type="date" id="date" name="date" value="<?php echo $date;?>">
    <span class="error"> <?php echo $dateErr;?></span>
    <br><br>


    Horário da consulta: <br>
    <select id="times" name="hour" value="<?php echo $hour;?>">
        <option value="<?php echo 8;?>">08:00</option>
        <option value="<?php echo 9;?>">09:00</option>
        <option value="<?php echo 10;?>">10:00</option>
        <option value="<?php echo 11;?>">11:00</option>
        <option value="<?php echo 14;?>">14:00</option>
        <option value="<?php echo 15;?>">15:00</option>
        <option value="<?php echo 16;?>">16:00</option>
    </select>
    <span class="error"> <?php echo $emailErr;?></span>
    <br><br>
    
    </div>

  <input type="submit">


</form>
    
    <form action="../index.php">
        <input type="submit" value="Retornar à pagina inicial" />
    </form>

        
    <form action="../Profiles/medicos.php">
        <input type="submit" value="Voltar ao perfil" />
    </form>

</body>
</html>

