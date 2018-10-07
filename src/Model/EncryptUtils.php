<?php

namespace App\Model;

class EncryptUtils
{
    const METHOD = 'aes-256-ctr';

    /** @var  string */
    private $appSecret;

    public function __construct($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    /**
     * Encrypt given string
     *
     * @param string $string
     *
     * @return string
     */
    public function encrypt(string $string): string
    {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        $ciphertext = openssl_encrypt(
            $string,
            self::METHOD,
            $this->appSecret,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return base64_encode($nonce . $ciphertext);
    }

    /**
     * Decrypt given string
     *
     * @param string $string
     *
     * @return string
     */
    public function decrypt(string $string): string
    {
        $message = base64_decode($string, true);

        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        $ciphertext = mb_substr($message, $nonceSize, null, '8bit');

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $this->appSecret,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $plaintext;
    }
}