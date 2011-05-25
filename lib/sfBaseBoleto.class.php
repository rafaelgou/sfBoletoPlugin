<?php
/**
 * Classe Base para Geração de Boleto no Symfony
 * Baseado no projeto BoletoPHP http://www.boletophp.com.br/
 *
 * @package sfBoleto
 * @author  Rafael Goulart <rafaelgou@gmail.com>
 */
abstract class sfBaseBoleto {

  /**
   * Extensão adicionada ao abrir templates
   */
  const TEMPLATE_EXTENSION   = '.html';

  /**
   * Caracter utilizado para delimitar campos no layout, tipo %VARIAVEL%
   */
  const TEMPLATE_CHAR        = '%';

  /**
   * Folha de estilo básica
   */
  const BASE_STYLESHEET   = 'sf_boleto.css';

  /**
   * Caminho padrão para templates
   */
  const DEFAULT_TEMPLATES_PATH   = '/../templates/';

  /**
   * Caminho padrão para imagens
   */
  const DEFAULT_IMAGE_PATH   = '/sfBoletoPlugin/images/';

  /**
   * Caminho padrão para folhas de estilo
   */
  const DEFAULT_STYLESHEETS_PATH   = '/../css/';

  /**
   * Logo da empresa padrão = logotipo do sfBoleto
   */
  const DEFAULT_LOGO_EMPRESA = 'sfBoletoLogo167x50.png';

  /**
   * Logo da empresa padrão = logotipo do sfBoleto
   */
  const DEFAULT_LOGO_BANCO = 'logo_banco.png';

  /**
   * Informaçãoes Head e Meta
   * @var array
   */
  protected $head = array(
    'title' => 'Symfony Boleto Plugin',
    'metas' => array (),
  );

  /**
   * Corpo do boleto (body)
   * @var array
   */
  protected $body        = array();

  /**
   * Folhas de estilo do boleto
   * @var array
   */
  protected $stylesheets = array();

  /**
   * Caminhos (paths) do boleto, como de imagens, templates, etc.
   * @var array
   */
  protected $paths       = array();

  /**
   * Templates padrão.
   * @var array
   */
  protected $templates   = array(
                                'instrucoes_pagina' => 'instrucoes_pagina',
                                'instrucoes_carne' => 'instrucoes_carne',
                                'recibo_do_sacado_pagina' => 'recibo_do_sacado_pagina',
                                'recibo_do_sacado_carne' => 'recibo_do_sacado_carne',
                                'ficha_de_compensacao_pagina' => 'ficha_de_compensacao_pagina',
                                'ficha_de_compensacao_carne' => 'ficha_de_compensacao_carne'
                                );

  /**
   * Formato (pagina ou carne)
   * @var string
   */
  protected $formato     = 'pagina';

  /**
   * Paramêtros, default string vazia
   * @var array
   */
  protected $parametros = array(
      'LINHA_DIGITAVEL'     => '',
      'CEDENTE'             => '',
      'AGENCIA_CODIGO'      => '',
      'CODIGO_CEDENTE'      => '',
      'ESPECIE'             => '',
      'QUANTIDADE'          => '',
      'NOSSO_NUMERO'        => '',
      'NUMERO_DOCUMENTO'    => '',
      'CPF_CNPJ'            => '',
      'DATA_VENCIMENTO'     => '',
      'DATA_DOCUMENTO'      => '',
      'DATA_PROCESSAMENTO'  => '',
      'VALOR_BOLETO'        => '',
      'DESCONTO_ABATIMENTO' => '',
      'OUTRAS_DEDUCOES'     => '',
      'MORA_MULTA'          => '',
      'OUTROS_ACRESCIMOS'   => '',
      'VALOR_COBRADO'       => '',
      'SACADO'              => '',
      'AVALISTA'            => '',
      'DEMONSTRATIVO1'      => '',
      'DEMONSTRATIVO2'      => '',
      'DEMONSTRATIVO3'      => '',
      'LOGO_EMPRESA'        => '',
      'IDENTIFICACAO'       => '',
      'CPF_CNPJ'            => '',
      'ENDERECO'            => '',
      'CIDADE_UF'           => '',
      'ACEITE'              => '',
      'ESPECIE_DOC'         => '',
      'INSTRUCOES1'         => '',
      'INSTRUCOES2'         => '',
      'INSTRUCOES3'         => '',
      'ENDERECO1'           => '',
      'ENDERECO2'           => '',
    );

