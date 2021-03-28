<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="./CSS/main.css">
<link rel="stylesheet" href="./CSS/login.css">
</head>

<body>
    <?php

    function redirect($url)
    {
        echo "<script> window.location.href = '{$url}'; </script>";
    }


    if (isset($_COOKIE['administrador'])) {
        redirect("../Profiles/administradores.php");
    }

    if (isset($_COOKIE['laboratorio'])) {
        redirect("../Profiles/laboratorios.php");
    }

    if (isset($_COOKIE['medico'])) {
        redirect("../Profiles/medicos.php");
    }

    if (isset($_COOKIE['paciente'])) {
        redirect("../Profiles/pacientes.php");
    }

    $user = "";
    $loginErr = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo $_POST["user"];

        if (empty($_POST["user"])) {
            $loginErr = "<br> Escolha sua categoria!";
        }

        switch ($_POST["user"]) {
            case "paciente":
                redirect("./Login/pacientes.php");
            case "medico":
                redirect("./Login/medicos.php");
            case "laboratorio":
                redirect("./Login/laboratorios.php");
        }
    }
    ?>
    
    
    
    <div class="container">
    
    
    <div class="header">
    <h1>Login</h1>
    <hr>
    </div>

    
    <h3>Selecione sua categoria:</h3>

    <form id="form" method="POST" action="<?php $_SERVER["PHP_SELF"]; ?>">
        <br>
        <input type="radio" name="user" <?php echo $user == "paciente"; ?> value="paciente">Paciente
        <input type="radio" name="user" <?php echo $user == "medico"; ?> value="medico">Médico
        <input type="radio" name="user" <?php echo $user == "laboratorio"; ?> value="laboratorio">Laboratório
        <span class="error"> <?php echo $loginErr; ?></span>
        <br><br>
            
            <input id="formsubmit" type="submit" name="submit" value="Enviar">
            
    </form>
    
    <form action="./index.php">
    <input type="submit" value="Retornar à pagina inicial" />
    </form>
    
    <form action="./Login/administradores.php">
        <input id="admbutton" type="submit" value="Sou Administrador" />
    </form>

    </div>

</body>

</html>