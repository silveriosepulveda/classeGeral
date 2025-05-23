<?php

namespace ClasseGeral;

// As classes deste arquivo foram movidas para arquivos separados conforme PSR-4.
// Para manter a compatibilidade, o namespace foi mantido.

class ManipulaValores
{
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

class ManipulaEmails
{

    public $servidor = '';
    public $email = '';
    public $nome = '';
    public $destinatarios = array();
    public $assunto = '';
    public $usuario = '';
    public $senha = '';
    public $autenticar = false;
    public $texto = '';

    public function enviarEmail()
    {
        date_default_timezone_set("America/Sao_Paulo");
        require_once('PHPMailer/class.phpmailer.php');
        //Primeiro setamos o cabe�alho:
        $header = " Content-type: text/html; charset=utf-8\r\n";
        //instanciamos o objeto
        $mail = new PHPMailer();
        $mail->SetLanguage("br");
        $mail->IsMail();
        // Informamos que vamos enviar atrav�s de SMTP
        //$mail->IsSMTP();
        $mail->IsHTML(true);
        // Colocamos o servidor smtp
        $mail->Host = $this->servidor;
        $mail->SMTPAuth = $this->autenticar;
        $mail->Username = $this->usuario;
        $mail->Password = $this->senha;

        $mail->From = $this->email;
        $mail->FromName = $this->nome;
        $mail->Subject = $this->assunto;

        foreach ($this->destinatarios as $key => $val) {
            $mail->AddAddress($val);
        }

        $mail->MsgHTML($this->texto, ' ');

        if ($mail->Send()) {
            return true;
        } else {
            return false;
        }
    }

}


class Combinacoes
{

    /**
     * Funcao que transforma um array em outro array com as combinacoes possiveis
     * do array de entrada
     * @param varchar $array variavel com os valores a serem combinados
     * @param varchar $separador Caracter que separara os valores nas combinacoes de retorno
     * @return array Variavel que retornara um array com as combinacoes
     */
    public function combinacoesPossiveis($array, $separador = '-')
    {
        $retorno = array();
        $tam = sizeof($array);
        require_once 'combinacoes.php';
        $combinatorics = new Math_Combinatorics;
        foreach ($combinatorics->permutations($array, $tam) as $p) {
            $retorno[] = join($separador, $p);
        }
        return $retorno;
    }

}

class ManipulaDatas
{

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

class ManipulaArquivos
{

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



class GerenciaDiretorios
{

    public function criadiretorio($caminho)
    {
        ini_set('display_errors', 1);
        $temp = explode('/', $caminho);
        $diratual = '';
        foreach ($temp as $key => $dir) {
            //echo substr(trim($dir), strlen(trim($dir)) - 1, 1) . "\n";
            $diratual .= $dir . '/';
            if (!is_dir($diratual)) {
                mkdir($diratual, 0777, true);
            }
        }
        return true;
    }

    public function listaarquivosdiretorio($caminho)
    {
        /* Apaga o diret�rio e todo o seu cont�do */
        $extencoes = array('rar', 'jpg', 'jpeg', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'pdf', 'exe');
        $dir = $caminho;
        $dirt = scandir($dir);
        $retorno = array();
//Apagando os arquivos da pasta raiz
        foreach ($dirt as $arq) {
            $arquivo = $dir . $arq;
            if (trim($arq != '.') && trim($arq != '..')) {
                $temp = explode('.', $arq);
                $ext = $temp[sizeof($temp) - 1];
                if (in_array($ext, $extencoes)) {
                    $retorno[] = $arq;
                }
            }
        }
        return ($retorno);
    }

    public function apagadiretorio($caminho)
    {
        /* Apaga o diret�rio e todo o seu cont�do */
        $dir = $caminho;
        $dirt = scandir($dir);

//Apagando os arquivos da pasta raiz
        foreach ($dirt as $arq) {
            $arquivo = $dir . $arq;
            if (is_file($arquivo)) {
                unlink($arquivo);
            } else if (is_dir($arquivo) && trim($arq != '.') && trim($arq != '..')) {
                $dirtam = scandir($arquivo);
                foreach ($dirtam as $imgtam) {
                    $imagem = $arquivo . '/' . $imgtam;
                    if (is_file($imagem))
                        unlink($imagem);
                }
                rmdir($arquivo);
            }
        }
        rmdir($dir);
    }

}

//Tenho que passar destaque e alterar posicao para classe geral posterior mente 18/12/2018//
class Destaque /*extends conexao*/
{
    public function __construct()
    {
        include_once 'bancodedados/conexao.php';
    }