  /**
   * Configuração do boleto para cada banco (CSS, Título, Logotipo Banco)
   * @var array
   */
  protected $config = array(
    'css'        => 'sf_boleto.css',
    'title'      => 'sfBoleto',
    'logo_banco' => 'logo_banco.png',
  );

  /**
   * Dados de exemplo - obrigatória definição em classes filhas
   * @return array
   */
  abstract protected function getDadosExemplo();

  /**
   * Construtor da classe
   *
   * @param array  $parametros => array com dados para o boleto
   * @param string $formato    => formato: pagina (padrão) ou carne
   */
  public function __construct($parametros=false, $formato='pagina')
  {

    $this->addHeadMeta('http-equiv="Content-Type" content="text/html; charset=utf-8"');
    $this->addStylesheet(self::BASE_STYLESHEET);

    if ( isset($this->config['css'])
         && isset($this->config['css']) != '')
         $this->addStylesheet($this->config['css']);

    $this->parametros['LOGO_BANCO'] = ( isset($this->config['logo_banco'])
                                        && isset($this->config['logo_banco']) != '')
                                      ? $this->config['logo_banco']
                                      : self::DEFAULT_LOGO_BANCO;

    if ( isset($this->config['title'])
         && isset($this->config['title']) != '')
         $this->setHeadTitle($this->config['title']);


    $this->parametros['LOGO_EMPRESA'] = isset($parametros['LOGO_EMPRESA'])
                                        ? $parametros['LOGO_EMPRESA']
                                        : self::DEFAULT_IMAGE_PATH . '/' .self::DEFAULT_LOGO_EMPRESA;


    $this->setImagePath(self::DEFAULT_IMAGE_PATH);
    $this->setStylesheetPath(dirname(__FILE__) . self::DEFAULT_STYLESHEETS_PATH);
    $this->setTemplatePath(dirname(__FILE__) . self::DEFAULT_TEMPLATES_PATH);
    $this->setFormato($formato);
    if ($parametros) $this->configurar($parametros);
  }

  /**
   * Configuração de parâmetros do boleto
   *
   * @param array  $parametros => array com dados para o boleto
   */
  public function configurar($parametros=array())
  {
    $parametros = ($parametros) ? $parametros : array();
    $this->setParametros($parametros);
  }

  /**
   * Define formato
   *
   * @param string $formato    => formato: pagina (padrão) ou carne
   */
  public function setFormato($formato='pagina')
  {
    if (in_array($formato, array('pagina','carne')))
    {
      $this->formato = $formato;
    } else {
      throw new Exception('Formato de Bloco de Boleto Invalido', 500);
    }
  }

  /**
   * Define formato para Carne
   */
  public function setFormatoCarne()
  {
    $this->setFormato('carne');
  }

  /**
   * Define formato para Página
   */
  public function setFormatoPagina()
  {
    $this->setFormato('pagina');
  }

  /**
   * Define parâmetros
   *
   * @param array  $parametros => array com dados para o boleto
   */
  public function setParametros($parametros)
  {
    if(!is_array($parametros))
    {
      throw new Exception('Esperado um Array para Parâmetros');
    }
    $this->parametros = array_merge($this->parametros, $parametros);
  }

  /**
   * Define um template
   *
   * @param string $id Identificador do template
   * @param string $template Nome do template
   */
  public function setTemplate($id, $template)
  {
    if( ! in_array($id, array_keys($this->templates)) )
    {
      throw new Exception('Template invalido');
    }
    $this->templates[$id] = $template;
  }

  /**
   * Define vários templates através de array
   *
   * @param array  $templates => array ($id => $template)
   */
  public function setTemplates($templates)
  {
    if(!is_array($templates))
    {
      throw new Exception('Esperado um Array para Templates');
    }
    foreach ($templates as $id => $template)
    {
      $this->setTemplate($id, $template);
    }
  }

