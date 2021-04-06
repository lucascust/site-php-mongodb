<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="../CSS/profile.css">
<link rel="stylesheet" href="../CSS/main.css">
</head>

<body>
    <?php
    include('../config.php');
    
    function alertBox($alertText)
    {
        echo "<script>alert('${alertText}');</script>";
    }

    function redirect($url)
    {
        echo "<script> window.location.href = '{$url}'; </script>";
    }

    if(!isset($_COOKIE['paciente'])){
        alertBox("Você precisa estar logado como paciente para acessar esta página.");

        redirect('../Login/pacientes.php');
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_COOKIE['paciente'])) {
            unset($_COOKIE['paciente']);
            setcookie('paciente', null, -86400, '/');
            alertBox("Deslogado com sucesso.");
            redirect("../index.php");
        }
    }

    $DBManager = new MongoDB\Driver\Manager(server);
            
    $filter = [ 'emailPatient' => $_COOKIE['paciente'] ]; 
    $query = new MongoDB\Driver\Query($filter); 
        
    $res = $DBManager->executeQuery("planoSaude.consultas", $query);
    $resExame = $DBManager->executeQuery("planoSaude.exames", $query);

    ?>


    <h1>
        Você está logado como Paciente
    </h1>
    <hr>
    <h3>
        Suas consultas:
    </h3>
    
    Você possui 
    <?php
    $cursor = $DBManager->executeQuery("planoSaude.consultas", $query);
    $cursorArray = $cursor->toArray();
    echo count($cursorArray);
    ?>
    consulta(s).

    <table id="tabela">
        <thead>
            <tr>
                <th>Médico</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Receita</th>
            </tr>
        </thead>
        <tbody> 
        <?php foreach ($res as $consulta):
            if($_COOKIE["paciente"] == $consulta->emailPatient):?>
                <tr>
                    <td><?php echo $consulta->nameDoctor; ?></td>
                    <td><?php echo $consulta->date; ?></td>
                    <td><?php echo strval($consulta->hour).":00"; ?></td>
                    <td class="prescription"><?php echo $consulta->prescription; ?></td>
                </tr>
            <?php endif; endforeach; ?>
        </tbody>
    </table>

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

    <table id="tabela">
        <thead>
            <tr>
                <th>Laboratório</th>
                <th>Exame</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody> 
        <?php foreach ($resExame as $exame):
            if($_COOKIE["paciente"] == $exame->emailPatient):?>
                <tr>
                    <td><?php echo $exame->nameLab; ?></td>
                    <td><?php echo $exame->exame; ?></td>
                    <td><?php echo $exame->date; ?></td>
                    <td><?php echo (float) $exame->hour !== floor($exame->hour) ? strval(intval($exame->hour)) . ":30" : strval($exame->hour).":00"; ?></td>
                    <td><?php echo $exame->resultado; ?></td>
                </tr>
            <?php endif; endforeach; ?>
        </tbody>
    </table>

    <script src="../JS/buildTable.js"></script>


    <br>
    <form method="POST" action="<?php $_SERVER["PHP_SELF"]; ?>">
        <input type="submit" name="submit" value="Deslogar">
    </form>


    <form action="../index.php">
        <input type="submit" value="Retornar à pagina inicial" />
    </form>

</body>

</html>