<?php

use Illuminate\Support\Str;

/**
 * Remove acentos de uma string
 * @param string  $string : informe uma string que deseja retirar acentos
 * @return string
 */
function removerAcentos($string):string {
    $acentos = [
        'á' => 'a',
        'à' => 'a',
        'ã' => 'a',
        'â' => 'a',
        'ä' => 'a',
        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'í' => 'i',
        'ì' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ó' => 'o',
        'ò' => 'o',
        'õ' => 'o',
        'ô' => 'o',
        'ö' => 'o',
        'ú' => 'u',
        'ù' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ç' => 'c',
        'Á' => 'A',
        'À' => 'A',
        'Ã' => 'A',
        'Â' => 'A',
        'Ä' => 'A',
        'É' => 'E',
        'È' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Í' => 'I',
        'Ì' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ó' => 'O',
        'Ò' => 'O',
        'Õ' => 'O',
        'Ô' => 'O',
        'Ö' => 'O',
        'Ú' => 'U',
        'Ù' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ç' => 'C'
    ];

    return strtr($string, $acentos);
}

/**
 * Converte uma string para slug
 * @param string  $string : informe uma string que deseja converter para slug
 * @param string $separador = "-" : tipo de separador, é opcional, ex: '_' ',' '.' ...
 * @return string : slug
 */
function converteParaSlug($string, string $separador='-'): string {
    return Str::slug($string, $separador);
}

/**
 * Converte nome em e-mail fake
 * @param string  $nome : informe uma string que deseja converter para slug
 * @param string $dominio = "teste.com" : domínio do e-mail, é opcional
 * @return string : e-mail fake
 */
function tornarEmail(string $nome, string $dominio): string {
    return $nome . '@' . $dominio;
}

/**
 * Retorna um array com todas as unidades federativas (UF) do Brasil
 * @return array : Array de UF do Brasil
 */
function ufsBr(): array
{
    return [
        "AC", // Acre
        "AL", // Alagoas
        "AP", // Amapá
        "AM", // Amazonas
        "BA", // Bahia
        "CE", // Ceará
        "DF", // Distrito Federal
        "ES", // Espírito Santo
        "GO", // Goiás
        "MA", // Maranhão
        "MT", // Mato Grosso
        "MS", // Mato Grosso do Sul
        "MG", // Minas Gerais
        "PA", // Pará
        "PB", // Paraíba
        "PR", // Paraná
        "PE", // Pernambuco
        "PI", // Piauí
        "RJ", // Rio de Janeiro
        "RN", // Rio Grande do Norte
        "RS", // Rio Grande do Sul
        "RO", // Rondônia
        "RR", // Roraima
        "SC", // Santa Catarina
        "SP", // São Paulo
        "SE", // Sergipe
        "TO"  // Tocantins
    ];
}


/**
 * Gera CPF válido
 * @return string : retorna um CPF Válido
 */
function gerarCPF() : string
{
    $cpf = [];

    // Gera os primeiros 9 dígitos aleatórios do CPF
    for ($i = 0; $i < 9; $i++) {
        $cpf[$i] = rand(0, 9);
    }

    // Calcula o primeiro dígito verificador
    $cpf[9] = calcularDigitoVerificador($cpf, 10);

    // Calcula o segundo dígito verificador
    $cpf[10] = calcularDigitoVerificador($cpf, 11);

    // Formata o CPF
    return implode('', $cpf);
}


/**
 * Retorna o dígito verificador do CPF, ajustando para os casos em que o resto é menor que 2
 * @return integer : digito verificador
 */
function calcularDigitoVerificador($cpf, $pesoInicial) : int
{
    $soma = 0;
    for ($i = 0; $i < $pesoInicial - 1; $i++) {
        $soma += $cpf[$i] * ($pesoInicial - $i);
    }

    $resto = $soma % 11;

    // Retorna o dígito verificador, ajustando para os casos em que o resto é menor que 2
    return ($resto < 2) ? 0 : 11 - $resto;
}
