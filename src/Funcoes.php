<?php

namespace ClasseGeral;

// As classes deste arquivo foram movidas para arquivos separados conforme PSR-4.
// Para manter a compatibilidade, o namespace foi mantido;

/**
 * Classe utilitária para manipulação de valores, como conversão de números por extenso.
 */
class ManipulaValores
{
    /**
     * Converte um valor numérico para o formato por extenso.
     *
     * @param float|int $valor Valor a ser convertido
     * @param bool $bolExibirMoeda Se deve exibir o nome da moeda
     * @param bool $bolPalavraFeminina Se deve usar palavras no feminino
     * @return string Valor por extenso
     */
    public function valorPorExtenso($valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false)
    {
        $singular = null;
        $plural = null;

        if ($bolExibirMoeda) {
            $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
        } else {
            $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("", "", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
        }

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");


        if ($bolPalavraFeminina) {

            if ($valor == 1) {
                $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
            } else {
                $u = array("", "um", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
            }


            $c = array("", "cem", "duzentas", "trezentas", "quatrocentas", "quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");


        }


        $z = 0;

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);

        for ($i = 0; $i < count($inteiro); $i++) {
            for ($ii = mb_strlen($inteiro[$i]); $ii < 3; $ii++) {
                $inteiro[$i] = "0" . $inteiro[$i];
            }
        }

        // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
        $rt = null;
        $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
        for ($i = 0; $i < count($inteiro); $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = count($inteiro) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000")
                $z++;
            elseif ($z > 0)
                $z--;

            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                $r .= (($z > 1) ? " de " : "") . $plural[$t];

            if ($r)
                $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        $rt = mb_substr($rt, 1);

        return ($rt ? trim($rt) : "zero");

    }

    /**
     * Compara dois valores utilizando o operador especificado.
     *
     * @param float|int $valor1 Primeiro valor a ser comparado
     * @param string $operador Operador de comparação (<, <=, >, >=, =, !=)
     * @param float|int $valor2 Segundo valor a ser comparado
     * @return bool Resultado da comparação
     */
    public function compararValor($valor1, $operador, $valor2)
    {
        switch ($operador) {
            case '<':
                return $valor1 < $valor2;
                break;
            case '<=':
                return $valor1 <= $valor2;
                break;
            case '>':
                return $valor1 > $valor2;
                break;
            case '>=':
                return $valor1 >= $valor2;
                break;
            case '=':
                return $valor1 == $valor2;
                break;
            case '!=':
                return $valor1 != $valor2;
                break;
            default:
                return false;
        }
        return false;
    }
}


/**
 * Classe para manipulação de datas, incluindo formatação, cálculo de diferenças e adição de intervalos.
 */
class ManipulaDatas
{

    /**
     * Converte uma data para o formato por extenso.
     *
     * @param string $data Data a ser convertida
     * @return string Data por extenso
     */
    public function dataExtenso($data)
    {
// leitura das datas
        $temp = explode('/', $data);
        if (sizeof($temp) < 2) {
            return false;
        } else {
            $dia = intval($temp[0]);
            $mes = intval($temp[1]);
            $ano = $temp[2];

            $meses = $this->arraymeses();
            $mes_extenso = $meses[$mes];
            return $dia . ' de ' . $mes_extenso . ' de ' . $ano;
        }
    }

    /**
     * Calcula a diferença em dias entre duas datas.
     *
     * @param string $data_inicial Data inicial
     * @param string $data_final Data final
     * @param string $tipo Tipo de entrada das datas ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @return int|bool Diferença em dias ou false em caso de erro
     */
    public function diferencaEntreDatas($data_inicial, $data_final, $tipo = 'base')
    {
        if ($tipo == 'base') {
            $temp_i = explode('-', $data_inicial);
            $ano_i = intval($temp_i[0]);
            $mes_i = intval($temp_i[1]);
            $dia_i = intval($temp_i[2]);

            $temp_f = explode('-', $data_final);
            $ano_f = intval($temp_f[0]);
            $mes_f = intval($temp_f[1]);
            $dia_f = intval($temp_f[2]);
        } else if ($tipo == 'tela') {
            $temp_i = explode('/', $data_inicial);
            $ano_i = $temp_i[2];
            $mes_i = $temp_i[1];
            $dia_i = $temp_i[0];

            $temp_f = explode('/', $data_final);
            $ano_f = $temp_f[2];
            $mes_f = $temp_f[1];
            $dia_f = $temp_f[0];
        }

        $data_i = mktime(0, 0, 0, $mes_i, $dia_i, $ano_i);
        $data_f = mktime(0, 0, 0, $mes_f, $dia_f, $ano_f);
        $segundos_diferenca = $data_f - $data_i;
        $dias_diferenca = $segundos_diferenca / (60 * 60 * 24);
        return floor($dias_diferenca);
    }

    /**
     * Adiciona uma quantidade de meses a uma data.
     *
     * @param string $data Data de entrada
     * @param int $meses Quantidade de meses a serem adicionados
     * @param string $tipo Tipo de entrada da data ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @param string $tipoRetorno Tipo de saída da data ('tela' para d/m/Y ou 'base' para Y-m-d)
     * @return string Data com os meses adicionados
     */
    public function somarMesesAData($data, $meses, $tipo = 'base', $tipoRetorno = 'tela')
    {
        if ($tipo == 'base') {
            $temp = explode('-', $data);
            $ano = $temp[0];
            $mes = $temp[1];
            $dia = $temp[2];
        } else if ($tipo == 'tela') {
            $temp = explode('/', $data);
            $ano = $temp[2];
            $mes = $temp[1];
            $dia = $temp[0];
        }

        if ($dia > 28) {
            if ($this->validadata($ano . '-' . ($mes + $meses) . '-' . $dia)) {
                $dataRetorno = mktime(0, 0, 0, ($mes + $meses), $dia, $ano);
            } else {
                //Verificando se e mes 12
                if ($meses >= 12) {
                    $anos = intval($meses / 12);
                    $meses = $meses % 12 > 0 ? ($meses % 12) + 1 : 1;

                    if ($this->validadata(($ano + $anos) . '-' . $meses . '-' . $dia)) {
                        $tempU = array(($ano + $anos), $meses, $dia);
                    } else {
                        $tempU = explode('-', $this->ultimoDiaMes(($ano + $anos) . '-' . $meses . '-1'));
                    }
                } else {
                    $tempU = explode('-', $this->ultimoDiaMes($ano . '-' . ($mes + $meses) . '-1'));
                }

                $dataRetorno = mktime(0, 0, 0, $tempU[1], $tempU[2], $tempU[0]);
            }
        } else {
            $dataRetorno = mktime(0, 0, 0, ($mes + $meses), $dia, $ano);
        }

        return $tipoRetorno == 'tela' ? date('d/m/Y', $dataRetorno) : date('Y-m-d', $dataRetorno);

    }

    /**
     * Adiciona uma quantidade de dias a uma data.
     *
     * @param string $data Data de entrada
     * @param int $dias Quantidade de dias a serem adicionados
     * @param string $tipoEntrada Tipo de entrada da data ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @param string $tipoSaida Tipo de saída da data ('tela' para d/m/Y ou 'base' para Y-m-d)
     * @return string Data com os dias adicionados
     */
    public function somarDiasAData($data, $dias, $tipoEntrada = 'base', $tipoSaida = 'tela')
    {
        $data = str_replace("'", '', $data);

        if ($tipoEntrada == 'base') {
            $temp = explode('-', $data);
            $ano = $temp[0];
            $mes = $temp[1];
            $dia = $temp[2];
        } else if ($tipoEntrada == 'tela') {
            $temp = explode('/', $data);
            $ano = $temp[2];
            $mes = $temp[1];
            $dia = $temp[0];
        }

        //echo $dia .'--' . $mes . ' -- ' . $ano;
        $dataRetorno = mktime(0, 0, 0, $mes, $dia + $dias, $ano);

        if ($tipoSaida == 'tela') {
            return date('d/m/Y', $dataRetorno);
        } else if ($tipoSaida == 'base') {
            return date('Y-m-d', $dataRetorno);
        }
    }

    /**
     * Subtrai uma quantidade de dias de uma data.
     *
     * @param string $data Data de entrada
     * @param int $dias Quantidade de dias a serem subtraídos
     * @param string $tipoEntrada Tipo de entrada da data ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @param string $tipoSaida Tipo de saída da data ('tela' para d/m/Y ou 'base' para Y-m-d)
     * @return string Data com os dias subtraídos
     */
    public function subtrairDiasDaData($data, $dias, $tipoEntrada = 'base', $tipoSaida = 'tela')
    {
        $data = str_replace("'", '', $data);

        if ($tipoEntrada == 'base') {
            $temp = explode('-', $data);
            $ano = $temp[0];
            $mes = $temp[1];
            $dia = $temp[2];
        } else if ($tipoEntrada == 'tela') {
            $temp = explode('/', $data);
            $ano = $temp[2];
            $mes = $temp[1];
            $dia = $temp[0];
        }

        //echo $dia .'--' . $mes . ' -- ' . $ano;
        $dataRetorno = mktime(0, 0, 0, $mes, $dia - $dias, $ano);

        if ($tipoSaida == 'tela') {
            return date('d/m/Y', $dataRetorno);
        } else if ($tipoSaida == 'base') {
            return date('Y-m-d', $dataRetorno);
        }
    }

    /**
     * Retorna um array com os meses do ano.
     *
     * @param int $mes Número do mês (1 a 12) para retorno do nome específico, ou 0 para retornar todos os meses
     * @return array|mixed Array com os nomes dos meses ou nome do mês específico
     */
    public function arraymeses($mes = 0)
    {
        $meses = array(
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        );
        if ($mes > 0) {
            return $meses[$mes];
        } else {
            return $meses;
        }
    }

    /**
     * Retorna um array com os dias da semana.
     *
     * @return array Array com os nomes dos dias da semana
     */
    public function arraydiassemana()
    {
        $dias_semana = array(
            0 => 'Domingo',
            1 => 'Segunda',
            2 => 'Terça',
            3 => 'Quarta',
            4 => 'Quinta',
            5 => 'Sexta',
            6 => 'Sábado'
        );
        return $dias_semana;
    }

    /**
     * Retorna o dia da semana para uma determinada data.
     *
     * @param string $data Data a ser verificada
     * @param string $tipo Tipo de entrada da data ('tela' para d/m/Y ou 'base' para Y-m-d)
     * @return array Array contendo a chave do dia da semana e o nome do dia
     */
    public function diadasemana($data, $tipo = 'tela')
    {

        if ($tipo == 'base') {
            $temp = explode('-', $data);
            $ano = $temp[0];
            $mes = $temp[1];
            $dia = $temp[2];
        } else if ($tipo == 'tela') {
            $temp = explode('/', $data);
            $ano = $temp[2];
            $mes = $temp[1];
            $dia = $temp[0];
        }

        $chave = date("w", mktime(0, 0, 0, $mes, $dia, $ano));
        $dias = $this->arraydiassemana();


        $dia = $dias[$chave];
        return array('chave_dia' => $chave, 'dia' => $dia);
    }

    /**
     * Retorna um array com os dias de um mês específico de um ano.
     *
     * @param int $chave_mes Número do mês (1 a 12)
     * @param string $ano Ano desejado (se não informado, será considerado o ano atual)
     * @return array Array contendo o ano, o nome do mês e os dias do mês
     */
    public function arraydiasmes($chave_mes, $ano = '')
    {
        date_default_timezone_set('America/Sao_Paulo');
        $meses = $this->arraymeses();
        $dias_semana = $this->arraydiassemana();

        $ano = $ano === '' ? date('Y') : $ano;
        $retorno['ano'] = $ano;
        $retorno['meses'][$chave_mes]['nome_mes'] = $meses[$chave_mes];

        for ($dia = 1; $dia <= 31; $dia++) {
            if (checkdate($chave_mes, $dia, $ano)) {
                $chave_dia_semana = date('w', mktime(0, 0, 0, $chave_mes, $dia, $ano));
                $retorno['meses'][$chave_mes]['dias'][$dia] = $dias_semana[$chave_dia_semana];
            }
        }
        return $retorno;
    }

    /**
     * Retorna um array com os dias de todos os meses de um ano.
     *
     * @param string $ano Ano desejado (se não informado, será considerado o ano atual)
     * @return array Array contendo o ano, os meses e os dias de cada mês
     */
    public function arraydiasmeses($ano = '')
    {
        date_default_timezone_set('America/Sao_Paulo');
        $ano = $ano === '' ? date('Y') : $ano;
        $retorno = array();
        $retorno['ano'] = $ano;
        $meses = $this->arraymeses();
        $dias_semana = $this->arraydiassemana();

        for ($chave_mes = 1; $chave_mes <= 12; $chave_mes++) {
            $retorno['meses'][$chave_mes]['nome_mes'] = $meses[$chave_mes];
            for ($dia = 1; $dia <= 31; $dia++) {
                if (checkdate($chave_mes, $dia, $ano)) {
                    $chave_dia_semana = date('w', mktime(0, 0, 0, $chave_mes, $dia, $ano));
                    $retorno['meses'][$chave_mes]['dias'][$dia]['chave_dia_semana'] = $chave_dia_semana;
                    $retorno['meses'][$chave_mes]['dias'][$dia]['dia_semana'] = $dias_semana[$chave_dia_semana];
                }
            }
        }
        return $retorno;
    }

    /**
     * Valida uma data verificando se é uma data real no calendário.
     *
     * @param string $data Data a ser validada
     * @param string $origem Origem da data ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @return bool Resultado da validação
     */
    public function validadata($data, $origem = 'base')
    {
        date_default_timezone_set('America/Sao_Paulo');
        if ($data != '') {
            if ($origem === 'base') {
                $temp = explode('-', $data);
                $d = $temp[2];
                $m = $temp[1];
                $a = $temp[0];
            } else if ($origem === 'tela') {
                $temp = explode('/', $data);
                $d = $temp[0];
                $m = $temp[1];
                $a = $temp[2];
            }

            return checkdate($m, $d, $a);
        } else {
            return false;
        }
    }

    /**
     * Retorna o primeiro dia de um mês a partir de uma data.
     *
     * @param string $data Data de referência
     * @param string $origem Origem da data ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @param string $destino Formato de saída da data ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @return string Primeiro dia do mês no formato desejado
     */
    public function primeiroDiaMes($data, $origem = 'base', $destino = 'base')
    {
        $data = str_replace("'", "", $data);
        if ($origem == 'base') {
            $temp = explode('-', $data);
            $ano = $temp[0];
            $mes = $temp[1];
            echo $ano . ' - ' . $mes . ' - ' . "\n";
        } else if ($origem == 'tela') {
            $temp = explode('/', $data);
            $ano = $temp[2];
            $mes = $temp[1];
        }


        return $destino == 'tela' ? "01/$mes/$ano" : $ano . '-' . $mes . '-01';
    }

    /**
     * Retorna o último dia de um mês a partir de uma data.
     *
     * @param string $data Data de referência
     * @param string $origem Origem da data ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @param string $destino Formato de saída da data ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @return string Último dia do mês no formato desejado
     */
    public function ultimoDiaMes($data, $origem = 'base', $destino = 'base')
    {
        $data = str_replace("'", "", $data);

        if ($origem == 'base') {
            $temp = explode('-', $data);
            $ano = $temp[0];
            $mes = $temp[1];
            $dia = $temp[2];
        } else if ($origem == 'tela') {
            $temp = explode('/', $data);
            $ano = $temp[2];
            $mes = $temp[1];
            $dia = $temp[0];
        }

        $c = 31;
        $dataRetorno = 'va';

        while ($c >= 28) {
            //echo $c . '-' . $mes . '-' . $ano . ' ------ ';
            if ($this->validadata($ano . '-' . $mes . '-' . $c)) {
                $dataRetorno = mktime(0, 0, 0, $mes, $c, $ano);
                return $destino == 'base' ? date('Y-m-d', $dataRetorno) : date('d/m/Y', $dataRetorno);
            }
            $c--;
        }
    }

    /**
     * Retorna o número da semana de uma data.
     *
     * @param string $data Data de referência
     * @param string $origem Origem da data ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @param string $destino Formato de saída da semana ('base' para número da semana ou 'tela' para intervalo de datas)
     * @return mixed Número da semana ou intervalo de datas
     */
    public function numeroSemana($data, $origem = 'base', $destino = 'base')
    {
        $data = str_replace("'", "", $data);

        if ($origem == 'base') {
            $temp = explode('-', $data);
            $ano = $temp[0];
            $mes = $temp[1];
            $dia = $temp[2];
        } else if ($origem == 'tela') {
            $temp = explode('/', $data);
            $ano = $temp[2];
            $mes = $temp[1];
            $dia = $temp[0];
        }

        $date = mktime(0, 0, 0, $mes, $dia, $ano);
        return (int)date('W', $date);
    }

    /**
     * Retorna um array com todas as datas em um intervalo entre duas datas.
     *
     * @param string $dataInicial Data inicial do intervalo
     * @param string $dataFinal Data final do intervalo
     * @param string $tipoEntrada Tipo de entrada das datas ('base' para Y-m-d ou 'tela' para d/m/Y)
     * @param string $tipoSaida Tipo de saída das datas ('tela' para d/m/Y ou 'base' para Y-m-d)
     * @return array Array com as datas do intervalo
     */
    public function arrayDatasIntervalo($dataInicial, $dataFinal, $tipoEntrada = 'base', $tipoSaida = 'tela')
    {
        $diferenca = $this->diferencaEntreDatas($dataInicial, $dataFinal, $tipoEntrada);

        $retorno = [];

        for ($i = 0; $i <= $diferenca; $i++) {

            $retorno[] = $this->somarDiasAData($dataInicial, $i, $tipoEntrada, $tipoSaida);
        }

        return $retorno;

    }


}

/**
 * Classe para manipulação de arquivos.
 */
class ManipulaArquivos
{

    /**
     * Lê o conteúdo de um arquivo e retorna como uma string.
     *
     * @param string $caminho Caminho do arquivo a ser lido
     * @param bool $temaspas Se deve adicionar aspas ao redor do conteúdo
     * @return string Conteúdo do arquivo
     */
    public function arquivoparavariavel($caminho, $temaspas = true)
    {
        $retorno = $temaspas ? '"' : '';
        if (is_file($caminho)) {
            $temp = fopen($caminho, 'r');
            while (($linha = fgets($temp, 4096)) != false) {
                $retorno .= $linha;
            }
        }
        $retorno .= $temaspas ? '"' : '';
        return $retorno;
    }

}




/**
 * Classe para validação e manipulação de documentos, como CNPJ.
 */
class ManipulaDocumentos
{

    /**
     * Valida um CNPJ verificando se é um número de CNPJ válido.
     *
     * @param string $cnpj CNPJ a ser validado
     * @return bool Resultado da validação
     */
    public function valida_cnpj($cnpj)
    {
        // Deixa o CNPJ com apenas n�meros
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Garante que o CNPJ � uma string
        $cnpj = (string)$cnpj;

        // O valor original
        $cnpj_original = $cnpj;

        // Captura os primeiros 12 n�meros do CNPJ
        $primeiros_numeros_cnpj = substr($cnpj, 0, 12);

        /**
         * Multiplica��o do CNPJ
         *
         * @param string $cnpj Os digitos do CNPJ
         * @param int $posicoes A posi��o que vai iniciar a regress�o
         * @return int O
         *
         */
        function multiplica_cnpj($cnpj, $posicao = 5)
        {
            // Vari�vel para o c�lculo
            $calculo = 0;

            // La�o para percorrer os item do cnpj
            for ($i = 0; $i < strlen($cnpj); $i++) {
                // C�lculo mais posi��o do CNPJ * a posi��o
                $calculo = $calculo + ($cnpj[$i] * $posicao);

                // Decrementa a posi��o a cada volta do la�o
                $posicao--;

                // Se a posi��o for menor que 2, ela se torna 9
                if ($posicao < 2) {
                    $posicao = 9;
                }
            }
            // Retorna o c�lculo
            return $calculo;
        }

        // Faz o primeiro c�lculo
        $primeiro_calculo = multiplica_cnpj($primeiros_numeros_cnpj);

        // Se o resto da divis�o entre o primeiro c�lculo e 11 for menor que 2, o primeiro
        // D�gito � zero (0), caso contr�rio � 11 - o resto da divis�o entre o c�lculo e 11
        $primeiro_digito = ($primeiro_calculo % 11) < 2 ? 0 : 11 - ($primeiro_calculo % 11);

        // Concatena o primeiro d�gito nos 12 primeiros n�meros do CNPJ
        // Agora temos 13 n�meros aqui
        $primeiros_numeros_cnpj .= $primeiro_digito;

        // O segundo c�lculo � a mesma coisa do primeiro, por�m, come�a na posi��o 6
        $segundo_calculo = multiplica_cnpj($primeiros_numeros_cnpj, 6);
        $segundo_digito = ($segundo_calculo % 11) < 2 ? 0 : 11 - ($segundo_calculo % 11);

        // Concatena o segundo d�gito ao CNPJ
        $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

        // Verifica se o CNPJ gerado � id�ntico ao enviado
        if ($cnpj === $cnpj_original) {
            return true;
        } else {
            return false;
        }
    }

}
