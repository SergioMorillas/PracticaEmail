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
echo "\nContraseña: " . getPass() . "\n";

function mostrarMenu($correo, $asunto, $cuerpo)
{
    $str = "1) Añadir correo en Para\n";
    $str .= "2) Añadir correos en Copia\n";
    $str .= "3) Añadir correos en Copia Oculta\n";
    $str .= "4) Añadir asunto de correo\n";
    $str .= "5) Añadir cuerpo\n";
    $str .= "6) Añadir archivo adjunto\n";
    if (!empty($correo) && !empty($asunto) && !empty($cuerpo)) {
        $str .= "7) Enviar correo\n";
    }
    $str .= "8) Salir\n";
    return $str;
}

$correo = "";
$cc = [];
$bcc = [];
$asunto = "";
$cuerpo = "";
$adjunto = "";

do {
    echo mostrarMenu($correo, $asunto, $cuerpo);

    $respuestaUsuario = readline("\nIndica el número de la opción que necesites: ");
    switch ($respuestaUsuario) {
        case '1':
            $respuesta1 = readline("\nIndica el correo al que deseas enviar: ");
            // Comprobamos que es un correo válido
            if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $respuesta1)) {
                $correo = $respuesta1;
                echo "Correo: $correo guardado\n";
            } else {
                echo "No es un correo válido.\n";
            }
            break;
        case '2':
            $respuesta2 = readline("\nIndica los correos que quieres añadir en copia separados por ',' : ");
            $arrayRepuesta2 = explode(",", $respuesta2);
            // Revisamos que los datos del array sean correos válidos
            foreach ($arrayRepuesta2 as $copia) {
                if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $copia)) {
                    array_push($cc, $copia);
                    echo "Correo en copia: $copia añadido\n";
                } else {
                    echo "No es una lista de correo válida.\n";
                }
            }
            break;
        case '3':
            $respuesta3 = readline("\nIndica los correos que quieres añadir en copia oculta separados por ',' : ");
            $arrayRepuesta3 = explode(",", $respuesta3);

            foreach ($arrayRepuesta3 as $oculta) {
                // Revisamos que los datos del array sean correos válidos
                if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $oculta)) {
                    array_push($bcc, $oculta);
                    echo "Correo en copia oculta: $oculta añadido\n";
                } else {
                    echo "No es una lista de correo válida.\n";
                }
            }
            break;
        case '4':
            $asunto = readline("\nAñade el asunto del correo: ");
            break;
        case '5':
            $cuerpo = readline("\nAñade el cuerpo del correo: ");
            break;
        case '6':
            $respuesta6 = readline("\nIndica el nombre del archivo que quieres enviar, incluyendo la extensión del archivo (Debe estar en la carpeta del proyecto o indicar ruta): ");
            // Comprobamos que el archivo indicado existe 
            if (file_exists($respuesta6)) {
                $adjunto = $respuesta6;
                echo "Archivo adjunto: $respuesta6 añadido\n";
            } else {
                echo "No se ha encontrado el archivo: $respuesta6\n";
            }
        case '7':
            return mandarCorreo($correo, $cc, $bcc, $asunto, $cuerpo, $adjunto);
            break;
        case '8':
            return;
            break;
        default:
            echo "Introduce un número correcto";
    }
} while ($respuestaUsuario != 8);
