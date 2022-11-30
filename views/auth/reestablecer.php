<div class="cotenedor reestablecer">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu Nueva Contraseña</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <?php if ($mostrar) { ?>

            <form class="formulario" method="POST">
                <div class="campo">
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="Tu Password" name="password" />
                </div>
                <div class="campo">
                    <label for="password2">Repetir Password</label>
                    <input type="password" id="password2" placeholder="Repite Tu Password" name="password2" />
                </div>

                <input type="submit" class="boton" value="Reestablecer Contraseña">
            </form>
            <div class="acciones">
                <a href="/crear">¿Aun no tienes una cuenta? obtener una</a>
                <a href="/olvide">¿Olvidaste tu password?</a>
            </div>
    </div>
    <!--.contenedor-sm-->
</div>

<?php } ?>