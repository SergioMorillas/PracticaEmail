<?php
require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";
require "PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

include_once "cifrarDescifrar.php";
include_once "art.php";

$env =  getEnvVar();
$correo = "";
$cc = [];
$bcc = [];
$asunto = "";
$cuerpo = "";
$adjunto = "";

/**
 * Funcion que crea una instancia de PHPMailer con los parametros definidos por el usuario en el fichero .env y los propios de Gmail, como el tipo de encriptado y el host del servidor SMTP
 * @return PHPMailer Configurado totalmente para poder mandar correos
 */
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
/**
 * Función que establece el destinatario introducido por el usuario en el mail
 * @param PHPMailer $mail El correo previamente configurado en la función @configurarCorreo
 * @param String $correo El correo al que quieres mandar el mensaje
 * @return void muestra un mensaje si el correo no se ha podido establecer
 */
function establecerDestinatario($mail, $correo)
{
    if (!empty($correo)) {
        $mail->addAddress($correo);
    } else {
        echo "La dirección de correo del destinatario no está definida. ";
    }
}
/**
 * Función que establece el cc introducido por el usuario en el mail
 * @param PHPMailer $mail El correo previamente configurado en la función 
 * @param Array $cc El/los correo/s al que quieres mandar el mensaje como CC en formato array
 */
function añadirCopia($mail, $cc)
{
    foreach ($cc as $email) {
        $mail->addCC($email);
    }
}
/**
 * Función que establece el CCO introducido por el usuario en el mail
 * @param PHPMailer $mail El correo previamente configurado en la función 
 * @param Array $bcc El/los correo/s al que quieres mandar el mensaje como CCO en formato array
 */
function añadirCopiaOculta($mail, $bcc)
{
    foreach ($bcc as $email) {
        $mail->addBCC($email);
    }
}
/**
 * Función que establece el adjunto introducido por el usuario en el mail
 * @param PHPMailer $mail El correo previamente configurado en la función 
 * @param String $adjunto La ruta del documento que quieres adjuntar al correo
 */
function añadirAdjunto($mail, $adjunto)
{
    if (!empty($adjunto)) {
        $mail->addAttachment($adjunto);
    }
}

/**
 * Función que envia un correo 
 * @param mixed $mail El mail configurado que has creado previamente
 * @param mixed $asunto El asunto del mensaje
 * @param mixed $cuerpo El cuerpo del mensaje
 * @return void
 */
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
/**
 * Función para mandar el correo, que ejecuta las funciones anteriores para configurar el correo y establecer los distintos parametros que necesita
 * @param mixed $correo El correo
 * @param mixed $cc El/los cc
 * @param mixed $bcc El/los cco
 * @param mixed $asunto El asunto del correo
 * @param mixed $cuerpo El cuerpo del correo
 * @param mixed $adjunto El fichero adjunto del correo
 * @return void
 */
function mandarCorreo($correo, $cc, $bcc, $asunto, $cuerpo, $adjunto)
{
    $mail = configurarCorreo();
    establecerDestinatario($mail, $correo);
    añadirCopia($mail, $cc);
    añadirCopiaOculta($mail, $bcc);
    añadirAdjunto($mail, $adjunto);
    enviarCorreo($mail, $asunto, $cuerpo);
}
/** 
 * 
*/
function getMail(){
    global $env;
    return $env["MAIL"];
}
/** */
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
    fclose($file);
    return $env;
}

function mostrarMenu($correo, $asunto, $cuerpo)
{

    
    echo getTitle();
    echo getCapibara();
    if (!empty($correo)) {
        $str ="1) Modificar correo en Para\n";
    }else{
        $str ="1) Añadir correo en Para\n";
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


do {
    echo mostrarMenu($correo, $asunto, $cuerpo);

    $respuestaUsuario = readline("Indica el número de la opción que necesites: ");

    switch ($respuestaUsuario) {
        case '1':
            $respuesta1 = readline("Indica el correo al que deseas enviar: ");
            // Comprobamos que es un correo válido
            $correo = comprobarCorreo($string);
            break;
        case '2':
            $respuesta2 = readline("Indica los correos que quieres añadir en copia separados por ',' : ");
            $arrayRepuesta2 = explode(",", $respuesta2);
            // Revisamos que los datos del array sean correos válidos
            $copia = comprobarArrayCorreo($arrayRepuesta2);
            $copia?array_push($cc, $copia):"No es una lista de correo válida.\n";
            
            break;
        case '3':
            $respuesta3 = readline("Indica los correos que quieres añadir en copia oculta separados por ',' : ");
            $arrayRepuesta3 = explode(",", $respuesta3);

            // Revisamos que los datos del array sean correos válidos
            $copia = comprobarArrayCorreo($arrayRepuesta3);
            $copia?array_push($bcc, $copia):"No es una lista de correo válida.\n";
            break;
        case '4':
            $asunto = readline("Añade el asunto del correo: ");
            break;
        case '5':
            $cuerpo = readline("Añade el cuerpo del correo: ");
            break;
        case '6':
            $respuesta6 = readline("Indica el nombre del archivo que quieres enviar, incluyendo la extensión del archivo (Debe estar en la carpeta del proyecto o indicar ruta): ");
            //Comprobamos que el archivo indicado existe 
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


function comprobarCorreo($string){
    if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $string)) {
        echo "Correo: $string guardado\n";
        return $string;
    } else {
        echo "No es un correo válido.\n";
    }
}

function comprobarArrayCorreo($array){
    foreach ($array as $copia) {
        if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $copia)) {
            echo "Correo en copia: $copia añadido\n";
        } else {
            return false;
        }
    }
    return true;
}