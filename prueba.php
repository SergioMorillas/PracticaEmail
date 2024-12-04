<?php

require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";
require "PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

include_once "cifrarDescifrar.php";
$env =  getEnvVar();
function configurarCorreo()
{
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = getMail();
    $mail->Password = getPass();
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = "UTF-8";

    return $mail;
}

function establecerDestinatario($mail, $correo)
{
    if (!empty($correo)) {
        $mail->addAddress($correo);
    } else {
        echo "La dirección de correo del destinatario no está definida. ";
    }
}

function añadirCopia($mail, $cc)
{
    foreach ($cc as $email) {
        $mail->addCC($email);
    }
}

function añadirCopiaOculta($mail, $bcc)
{
    foreach ($bcc as $email) {
        $mail->addBCC($email);
    }
}

function añadirAdjunto($mail, $adjunto)
{
    if (!empty($adjunto)) {
        $mail->addAttachment($adjunto);
    }
}

function enviarCorreo($mail, $asunto, $cuerpo)
{
    $mail->Subject = $asunto;
    $mail->Body = $cuerpo;

    try {
        $mail->send();
        echo "El mensaje ha sido enviado";
    } catch (Exception $e) {
        echo "El mensaje no se pudo enviar. Mailer Error: {$mail->ErrorInfo}";
    }
}

function mandarCorreo($correo, $cc, $bcc, $asunto, $cuerpo, $adjunto)
{
    $mail = configurarCorreo();
    establecerDestinatario($mail, $correo);
    añadirCopia($mail, $cc);
    añadirCopiaOculta($mail, $bcc);
    añadirAdjunto($mail, $adjunto);
    enviarCorreo($mail, $asunto, $cuerpo);
}

function getMail(){
    global $env;
    return $env["MAIL"];
}
function getPass()
{
    global $env;
    $cifrado = $env["CIFRADO"];
    $clave = $env["CLAVE"];

    return descifrar($cifrado, $clave);
}
function getEnvVar()
{
    global $env;
    $file = fopen(".env", "r");
    while (!feof($file)) {
        $line = fgets($file);
        $env[trim(explode("#", $line)[0])] = trim(explode("#", $line)[1]);
    }
    print_r($env);
    fclose($file);
    return $env;
}

function mostrarMenu($correo, $asunto, $cuerpo)
{
    $str = "1) Añadir correo en Para\n";
    if (!empty($correo)) {
        $str ="1) Añadir correo en Para\n";
    }else{
        $str ="1) Modificar correo en Para\n";  
    }
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
            break;
        case '7':
            return mandarCorreo($correo, $cc, $bcc, $asunto, $cuerpo, $adjunto);
        case '8':
            return;
        default:
            echo "Introduce un número correcto";
    }
} while ($respuestaUsuario != 8);
