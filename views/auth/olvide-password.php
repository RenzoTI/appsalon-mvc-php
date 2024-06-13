<h1 class="nombre-pagina">Olvide Password</h1>
<p class="descripcion-pagina">Restablece tu password escribiendo tu email a continuacion</p>

<?php
    include_once __DIR__ . "/../templates/alertas.php";
 ?>

<form class="formulario" action="/olvide" method="POST">
    <div class="campo">
            <label for="email">E-mail</label>
            <input 
                type="email"
                id="email"
                name="email" 
                placeholder="Tu Email"                       
            >
    </div>

    <input type="submit" value="Enviar Instrucciones" class="boton">

</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión </a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear Una</a>
</div>