  /**
   * Recupera um parâmetro
   *
   * @param string $parametro
   * @return mixed
   */
  public function getParametro($parametro)
  {
    return (isset($this->parametros[$parametro])) ? $this->parametros[$parametro] : false;
  }

  /**
   * Recupera array com parâmetros
   *
   * @return array
   */
  public function getParametros()
  {
    return $this->parametros;
  }

  /**
   * Recupera um template com substituição de parâmetros
   *
   * @param string $template   => Identificador do template
   * @param array  $parametros => Array com parâmetros para substituição
   */
  protected function getTemplate($template, $parametros = false) {

    if (!isset($this->templates[$template])) throw new Exception('Template indefinido', 500);

    // Usando exceção para tratar erro de inclusão de arquivo
    try {

      // Montando o diretório até o arquivo
      $dir = $this->getTemplatePath();

      // Proteção contra XSS
      // retirando \, /, ..
      $arquivo = str_replace(
                             array('\\','/','..'),
                             array('','',''),
                             $this->templates[$template]
                             ) . self::TEMPLATE_EXTENSION;

      // Se o arquivo não existe, então dispara exceção
      if (!file_exists($dir.$arquivo )) {

          throw new Exception("Template $dir$arquivo não existe.");

      } else {

        // Recuperando o arquivo para uma variável
        $conteudo = file_get_contents($dir.$arquivo);

        // Definindo parâmetros, se for enviado
        $parametros = ($parametros) ? $parametros : $this->getParametros();
        // Passando por parâmetros para criar
        // arrays para substituição
        foreach ($parametros as $chave => $valor)
        {
          $de[]   = self::TEMPLATE_CHAR . $chave . self::TEMPLATE_CHAR;
          $para[] = $valor;
        }

        $conteudo = str_replace($de, $para, $conteudo);

        return $conteudo;

      }

    } catch (Exception $e) {

        // Montando mensagem de erro
        $erro = '<h1>sfBoleto Erro:</h1><pre>' . $e->getMessage() . "\n" .
                $e->getTraceAsString() . '</pre>' . "\n";

        // Exibindo erro para o usuário e encerrando script
        die($erro);

    }

  }

  /**
   * Adiciona conteúdo ao corpo (body) do boleto
   *
   * @param string $body => conteúdo a ser adicionado
   */
  public function addBody($body)
  {
    $this->body[] = $body;
  }

  /**
   * Recupera corpo (body) do boleto, em formato de array
   *
   * @return array
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * Limpa conteúdo do corpo (body) do boleto
   */
  public function resetBody()
  {
    return $this->body = array();
  }

  /**
   * Renderiza conteúdo do corpo (body) do boleto
   *
   * @return string
   */
  public function renderBody()
  {
    return '  <body class="body_' . $this->formato . '">' . "\n" . implode("\n",$this->getBody()) . '  </body>' . "\n";
  }

  /**
   * Renderiza doctype (topo superior da página XHTML)
   *
   * @return string
   */
  public function renderDoctype()
  {
    return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n".
           '<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">' . "\n";
  }

  /**
   * Renderiza "head" do arquivo XHTML
   *
   * @return string
   */
  public function renderHead()
  {
    $head =
      " <head>\n" .
      $this->renderHeadMetas() .
      $this->renderHeadTitle() .
      $this->renderStylesheets() .
      " </head>\n";
    return $head;
  }

  /**
   * Define título da página
   *
   * @param string $title
   */
  public function setHeadTitle($title)
  {
    $this->head['title']  = $title;
  }

  /**
   * Recupera título da página
   * 
   * @return string
   */
  public function getHeadTitle()
  {
    return $this->head['title'];
  }

  /**
   * Renderiza tag <title> no "head " do arquivo XHTML
   *
   * @return string
   */
  public function renderHeadTitle()
  {
    return '    <title>' . $this->getHeadTitle() . '</title>' . "\n";
  }

  /**
   * Adiciona meta tags
   *
   * @param string $meta
   */
  public function addHeadMeta($meta)
  {
    $this->head['metas'][] = $meta;
  }

