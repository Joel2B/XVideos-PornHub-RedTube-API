<?php

// TODO: needs improvements
class Encryption {
    private const ENCRYPTION_ALGORITHM = 'AES-256-CBC';
    private const HASHING_ALGORITHM    = 'sha256';
    private const PASSWORD             = '5ae920dca70e1f969d627b246fcdcda38403e6296c6a6590b34728e058da95ad';

    private const SEPARATOR  = '(';
    private const SEPARATOR2 = ')';
    private $obfuscate;

    private $iv;
    private $key;

    public function __construct( $iv = '', $obfuscate = true ) {
        $this->iv        = $iv;
        $this->key       = hash( self::HASHING_ALGORITHM, self::PASSWORD, true );
        $this->obfuscate = $obfuscate;
    }

    private function rev( $str ) {
        $str = str_replace( '=', '_$_', $str );
        $str = strrev( $str );

        $len  = strlen( $str );
        $len2 = round( $len / 6 );

        for ( $i = 0; $i < $len; $i = $i + $len2 ) {
            $rand  = random_int( 1, 20 );
            $rand2 = random_int( 1, 20 );
            $str   = substr_replace( $str, self::SEPARATOR, $i + $rand, 0 );
            $str   = substr_replace( $str, self::SEPARATOR2, $i + $rand2 + $rand, 0 );
        }
        return '$' . $str;
    }

    private function unrev( $str ) {
        if ( preg_match( '/(\$|\(|\))/', $str ) !== false ) {
            $str = str_replace( ['(', ')', '_$_', '$'], ['', '', '=', ''], $str );
            $str = strrev( $str );
            return $str;
        }
    }

    public function obfuscate( $str ) {
        if ( $this->obfuscate ) {
            $str = $this->rev( $str );
        }
        return $str;
    }

    public function deobfuscate( $str ) {
        if ( $this->obfuscate ) {
            $str = $this->unrev( $str );
        }
        return $str;
    }

    public function encrypt( $str ) {
        $iv = $this->iv;
        if ( $iv == '' ) {
            $iv = random_bytes( 16 );
        } else {
            while ( strlen( $iv ) < 16 ) {
                $iv .= $iv[0];
            }
        }
        $cipherText = openssl_encrypt( $str, self::ENCRYPTION_ALGORITHM, $this->key, OPENSSL_RAW_DATA, $iv );
        $encoded    = base64_encode( $iv . $cipherText );
        return $this->obfuscate( str_replace( array( '+', '/' ), array( '-', '_' ), $encoded ) );
    }

    public function decrypt( $str ) {
        $str = base64_decode( str_replace( array( '-', '_' ), array( '+', '/' ), $this->deobfuscate( $str ) ) );
        if ( strlen( $str ) <= 16 ) {
            return '';
        }
        $iv         = substr( $str, 0, 16 );
        $cipherText = substr( $str, 16 );
        $output     = openssl_decrypt( $cipherText, self::ENCRYPTION_ALGORITHM, $this->key, OPENSSL_RAW_DATA, $iv );
        if ( ! empty( $output ) ) {
            return $output;
        }
    }
}
