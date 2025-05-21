<?php

namespace App\Helpers;

class Formatters
{
    /**
     * Formata um CPF para o padrão 123.456.789-01.
     *
     * @param string $cpf
     * @return string
     */
    public static function formataCpf(string $cpf): string
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se tem 11 dígitos
        if (strlen($cpf) !== 11) {
            return $cpf; // Retorna sem formatação se inválido
        }

        // Aplica a formatação
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }
}
