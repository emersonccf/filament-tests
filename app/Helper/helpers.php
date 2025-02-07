<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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


/**
 * Retorna nome de uma pessoa reduzido, no máximo dois nomes no mínimo um nome
 * @param string  $nomeCompleto : informe nome completo de uma pessoa
 * @return string : Retorna o nome reduzido da pessoa Ex. getNomeReduzido("Ana Paula Souza Costa"); // Saída: Ana Costa
 * getNomeReduzido("Maria"); // Saída: Maria ; getNomeReduzido("Carlos Eduardo"); // Saída: Carlos Eduardo
 */
function getNomeReduzido(string $nomeCompleto) : string
{
    // Remove espaços extras no início e no fim da string
    $nomeCompleto = trim($nomeCompleto);

    // Divide o nome em partes
    $partes = explode(' ', $nomeCompleto);

    // Se o nome tiver apenas uma parte, retorna ela mesma
    if (count($partes) == 1) {
        return $partes[0];
    }

    // Caso contrário, retorna o primeiro e o último nome
    $primeiroNome = $partes[0];
    $ultimoNome = $partes[count($partes) - 1];

    return $primeiroNome . ' ' . $ultimoNome;
}

/**
 * Gera um URL completo para um caminho de arquivo, com cache.
 *
 * @param string|null $path O caminho do arquivo
 * @param string $disk O disco de armazenamento (opcional, padrão: 'public')
 * @param int $cacheMinutes Tempo de cache em minutos (opcional, padrão: 1440 - 24 horas)
 * @return string
 */
function full_url(?string $path, string $disk = 'public', int $cacheMinutes = 1440): string
{
    if (empty($path)) {
        return '';
    }

    return Cache::remember("full_url_{$disk}_{$path}", now()->addMinutes($cacheMinutes), function () use ($path, $disk) {
        $url = Storage::disk($disk)->url($path);
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : config('app.url') . $url;
    });
}

/**
 * Retorna só os números de um valor informado.
 *
 * @param string $value texto que pode conter ou não números
 * @return string
 */
function soNumeros(string $value): string
{
    return preg_replace('/[^0-9]/', '', $value);
}


/**
 * Procura a ocorrência de palavras em um texto e retorna true ao encontra pelo menos uma das palavras
 *
 * @param string|array $palavras Uma string ou um array de strings para procurar.
 * @param string $texto O texto onde procurar as palavras.
 * @return bool Retorna true se pelo menos uma palavra for encontrada, false caso contrário.
 */
function encontraPalavras(string|array $palavras, string $texto): bool
{
    // Verifica se $palavras é uma string e converte para array se for
    if (is_string($palavras)) {
        $palavras = [$palavras];
    }

    // Verifica se $palavras é um array
    if (!is_array($palavras)) {
        throw new InvalidArgumentException('O primeiro argumento deve ser uma string ou um array de strings.');
    }

    // Converte o texto para minúsculas para fazer uma busca case-insensitive
    $textoMinusculo = mb_strtolower($texto);

    // Itera sobre cada palavra
    foreach ($palavras as $palavra) {
        // Verifica se a palavra é uma string
        if (!is_string($palavra)) {
            throw new InvalidArgumentException('Todas as palavras devem ser strings.');
        }

        // Converte a palavra para minúsculas
        $palavraMinuscula = mb_strtolower($palavra);

        // Se encontrar a palavra, retorna true imediatamente
        if (mb_strpos($textoMinusculo, $palavraMinuscula) !== false) {
            return true;
        }
    }

    // Se nenhuma palavra for encontrada, retorna false
    return false;
}
