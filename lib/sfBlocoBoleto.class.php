<?php
/**
 * Classe para Geração de Bloco de Boletos no Symfony
 * Baseado no projeto BoletoPHP http://www.boletophp.com.br/
 *
 * @author    Rafael Goulart <rafaelgou@gmail.com>
 * @version   $Revision: 0.1 $
 */
class sfBlocoBoleto {

  protected $boleto_layout;
  protected $boletos;
  protected $formato    = 'pagina';

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
  protected $templates   = array();

  /**
   * Construtor da classe
   *
   * @param string $boleto_layout Layout do boleto
   * @param array  $parametros => array com dados para o boleto
   * @param string $formato    => formato: pagina (padrão) ou carne
   */
  public function __construct($boleto_layout, $parametros=false, $formato='pagina')
  {
    if ($parametros) $this->setParametrosGlobais($parametros);
    $this->boleto_layout = $boleto_layout;
    $this->setFormato($formato);
  }

  /**
   * Definir parâmetro globais para todos boletos
   *
   * @param array  $parametros => array com dados para o boleto
   */
  public function setParametrosGlobais($parametros)
  {
    if(!is_array($parametros))
    {
      throw new Exception('Esperado um Array para Parâmetros');
    }
    $this->parametros = $parametros;
  }

  /**
   * Define formato
   *
   * @param string $formato    => formato: pagina (padrão) ou carne
   */
  protected function setFormato($formato='pagina')
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
   * Retorna quantidade de boletos
   * @return integer
   */
  public function countBoletos()
  {
    return count($this->boletos);
  }

  /**
   * Adiciona um boleto ao bloco, com seus parâmetros
   */
  public function addBoleto($parametros)
  {
    $boleto = sfBoleto::create($this->boleto_layout,array_merge($this->parametros, $parametros));
    $boleto->setFormato($this->formato);
    $boleto->setImagePath($this->getImagePath());
    $this->boletos[] = $boleto;
  }

  /**
   * Renderiza bloco de boletos
   * @return string
   */
  public function render()
  {
    switch ($this->formato) {
      case 'carne':
        return $this->renderCarne();
        break;

      case 'pagina':
      default:
        return $this->renderPagina();
        break;
    }
  }

  /**
   * Renderiza bloco de boletos em formato carnê
   * @return string
   */
  public function renderCarne()
  {
    $body = '  <body class="body_carne">' . "\n";
    $count = 0;
    foreach ($this->boletos as $boleto)
    {
      $count++;
      $boleto->setFormatoCarne();
      foreach ($this->stylesheets as $stylesheet) $boleto->addStyleshets($stylesheet);
      foreach ($this->templates as $template) $boleto->setTemplate($template);
      $boleto->resetBody();
      $boleto->addBody($boleto->renderReciboDoSacadoCarne());
      $boleto->addBody($boleto->renderFichaDeCompensacaoCarne());
      $boleto->addBody('<div class="' . (($count%2) ? 'page_space' : 'page_break' ) . '">&nbsp;</div>');
      $body .=  implode("\n",$boleto->getBody());
    }
    return
      $boleto->renderDoctype() .
      $boleto->renderHead() .
      $body .
      "  </body>\n" .
      "</html>";
  }

  /**
   * Renderiza exemplo de bloco de boletos em formato carnê
   *
   * @param  array $quantidade_boletos Quantidade de boletos a renderizar
   * @return string
   */
  public function renderCarneExemplo($quantidade_boletos = 10)
  {
    $boleto = sfBoleto::create($this->boleto_layout);
    $boleto->setImagePath($this->getImagePath());
    $boleto->setFormatoCarne();
    return $boleto->renderCarneExemplo($quantidade_boletos);
  }

  /**
   * Renderiza bloco de boletos em formato página
   * @return string
   */
  public function renderPagina()
  {
    $body = '';
    foreach ($this->boletos as $boleto)
    {
      $boleto->setFormatoPagina();
      foreach ($this->stylesheets as $stylesheet) $boleto->addStyleshets($stylesheet);
      foreach ($this->templates as $template) $boleto->setTemplate($template);
      $boleto->resetBody();
      $boleto->addBody($boleto->renderInstrucoesPagina());
      $boleto->addBody($boleto->renderReciboDoSacadoPagina());
      $boleto->addBody($boleto->renderFichaDeCompensacaoPagina());
      $boleto->addBody('<div style="page-break-after:always;clear;both;width:100%">&nbsp;</div>');
      $body .= $boleto->renderBody();
    }
    return
      $boleto->renderDoctype() .
      $boleto->renderHead() .
      $body .
      "</html>";
  }

  /**
   * Renderiza exemplo de bloco de boletos em formato página
   *
   * @param  array $quantidade_boletos Quantidade de boletos/páginas a renderizar
   * @return string
   */
  public function renderPaginaExemplo($quantidade_boletos = 10)
  {
    $boleto = sfBoleto::create($this->boleto_layout,$parametros);
    $boleto->setImagePath($this->paths['image']);
    $boleto->setFormatoPagina();
    return $boleto->renderPaginaExemplo($quantidade_boletos);
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

  public function setTemplates($templates)
  {
    if(!is_array($templates))
    {
      throw new Exception('Esperado um Array para Templates');
    }
    foreach ($templates as $template => $valor)
    {
      $this->setTemplate($template, $valor);
    }
  }

}