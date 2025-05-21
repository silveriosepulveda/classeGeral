<?php

namespace ClasseGeral;

class ClasseGeral
{
     private $funcoes = "";
    private $mostrarSQLConsulta = false;

    private $paginacao = array(
        'paginasMostrar' => 5,
        'limitePaginaAtiva' => 5,
        'qtdItensRetornados' => 0,
        'pagina' => 1,
        'qtdPaginas' => 1,
        'primeiraPagina' => 1,
        'ultimaPagina' => 10,
        'itensPagina' => 25,
        'itensUltimaPagina' => 0,
    );


    public function formataUrlVideo($url): string
    {
        $retorno = '';
        if (strpos($url, 'watch?v=') > 0)
            $retorno = str_replace('watch?v=', 'embed/', $url);
        else if (strpos($url, 'shorts/'))
            $retorno = str_replace('shorts/', 'embed/', $url);
        return $retorno;
    }
}