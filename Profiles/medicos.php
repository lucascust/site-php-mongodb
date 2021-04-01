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

    if(!isset($_COOKIE['medico'])){
        alertBox("Você precisa estar logado como medico para acessar esta página.");

        redirect('../Login/medicos.php');
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_COOKIE['medico'])) {
            unset($_COOKIE['medico']);
            setcookie('medico', null, -86400, '/');
            alertBox("Deslogado com sucesso.");
            redirect("../index.php");
        }
    }

    $DBManager = new MongoDB\Driver\Manager(server);
            
    $filter = []; 
    $query = new MongoDB\Driver\Query($filter); 
        
    $res = $DBManager->executeQuery("planoSaude.consultas", $query);

    ?>


    <h1>
        Você está logado como Médico
    </h1>

    <hr>

    <h3>
        Suas consultas marcadas:
    </h3>

    <table id="tabela">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Email</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Receita</th>
                <th>Observações</th>
            </tr>
        </thead>
        <tbody> 
        <?php foreach ($res as $consulta):
            if($_COOKIE["medico"] == $consulta->emailDoctor):?>
                <tr>
                    <td><?php echo $consulta->namePatient; ?></td>
                    <td><?php echo $consulta->emailPatient; ?></td>
                    <td><?php echo $consulta->date; ?></td>
                    <td><?php echo strval($consulta->hour).":00"; ?></td>
                    <td class="prescription"><?php echo $consulta->prescription; ?></td>
                    <td class="observations"><?php echo $consulta->observations; ?></td>
                </tr>
            <?php endif; endforeach; ?>
        </tbody>
    </table>


    <br>
    
    <form action="../Edit/medicos.php">
    <input type="submit" value="Verificar/Editar Dados" />
    </form>

    <form action="../Cadastro/consultas.php">
    <input type="submit" value="Marcar Consulta" />
    </form>

    </form>
    <form method="POST" action="<?php $_SERVER["PHP_SELF"]; ?>">
        <input type="submit" name="submit" value="Deslogar">
    </form>
    
    <form action="../index.php">
    <input type="submit" value="Retornar à pagina inicial" />
</body>

</html>