    public function poremdestaque($tabela, $campo_chave, $chave, $valor, $posicao = 0)
    {
        $tabela = strtoupper($tabela);
        $campo_chave = strtoupper($campo_chave);

        $ativo = $valor == 'true' ? 'D' : 'S';

        $pos = new posicoes;

        if ($ativo == 'D') {
            $posicao = $pos->proximaposicao($tabela);
            $sql = "UPDATE $tabela SET DISPONIVEL = '$ativo', POSICAO = $posicao WHERE $campo_chave = $chave";
            $res = $this->executasql($sql);
            if ($res) {
                return $posicao;
            } else {
                return 0;
            }
        } else if ($ativo == 'S') {
            if ($pos->atualizarposicoesexclusao($tabela, $posicao, 'DISPONIVEL', 'D')) {
                $sql = "UPDATE $tabela SET DISPONIVEL = '$ativo', POSICAO = 0 WHERE $campo_chave = $chave";
                $res = $this->executasql($sql);
                if ($res) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 0;
            }
        }
    }

    public function quantidadedestaque($tabela)
    {
        $tabela = strtoupper($tabela);
        $sql = "SELECT COUNT(POSICAO) AS QUANTIDADE FROM $tabela WHERE DISPONIVEL = 'D'";
        $res = $this->executasql($sql);
        $lin = $this->retornosql($res);
        $quantidade = $lin['QUANTIDADE'];
        return $quantidade;
    }

}

class Posicoes /*extends conexao*/
{
    public function alterarposicaonova($tabela, $campo_chave, $chave_primaria, $acrescentar, $pos_atual, $nova_pos, $campo_ctp = 'naousar', $valor_ctp = '')
    {
        $tabela = $this->nometabela($tabela);
        $campo_chave = $campo_chave != '' ? strtoupper($campo_chave) : $this->campochavetabela($tabela);

        $sql = "UPDATE $tabela SET POSICAO = $nova_pos WHERE $campo_chave = $chave_primaria";

        $operador = $acrescentar > 0 ? '+' : '-';

        $sql2 = "UPDATE $tabela SET POSICAO = POSICAO $operador 1";

        if ($acrescentar < 0) {
            $sql2 .= " WHERE POSICAO > $pos_atual AND POSICAO <= $nova_pos";
        } else if ($acrescentar > 0) {
            $sql2 .= " WHERE POSICAO >= $nova_pos AND POSICAO < $pos_atual";
        }

        if ($campo_ctp != 'naousar') {
            $sql_ctp = "SELECT $campo_ctp FROM $tabela WHERE $campo_chave = $chave_primaria";
            $res_ctp = $this->executasql($sql_ctp);
            $lin = $this->retornosql($res_ctp);
            $sql2 .= " AND $campo_ctp = $valor_ctp";
        }
        $sql2 .= " AND $campo_chave != $chave_primaria";

        $res = $this->executasql($sql);
        $res2 = $this->executasql($sql2);

        return 1;
    }

