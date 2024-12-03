<?php
/**
 *  Cifra el texto plano usando AES-256-CBC con una clave dada.
 *
 *  @param string $data El texto plano que se desea cifrar
 *  @param string $key La clave de cifrado
 *  @return string El texto cifrado en formato base64
 *
 */
function cifrar($data, $key)
{
    $method = 'AES-256-CBC';
    $key = hash('sha256', $key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 *  Descifra el texto plano usando AES-256-CBC con una clave dada.
 *
 *  @param string $data El texto plano que se desea cifrar
 *  @param string $key La clave de cifrado
 *  @return string El texto cifrado en formato base64
 *
 */
function descifrar($data, $key)
{
    $method = 'AES-256-CBC';
    $key = hash('sha256', $key);
    $data = base64_decode($data);
    $iv_length = openssl_cipher_iv_length($method);
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    return openssl_decrypt($encrypted, $method, $key, 0, $iv);
}

// Pruebas

$textoPlano = "hpnw phfe sxiv cagc";
$clave = "pruebas";
$textoCifrado = cifrar($textoPlano, $clave);
echo "Texto Cifrado: " . $textoCifrado . "\n";


$textoDescifrado = descifrar($textoCifrado, $clave);
echo "Texto Descifrado: " . $textoDescifrado;