  /**
   * Recupera array com meta tags
   *
   * @return array
   */
  public function getHeadMetas()
  {
    return $this->head['metas'];
  }

  /**
   * Limpa meta tags
   */
  public function resetHeadMetas()
  {
    return $this->head['metas'][] = array();
  }

  /*
   * Renderiza tag meta no "head" do arquivo XHTML
   *
   * @return string
   */

  public function renderHeadMetas()
  {
    $metas = '';
    foreach ($this->getHeadMetas() as $meta)
    {
      $metas .= "    <meta $meta />\n";
    }
    return $metas;
  }

  /**
   * Adiciona folhas de estilo
   *
   * @param string $stylesheet
   */
  public function addStylesheet($stylesheet)
  {
    $this->stylesheets[] = $stylesheet;
  }

  /**
   * Recupera array de folhas de estilo
   *
   * @return array
   */
  public function getStylesheets()
  {
    return $this->stylesheets;
  }

  /**
   * Limpa folhas de estilo
   */
  public function resetStylesheets()
  {
    return $this->stylesheets = array();
  }

  /**
   * Renderiza folhas de estilo no "head" do arquivo XHTML
   *
   * @return string
   */
  public function renderStylesheets()
  {
    $stylesheets = '<style type="text/css">' . "\n";
    foreach ($this->getStylesheets() as $stylesheet)
    {
      $stylesheets .= file_get_contents($this->getStylesheetPath() . $stylesheet) . "\n" ;
    }
    $stylesheets .= '</style>' . "\n";
    return $stylesheets;
  }

  /**
   * Define um caminho
   *
   * @param string $type => Tipo de caminho
   * @param string $path => Caminho
   */
  public function setPath ($type, $path)
  {
    $this->paths[$type] = $path;
  }

  /**
   * Recupera um determinado caminho
   *
   * @param string $path
   * @return string
   */
  public function getPath ($type)
  {
    return (isset ($this->paths[$type])) ? $this->paths[$type] : false;
  }

  /**
   * Define caminho para templates
   *
   * @param string $path => Caminho
   */
  public function setTemplatePath ($path)
  {
    $this->setPath('template', $path);
  }

  /**
   * Recupera caminho para templates
   *
   * @return string
   */
  public function getTemplatePath ()
  {
    return $this->getPath('template');
  }

  /**
   * Define caminho para imagens
   *
   * @param string $path => Caminho
   */
  public function setImagePath ($path)
  {
    $this->parametros['IMAGE_PATH'] = $path;
    $this->setPath('image', $path);
  }

  /**
   * Recupera caminho para imagens
   *
   * @return string
   */
  public function getImagePath ()
  {
    return $this->getPath('image');
  }

  /**
   * Define caminho para folhas de estilo
   *
   * @param string $path => Caminho
   */
  public function setStylesheetPath ($path)
  {
    $this->setPath('stylesheet', $path);
  }

  /**
   * Recupera caminho para folhas de estilo
   *
   * @return string
   */
  public function getStylesheetPath ()
  {
    return $this->getPath('stylesheet');
  }

  /**
   * Define Dados de exemplo
   */
  public function setDadosExemplo()
  {
    $this->setParametros($this->getDadosExemplo());
  }

  /**
   * Renderiza instruções para formato página
   *
   * @param  array $parametros
   * @return string
   */
  public function renderInstrucoesPagina($parametros = false)
  {
    return $this->getTemplate('instrucoes_pagina', $parametros);
  }

  /**
   * Renderiza instruções para formato carne
   *
   * @param  array $parametros
   * @return string
   */
  public function renderInstrucoesCarne($parametros = false)
  {
    return $this->getTemplate('instrucoes_carne', $parametros);
  }

  /**
   * Renderiza instruções Recibo do Sacado para formato página
   *
   * @param  array $parametros
   * @return string
   */
  public function renderReciboDoSacadoPagina($parametros = false)
  {
    return $this->getTemplate('recibo_do_sacado_pagina', $parametros);
  }

  /**
   * Renderiza instruções Recibo do Sacado para formato carne
   *
   * @param  array $parametros
   * @return string
   */
  public function renderReciboDoSacadoCarne($parametros = false)
  {
    return $this->getTemplate('recibo_do_sacado_carne', $parametros);
  }

