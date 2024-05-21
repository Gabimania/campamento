<?php
include("conection.php");
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "select * from user where iduser = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $saldo = $result["saldo"];
    $name = $result["name"];
    $surname = $result["surname"];
}

if (isset($_GET["accion"])) {

    $accion = $_GET["accion"];
    $cantidad = $_GET["cantidad"];


    //Acción a realizar
    if ($accion === 'ingresar') {
        $nuevoSaldo = $saldo + $cantidad;
    } elseif ($accion === 'retirar') {
        if ($saldo >= $cantidad) {
            $nuevoSaldo = $saldo - $cantidad;
        } else {
            echo "<script>
                    setTimeout(function() {
                        alert('No tienes saldo suficiente para retirar: $cantidad.');
                        window.location.href = 'http://localhost/campamento/?id=$id';
                    }, 100);
                 </script>";
            exit();
        }
    }
    //Actualizacción del saldo en la BBDD
    $sql = "update user set saldo = ? where iduser= ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $nuevoSaldo);
    $stmt->bindParam(2, $id);
    if ($stmt->execute()) {
        header("Location: ?id=$id");
    } else {
        echo "Error al actualizar el saldo";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <div class= "container1">

    <h1>Datos del usuario</h1>
    <p class="info">Nombre:
       <?php echo $name ?>
    </p>
    <p class="info">Apellido:
        <?php echo $surname ?>
    </p>
    <p class="info">Saldo:
        <?php echo $saldo ?>
    </p>
    <div id="content" style="display: <?php echo $showForm ? 'block' : 'none'; ?>">
        <form method="GET" action="">
            <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>">
            <label for="accion" class="info">Acción:</label>
            <select name="accion" id="accion">
                <option value="">Seleciona una acción</option>
                <option value="ingresar">Ingresar</option>
                <option value="retirar">Retirar</option>
            </select><br><br>
            <div style="display: none;" id="op" class="option-container">
                <label for="cantidad" id="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" required><br><br>

                <input type="submit" value="Enviar">
            </div>

        </form>
        <script>
            document.getElementById("accion").onchange = () => {
                if (document.getElementById("op").style.display == "none") {
                    var password = prompt("Por favor, ingrese su contraseña:");
                    if (password == "1234") {
                        document.getElementById("op").style.display = "block";
                    }
                }

            }
        </script>
        </div>

</body>

</html>