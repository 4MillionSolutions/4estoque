<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class DateHelpers
{
    public static function formatDate_dmY($value) {
	    return Carbon::parse(str_replace('/', '-', $value))->format('Y-m-d');
    }

    public static function formatDate_ddmmYYYY($value) {
	    return Carbon::parse(str_replace('-', '/', $value))->format('d/m/Y');
    }

    public static function formatFloatValue($value) {
        $value = preg_replace('/\,/', '.', preg_replace('/\./', '', $value));
        return number_format($value, 2, '.', '');
    }

    public static function formatRealFormat($value) {
        return number_format($value, 2, ',', '');
    }

    public function formatarCpfCnpj($numero) {
        // Remove qualquer caractere não numérico
        $numero = preg_replace('/\D/', '', $numero);

        // Verifica se é CPF (11 dígitos)
        if (strlen($numero) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $numero);
        }
        // Verifica se é CNPJ (14 dígitos)
        elseif (strlen($numero) === 14) {
            return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $numero);
        }

        // Retorna o número original se não for CPF nem CNPJ
        return $numero;
    }
}
