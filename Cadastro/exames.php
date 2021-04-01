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
    $emailErr = $dateErr = $hourErr = $examErr = "";
    $email = $date = $hour = $exam = "";


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


    function verifyExistance($date, $hour, $exam)
    {
        
        $DBManager = new MongoDB\Driver\Manager(server);
        
        $filter = [ 
            'date' => $date,
            'hour' => $hour,
            'exame' => $exam
            ]; 
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

        if(empty($_POST["exam"])){
            $examErr = "Campo \"Exame\" é obrigatório!";
            $anyErr = true;
        } else {
            $exam = $_POST["exam"];
        }


        if( !$anyErr ){
            if( !verifyExistance($date, $hour, $exam) ){
                
                //geração de id como pk
                $id =  uniqid();

                $user_name = getName($email, "paciente");
                $lab_name = getName($_COOKIE["laboratorio"], "laboratorio");

                if(!$user_name){
                    alertBox("O paciente não cadastrado. Por favor, contate o administrador.");

                    redirect("../Profiles/laboratorios.php");
                    return 0;
                }
                

                $DBManager = new MongoDB\Driver\Manager(server);
                $bulk = new MongoDB\Driver\BulkWrite;
                

                $doc = [
                    "id" => $id,
                    'namePatient' => $user_name,
                    'nameLab' => $lab_name,
                    'emailPaciente' => $email,
                    'emailLaboratorio' => $_COOKIE["laboratorio"],
                    'exame' => $exam,
                    'date' => $date,
                    'hour' => $hour,
                    'resultado' =>  'Em análise'
                ];
                
                $bulk->insert($doc);
                
                $DBManager->executeBulkWrite('planoSaude.consultas', $bulk);

                alertBox("Exame marcado com sucesso!");
                redirect("../Profiles/laboratorios.php");
            } else {
                alertBox("Já existe reserva neste horário.");
            }           
        } else {
            alertBox("O Cadastro não pôde ser efetuado!");
    }
    }

    $xml = simplexml_load_file("../Dados/laboratorios.xml");
    
    ?>

    <h1>Cadastro de Exame</h1>
    <hr> <br>
    <form method="POST" action="<?php $_SERVER["PHP_SELF"];?>">

    <div class="form-group">
    E-mail do paciente: <br>
    <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error"> <?php echo $emailErr;?></span>
    <br><br>
    
    Tipo do Exame: <br>
    <select id="examselect" name="exam" value="<?php echo $exam;?>">
    <?php foreach ($xml->laboratorio as $laboratorio):
        if($_COOKIE["laboratorio"] == $laboratorio->email):
            foreach ($laboratorio->exames->exame as $exam):?>
                <option><?php echo $exam; ?></option>
    <?php  endforeach;endif; endforeach; ?>
    </select>

    <span class="error"> <?php echo $examErr;?></span>
    
    <br><br>
    
    Data do Exame: <br>
    <input type="date" id="date" name="date" value="<?php echo $date;?>">
    <span class="error"> <?php echo $dateErr;?></span>
    <br><br>


    Horário do Exame: <br>
    <select id="hour" name="hour" value="<?php echo $hour;?>">
        <option value="<?php echo 8;?>">08:00</option>
        <option value="<?php echo 8.5;?>">08:30</option>
        <option value="<?php echo 9;?>">09:00</option>
        <option value="<?php echo 9.5;?>">09:30</option>
        <option value="<?php echo 10;?>">10:00</option>
        <option value="<?php echo 10.5;?>">10:30</option>
        <option value="<?php echo 11;?>">11:00</option>
        <option value="<?php echo 11.5;?>">11:30</option>
        <option value="<?php echo 14;?>">14:00</option>
        <option value="<?php echo 14.5;?>">14:30</option>
        <option value="<?php echo 15;?>">15:00</option>
        <option value="<?php echo 15.5;?>">15:30</option>
        <option value="<?php echo 16;?>">16:00</option>
        <option value="<?php echo 16.5;?>">16:30</option>
    </select>
    <span class="error"> <?php echo $hourErr;?></span>
    <br><br>
    
    </div>
  <input type="submit" value="Enviar">


</form>

    
    <form action="../index.php">
        <input type="submit" value="Retornar à pagina inicial" />
    </form>

        
    <form action="../Profiles/administradores.php">
        <input type="submit" value="Voltar ao perfil" />
    </form>

</body>
</html>