  /**
   * Renderiza instruções Ficha de Compensação para formato página
   *
   * @param  array $parametros
   * @return string
   */
  public function renderFichaDeCompensacaoPagina($parametros = false)
  {
    return $this->getTemplate('ficha_de_compensacao_pagina', $parametros);
  }

  /**
   * Renderiza instruções Ficha de Compensação para formato carne
   *
   * @param  array $parametros
   * @return string
   */
  public function renderFichaDeCompensacaoCarne($parametros = false)
  {
    return $this->getTemplate('ficha_de_compensacao_carne', $parametros);
  }

  /**
   * Renderiza um boleto completo
   *
   * @param  array $parametros
   * @return string
   */
  public function render($parametros = false)
  {
    return
      $this->renderDoctype() .
      $this->renderHead() .
      $this->renderPagina() .
      "</html>";
  }

  /**
   * Renderiza um boleto de exemplo
   *
   * @return string
   */
  public function renderExemplo()
  {
    $this->setDadosExemplo();
    $this->configurar();
    return  $this->render();
  }

  /**
   * Renderiza uma página
   *
   * @param  array $parametros
   * @return string
   */
  public function renderPagina($parametros = false)
  {
    $this->addBody($this->renderInstrucoesPagina($parametros));
    $this->addBody($this->renderReciboDoSacadoPagina($parametros));
    $this->addBody($this->renderFichaDeCompensacaoPagina($parametros));
    return $this->renderBody();
  }

  /**
   * Renderiza página(s) com dados de exemplo
   *
   * @param  array $quantidade_boletos Quantidade de boletos a renderizar
   * @return string
   */
  public function renderPaginaExemplo($quantidade_boletos = 10)
  {
    $this->setFormatoPagina();
    $this->setDadosExemplo();
    $this->resetBody();
    for ($i=1;$i<=$quantidade_boletos;$i++)
    {
      $this->addBody($this->renderInstrucoesPagina());
      $this->addBody($this->renderReciboDoSacadoPagina());
      $this->addBody($this->renderFichaDeCompensacaoPagina());
      $this->addBody('<div style="page-break-after:always;clear;both;width:100%">&nbsp;</div>');
    }

    return
      $this->renderDoctype() .
      $this->renderHead() .
      $this->renderBody() .
      "</html>";
  }

  /**
   * Renderiza um boleem formato Carnê
   *
   * @param  array $parametros
   * @return string
   */
  public function renderCarne($parametros = false)
  {
    $this->setFormatoCarne();
    $this->resetBody();

    $this->addBody($this->renderInstrucoesCarne($parametros));
    $this->addBody($this->renderReciboDoSacadoCarne($parametros));
    $this->addBody($this->renderFichaDeCompensacaoCarne($parametros));
    $this->addBody('<div style="page-break-after:always;clear;both;width:100%">&nbsp;</div>');

    return
      $this->renderDoctype() .
      $this->renderHead() .
      $this->renderBody() .
      "</html>";

  }

  public function renderCarneExemplo($quantidade_boletos = 10)
  {
    $this->setFormatoCarne();
    $this->setDadosExemplo();
    $this->resetBody();
    for ($i=1;$i<=$quantidade_boletos;$i++)
    {
      //$this->addBody($this->renderInstrucoesCarne());
      $this->addBody($this->renderReciboDoSacadoCarne());
      $this->addBody($this->renderFichaDeCompensacaoCarne());
      if ($quantidade_boletos % 2 == 0) $this->addBody('<div style="page-break-after:always;clear;both;width:100%">&nbsp;</div>');
    }

    return
      $this->renderDoctype() .
      $this->renderHead() .
      $this->renderBody() .
      "</html>";
  }

  /**
   * MÉTODOS GENÉRICOS - VÁLIDOS PARA TODOS OS BOLETOS
   * Podem ser sobrescritos para alguma particularidade
   */

  protected function _esquerda($entra,$comp){
    return substr($entra,0,$comp);
  }

