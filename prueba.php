<?php

require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";
require "PHPMailer/src/Exception.php";
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
include_once "cifrarDescifrar.php";

function mandarCorreo($correo, $cc, $bcc, $asunto, $cuerpo, $adjunto)
{
    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "sergiomc11756@gmail.com";
    $mail->Password = getPass();
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = "UTF-8";

    $to = "danielalonsodaw@gmail.com";

    if (!empty($to)) {
        $mail->addAddress($to);
    } else {
        echo "La dirección de correo del destinatario no está definida. ";
    }

// Contenido
    $mail->addcc("sergiomc11756@gmail.com", "sergio");
    $mail->addbcc("sergiomorillas02@gmail.com", "sergio");
    $mail->addAttachment("imagen.png");
    $mail->Subject = "MI ASUNTO PERSONALIZADO";
    $mail->Body = "EL CONTENIDO DE MI CORREO UTILIZANDO PHPMAILERsergio en bccsergio en bccsergio en bccsergio en bcc";

    try {
        $mail->send();
        echo "El mensaje ha sido enviado";
    } catch (Exception $e) {
        echo "El mensaje no se pudo enviar. Mailer Error: {$mail->ErrorInfo}";
    }
}
function getPass()
{
    $file = fopen(".env", "r");
    $cifrada = trim(explode("@", fgets($file))[1]);
    $clave = trim(explode("@", fgets($file))[1]);
    fclose($file);

    echo "\nClave: " . $clave;
    return descifrar($cifrada, $clave);
}
echo "\nContraseña: " . getPass();
