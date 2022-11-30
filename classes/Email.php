<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '4e04ead3b5c2aa';
        $mail->Password = '670b0310cc40c0';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentasa@uptask.com', 'uptask.com');
        $mail->Subject = 'confirma tu cuenta';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en el UpTask solo debes confirmar en el siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/confirmar?token=" . $this->token . "' >Confirmar cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        // enviar el email
        $mail->send();
    }

    public function enviarInstrucciones()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '4e04ead3b5c2aa';
        $mail->Password = '670b0310cc40c0';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentasa@uptask.com', 'uptask.com');
        $mail->Subject = 'reestablece tu password';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Parece que has olvidado tu password, sigue el siguiente enlace para recuperarlo</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/reestablecer?token=" . $this->token . "' >Reestablecer Password</a></p>";
        $contenido .= "<p>Si tu no enviaste esta solicitud, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        // enviar el email
        $mail->send();
    }
}
