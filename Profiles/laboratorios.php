<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="../CSS/main.css">
<link rel="stylesheet" href="../CSS/profile.css">
</head>

<body>
    <?php
    include('../config.php');

    function alertBox($alertText) {
        echo "<script>alert('${alertText}');</script>";
    }
   
    function redirect($url)
    {
        echo "<script> window.location.href = '{$url}'; </script>";
    }

    if(!isset($_COOKIE['laboratorio'])){
        alertBox("Você precisa estar logado como laboratorio para acessar esta página.");

        redirect('../Login/laboratorios.php');
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {



        if (isset($_COOKIE['laboratorio'])) {
            unset($_COOKIE['laboratorio']);
            setcookie('laboratorio', null, -86400, '/');
            alertBox("Deslogado com sucesso.");
            redirect("../index.php");
        }
    }
    
    
    $DBManager = new MongoDB\Driver\Manager(server);
            
    $filter = [ 'emailLaboratorio'=>$_COOKIE['laboratorio']]; 
    $query = new MongoDB\Driver\Query($filter); 
        
    $res = $DBManager->executeQuery("planoSaude.exames", $query);

    ?>


    <h1>
        Você está logado como Laboratório
    </h1>
    <hr>

    <h3>
    Seus Exames:
    </h3>

    Você possui 
    <?php
    $cursor = $DBManager->executeQuery("planoSaude.exames", $query);
    $cursorArray = $cursor->toArray();
    echo count($cursorArray);
    ?>
    exame(s).

    <div class="table-container">

        <table id="tabela">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Email</th>
                    <th>Exame</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody> 
            <?php foreach ($res as $exame):
                if($_COOKIE["laboratorio"] == $exame->emailLaboratorio):?>
                    <tr>
                        <td><?php echo $exame->namePatient; ?></td>
                        <td><?php echo $exame->emailPatient; ?></td>
                        <td><?php echo $exame->exame; ?></td>
                        <td><?php echo $exame->date; ?></td>
                        <td><?php echo (float) $exame->hour !== floor($exame->hour) ? strval(intval($exame->hour)) . ":30" : strval($exame->hour).":00"; ?></td>
                        <td><?php echo $exame->resultado; ?></td>
                    </tr>
                <?php endif; endforeach; ?>
            </tbody>
        </table>
    </div>                
    <br>
    
    <form action="../Edit/laboratorios.php">
    <input type="submit" value="Verificar/Editar Dados" />
    </form>

    <form action="../Cadastro/exames.php">
    <input type="submit" value="Cadastrar Exame" />
    </form>

    <form method="POST" action="<?php $_SERVER["PHP_SELF"]; ?>">
        <input type="submit" name="submit" value="Deslogar">
    </form>

    <form action="../index.php">
        <input type="submit" value="Retornar à pagina inicial" />
    </form>
</body>

</html>