  protected function _direita($entra,$comp){
    return substr($entra,strlen($entra)-$comp,$comp);
  }

  protected function _fatorVencimento($data) {
    $data = explode("/",$data);
    $ano = $data[2];
    $mes = $data[1];
    $dia = $data[0];
      return(abs(($this->_dateToDays("1997","10","07")) - ($this->_dateToDays($ano, $mes, $dia))));
  }

  protected function _dateToDays($year,$month,$day) {
      $century = substr($year, 0, 2);
      $year = substr($year, 2, 2);
      if ($month > 2) {
          $month -= 3;
      } else {
          $month += 9;
          if ($year) {
              $year--;
          } else {
              $year = 99;
              $century --;
          }
      }
      return ( floor((  146097 * $century)    /  4 ) +
              floor(( 1461 * $year)        /  4 ) +
              floor(( 153 * $month +  2) /  5 ) +
                  $day +  1721119);
  }

  protected function _modulo10($num) {
      $numtotal10 = 0;
          $fator = 2;

          // Separacao dos numeros
          for ($i = strlen($num); $i > 0; $i--) {
              // pega cada numero isoladamente
              $numeros[$i] = substr($num,$i-1,1);
              // Efetua multiplicacao do numero pelo (falor 10)
              // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
              $temp = $numeros[$i] * $fator;
              $temp0=0;
              foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
              $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
              // monta sequencia para soma dos digitos no (modulo 10)
              $numtotal10 += $parcial10[$i];
              if ($fator == 2) {
                  $fator = 1;
              } else {
                  $fator = 2; // intercala fator de multiplicacao (modulo 10)
              }
          }

          // várias linhas removidas, vide função original
          // Calculo do modulo 10
          $resto = $numtotal10 % 10;
          $digito = 10 - $resto;
          if ($resto == 0) {
              $digito = 0;
          }

          return $digito;

  }

  protected function _modulo11($num, $base=9, $r=0)  {
      /**
       *   Autor:
       *           Pablo Costa <pablo@users.sourceforge.net>
       *
       *   Função:
       *    Calculo do Modulo 11 para geracao do digito verificador
       *    de boletos bancarios conforme documentos obtidos
       *    da Febraban - www.febraban.org.br
       *
       *   Entrada:
       *     $num: string numérica para a qual se deseja calcularo digito verificador;
       *     $base: valor maximo de multiplicacao [2-$base]
       *     $r: quando especificado um devolve somente o resto
       *
       *   Saída:
       *     Retorna o Digito verificador.
       *
       *   Observações:
       *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
       *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
       */

      $soma = 0;
      $fator = 2;

      /* Separacao dos numeros */
      for ($i = strlen($num); $i > 0; $i--) {
          // pega cada numero isoladamente
          $numeros[$i] = substr($num,$i-1,1);
          // Efetua multiplicacao do numero pelo falor
          $parcial[$i] = $numeros[$i] * $fator;
          // Soma dos digitos
          $soma += $parcial[$i];
          if ($fator == $base) {
              // restaura fator de multiplicacao para 2
              $fator = 1;
          }
          $fator++;
      }

      /* Calculo do modulo 11 */
      if ($r == 0) {
          $soma *= 10;
          $digito = $soma % 11;
          if ($digito == 10) {
              $digito = 0;
          }
          return $digito;
      } elseif ($r == 1){
          $resto = $soma % 11;
          return $resto;
      }

  }

  protected function _modulo11Invertido($num)  { // Calculo de Modulo 11 "Invertido" (com pesos de 9 a 2  e não de 2 a 9)
      $ftini = 2;
      $ftfim = 9;
      $fator = $ftfim;
      $soma = 0;

      for ($i = strlen($num); $i > 0; $i--) {
        $soma += substr($num,$i-1,1) * $fator;
        if(--$fator < $ftini) $fator = $ftfim;
      }

      $digito = $soma % 11;
      if($digito > 9) $digito = 0;

      return $digito;
  }

  protected function _geraCodigoBanco($numero)
  {
      $parte1 = substr($numero, 0, 3);
      $parte2 = $this->_modulo11($parte1);
      return $parte1 . "-" . $parte2;
  }

