<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                // verificar que el usuario exista
                $usuario = Usuario::where('email', $auth->email);

                if (!$usuario) {
                    Usuario::setAlerta('error', 'El usuario no existe');
                } else if (!$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El usuario no esta confirmado');
                } else {
                    // el usuario existe
                    if (password_verify($_POST['password'], $usuario->password)) {
                        // inicriar la sesion del usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // redireccionar
                        header('location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'Password Incorrecto');
                    }
                }

                // debuguear($usuario);
            }
        }

        $alertas = Usuario::getAlertas();

        // render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        session_start();
        $_SESSION = [];
        header('location:/');
    }

    public static function crear(Router $router)
    {
        $alertas = [];
        $usuario = new Usuario;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);
                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // hashear el password
                    $usuario->hashPassword();

                    // eliminar password1
                    unset($usuario->password2);

                    // generar el token
                    $usuario->crearToken();

                    // crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    // enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    if ($resultado) {
                        header('location: /mensaje');
                    }
                }
            }
        }

        // render  a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea Tu Cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)) {
                // buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if ($usuario && $usuario->confirmado) {
                    // generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // actualizar el usuario 
                    $usuario->guardar();

                    // enivar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarINstrucciones();

                    // imprimir la alerta
                    Usuario::setAlerta('exito', 'hemos enviado las instrucciones a tu email');
                } else {
                    Usuario::setAlerta('error', ' El usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // muestra la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvidaste tu Contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router)
    {
        $token = s($_GET['token']);
        $mostrar = true;

        if (!$token) header('location: /');

        // identificar el usuario con este token
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'token no valido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // añadiendo el nuevo password
            $usuario->sincronizar($_POST);

            // validar el password
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                // hashear el nuevo password
                $usuario->hashPassword();

                // eliminar el token
                $usuario->token = null;

                // guardar el usuario en la DB
                $resultado = $usuario->guardar();

                // redireccionar
                if ($resultado) {
                    header('location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer tu Contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }

    public static function confirmar(Router $router)
    {
        $token = s($_GET['token']);

        if (!$token) header('location: /');

        // encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'token no valido');
        } else {
            // confirmar al usuario
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);

            // guar dar en la BD
            $usuario->guardar();

            Usuario::setAlerta('exito', 'cuenta confirmada correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar Cuenta',
            'alertas' => $alertas
        ]);
    }
}