    /**
     * Fun��o que altera as posi��es na tabela indicada
     * @param type $tabela Tabela que sera realizada a alteracao
     * @param type $campo_chave
     * @param type $posicao_atual
     * @param type $nova_posicao
     * @param type $chave_tabela_primaria
     * @param type $valor_ctp
     * @return boolean
     */
    public function alterarposicao($tabela, $campo_chave = '', $posicao_atual = 0, $nova_posicao = 0, $chave_tabela_primaria = '', $valor_ctp = 0)
    {
        $tabela = $this->nometabela($tabela);

        $campo_chave = $campo_chave = '' ? $this->campochavetabela($tabela) : $campo_chave;

        $chave_tabela_primaria = strtoupper($chave_tabela_primaria);

        $chave_pos_atual = $this->pegarchavepelaposicao($tabela, $campo_chave, $posicao_atual, $chave_tabela_primaria, $valor_ctp);

        $chave_pos_nova = $this->pegarchavepelaposicao($tabela, $campo_chave, $nova_posicao, $chave_tabela_primaria, $valor_ctp);

        //Ponho a nova posicao no campo que tem a posi�ao atual
        $sql = "UPDATE $tabela SET POSICAO = $nova_posicao";
        $sql .= " WHERE $campo_chave = $chave_pos_atual";
        if ($valor_ctp > 0) {
            $sql .= " AND $chave_tabela_primaria = $valor_ctp";
        }

        //echo $sql . "\n";

        $res = $this->executasql($sql);
        //Ponho a posi�ao atual no campo que tem a nova posi�ao
        $sql1 = "UPDATE $tabela SET POSICAO = $posicao_atual";
        $sql1 .= " WHERE $campo_chave = $chave_pos_nova";
        if ($valor_ctp > 0) {
            $sql1 .= " AND $chave_tabela_primaria = $valor_ctp";
        }
        //echo $sql1 . "\n";

        $res1 = $this->executasql($sql1);
        return true;
    }

    public function pegarchavepelaposicao($tabela, $campo_chave, $posicao, $chave_tabela_primaria = '', $valor_ctp = 0)
    {
        $tabela = strtoupper($tabela);
        $campo_chave = strtoupper($campo_chave);
        $chave_tabela_primaria = strtoupper($chave_tabela_primaria);


        $sql = "SELECT $campo_chave AS CHAVE FROM $tabela WHERE POSICAO = $posicao";
        if ($valor_ctp > 0)
            $sql .= " AND $chave_tabela_primaria = '$valor_ctp'";
//echo $sql;
        $res = $this->executasql($sql);
        $lin = $this->retornosql($res);
        return $lin['CHAVE'];
    }

    public function proximaposicao($tabela, $campo_tabela_primaria = '', $chave_primaria = 'naousar')
    {
        $tabela = strtoupper($tabela);
        $campo_tabela_primaria = strtoupper($campo_tabela_primaria);
        $campo_chave = $this->campochavetabela($tabela);
//Neste caso h� uma tabela primaria relacionada, tipo MENU_ITENS com MENUS
        if ($chave_primaria != 'naousar') {
            $sql = "SELECT COALESCE(MAX(POSICAO), 0) + 1 AS POSICAO FROM $tabela";
            $sql .= " WHERE $campo_tabela_primaria = '$chave_primaria'";
        } else {
            $sql = "SELECT COALESCE(MAX(POSICAO), 0) + 1 AS POSICAO FROM $tabela";
        }
//echo $sql;
        $res = $this->executasql($sql);

        $lin = $this->retornosql($res);
        return $lin['POSICAO'];
    }

    public function pegarposicao($tabela, $campo_chave, $chave)
    {
        $tabela = strtoupper($tabela);
        $campo_chave = strtoupper($campo_chave);
        $sql = "SELECT POSICAO FROM $tabela WHERE $campo_chave = $chave";

        $res = $this->executasql($sql);
        $lin = $this->retornosql($res);
        return $lin['POSICAO'];
    }

    /**
     *
     * @param type $tabela Tabela que sera alterada
     * @param type $posicao Posicao inicial das alteracoes
     * @param type $campo_tabela_primaria campo_chave_tabela_primaria caso haja
     * @param type $valor_ctp valor da chave da tabela primaria caso haja
     * @return boolean
     */
    public function atualizarposicoesexclusao($tabela, $posicao, $campo_tabela_primaria = '', $valor_ctp = '')
    {
        $tabela = strtoupper($tabela);
        $campo_chave = $this->campochavetabela($tabela);
        $campo_tabela_primaria = strtoupper($campo_tabela_primaria);

        $sql = "UPDATE $tabela SET POSICAO = POSICAO - 1";
        $sql .= " WHERE $campo_chave > 0 AND POSICAO > $posicao";

        if ($valor_ctp != '')
            $sql .= " AND $campo_tabela_primaria = '$valor_ctp'";

        //echo $sql;
        $res = $this->executasql($sql);
        return true;
    }

}

class ManipulaDocumentos
{

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
