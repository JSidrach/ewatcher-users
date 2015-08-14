<?php
  // Form not submitted yet
  if (!isset($_POST['submitForm'])){
    $result = false;
  }
  // Form submitted
  else {
    // Create user, feeds and inputs
    require_once('query.php');
    $result = create_linked_user($_REQUEST['username'], $_REQUEST['email'], $_REQUEST['password'], $_REQUEST['panelType']);
    // Clear the form if the action has completed successfully
    if($result === true) {
      $_REQUEST = array();
    }
  }
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Creación de Usuarios - ISM Solar</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" href="lib/style.css">
    <link rel="stylesheet" type="text/css" href="lib/sweetalert.css">
    <script src="lib/sweetalert.min.js"></script>
  </head>
<body>
  <h1 class="register-title">Introduzca los datos</h1>
  <form class="register" action="#" method="post">
    <div class="register-switch">
      <input type="radio" name="panelType" value="PV" id="P1" class="register-switch-input"
      <?php
        if(isset($_REQUEST['panelType'])) {
          if($_REQUEST['panelType'] === 'PV') {
            echo 'checked';
          }
        } else {
          echo 'checked';
        }
      ?>
      >
      <label for="P1" title="Autoconsumo Fotovoltaico" class="register-switch-label">FV</label>
      <input type="radio" name="panelType" value="Consumption" id="P2" class="register-switch-input"
      <?php
        if(isset($_REQUEST['panelType']) && $_REQUEST['panelType'] === 'Consumption') {
          echo 'checked';
        }
      ?>
      >
      <label for="P2" title="Consumo Eléctrico" class="register-switch-label">Consumo</label>
    </div>
    <input type="username" name="username" class="register-input" placeholder="Nombre del usuario" <?php if(isset($_REQUEST['username'])) echo 'value="' . $_REQUEST['username']. '"'; ?>>
    <input type="email" name="email" class="register-input" placeholder="Dirección de correo electrónico" <?php if(isset($_REQUEST['email'])) echo 'value="' . $_REQUEST['email']. '"'; ?>>
    <input type="password" name="password" class="register-input" placeholder="Contraseña">
    <input type="submit" name="submitForm" value="Crear Cuenta" class="register-button">
  </form>
  <div class="about">
    <p class="about-author">
      <a href="http://www.ismsolar.com/" target="_blank">ISM Solar</a>
    </p>
  </div>
  <script>
    <?php
      if($result === true) {
        ?>
          swal({
            title: "Éxito",
            text: "Usuario, inputs y feeds creados satisfactoriamente",
            type: "success",
            confirmButtonText: "Continuar"
          });
        <?php
        echo '';
      } else if($result === false) {
        // Show nothing
      } else {
        ?>
          swal({
            title: "Error",
            text: <?php echo '"' . $result . '"' ?>,
            type: "error",
            confirmButtonText: "Continuar"
          });
        <?php
      }
    ?>
  </script>
</body>
</html>
