<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="../CSS/main.css">
</head>

<body>
    <?php 
        function alertBox($alertText) {
            echo "<script>alert('${alertText}');</script>";
        }
    
        function redirect($url){
            echo "<script> window.location.href = '{$url}'; </script>";
        }


        if(!isset($_COOKIE['administrador'])){
            alertBox("Você precisa estar logado como administrador para acessar esta página.");

            redirect('../Login/administradores.php');
        }
    
    ?>

    <h1>
        Você está logado como Administrador
    </h1>
    <hr>

    <form action="../Cadastro/medicos.php">
        <input type="submit" value="Cadastrar Médico" />
    </form>

    <form action="../Cadastro/pacientes.php">
        <input type="submit" value="Cadastrar Paciente" />
    </form>

    <form action="../Cadastro/laboratorios.php">
        <input type="submit" value="Cadastrar Laboratório" />
    </form>

    <form action="../index.php">
        <input type="submit" value="Retornar à pagina inicial" />
    </form>

</body>


</html>