  protected function _dataJuliano($data)
  {
    $dia = (int)substr($data,0,2);
    $mes = (int)substr($data,3,2);
    $ano = (int)substr($data,6,4);
    $dataf = strtotime("$ano/$mes/$dia");
    $datai = strtotime(($ano-1).'/12/31');
    $dias  = (int)(($dataf - $datai)/(60*60*24));
    return str_pad($dias,3,'0',STR_PAD_LEFT).substr($data,9,4);
  }

  protected function _formataNumero($numero,$loop,$insert,$tipo = "geral")
  {
    if ($tipo == "geral") {
      $numero = str_replace(",","",$numero);
      while(strlen($numero)<$loop){
        $numero = $insert . $numero;
      }
    }
    if ($tipo == "valor") {
      /*
      retira as virgulas
      formata o numero
      preenche com zeros
      */
      $numero = str_replace(",","",$numero);
      while(strlen($numero)<$loop){
        $numero = $insert . $numero;
      }
    }
    if ($tipo == "convenio") {
      while(strlen($numero)<$loop){
        $numero = $numero . $insert;
      }
    }
    return $numero;
  }

  protected function _digitoVerificadorBarra($numero)
  {
    $resto = $this->_modulo11($numero, 9, 1);
       if ($resto == 0 || $resto == 1 || $resto == 10) {
          $dv = 1;
       } else {
      $dv = 11 - $resto;
       }
     return $dv;
  }

  /**
   * Returns an underscore-syntaxed version or the CamelCased string.
   * From: symfony sfInflector.class.php
   * 
   * @param  string $camel_cased_word  String to underscore.
   * @return string Underscored string.
   */
  public static function underscore($camel_cased_word)
  {
    $tmp = $camel_cased_word;
    $tmp = str_replace('::', '/', $tmp);
    $tmp = self::pregtr($tmp, array('/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2',
                                    '/([a-z\d])([A-Z])/'     => '\\1_\\2'));

    return strtolower($tmp);
  }

  /**
   * Returns a camelized string from a lower case and underscored string by replaceing slash with
   * double-colon and upper-casing each letter preceded by an underscore.
   * From: symfony sfInflector.class.php
   *
   * @param  string $lower_case_and_underscored_word  String to camelize.
   *
   * @return string Camelized string.
   */
  public static function camelize($lower_case_and_underscored_word)
  {
    $tmp = $lower_case_and_underscored_word;
    $tmp = self::pregtr($tmp, array('#/(.?)#e'    => "'::'.strtoupper('\\1')",
                                    '/(^|_|-)+(.)/e' => "strtoupper('\\2')"));

    return $tmp;
   }

  /**
   * Returns subject replaced with regular expression matchs
   * Fro: symfony sfToolkit.class.php
   *
   * @param mixed $search        subject to search
   * @param array $replacePairs  array of search => replace pairs
   */
  public static function pregtr($search, $replacePairs)
  {
    return preg_replace(array_keys($replacePairs), array_values($replacePairs), $search);
  }


  /*
   * Provê "accessors" - setters e getters
   * Baseado em: sfDoctrineRecord.class.php
   *
   * @param  string $method     The method name.
   * @param  array  $arguments  The method arguments.
   * @return mixed The returned value of the called method.
   */
  public function __call($method, $arguments)
  {
    try {
      if (in_array($verbo = substr($method, 0, 3), array('set', 'get')))
      {
        $nome = strtoupper(self::underscore(substr($method, 3)));
        if ($verbo == 'get' && isset($this->parametros[$nome]))
        {
            return $this->parametros[$nome];
        } elseif ($verbo == 'set') {
            $this->parametros[$nome] = $arguments[0];
        } else {
          throw new Exception("Parametro inexistente $nome", 1);
        }
      } else {
        if (method_exists($this, $method))
        {
          return $this->$method($arguments);
        } else {
          throw new Exception('Metodo inexistente', 1);
        }
        
      }
    } catch(Exception $e) {
      echo "<pre>ERRO: " . $e->getMessage() . "\n" .
           $e->getTraceAsString() . '</pre>';
      exit;
    }
  }

}
?>