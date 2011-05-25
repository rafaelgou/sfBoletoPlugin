<h1>sfBoleto Exemplos</h1>
<ul>
  <li><?php echo link_to1('Um boleto na página', 'boleto_demo/boleto') ?></li>
  <li><?php echo link_to1('Carnê (5 boletos de exemplo)', 'boleto_demo/carne') ?></li>
  <li><?php echo link_to1('Páginas (5 boletos de exemplo)', 'boleto_demo/pagina') ?></li>
</ul>

<pre>
# Boletos (Brazilian Payment Method) modelo CNAB - sfBoletoPlugin

GitHub: <http://github.com/rafaelgou/sfBoletoPlugin>

IMPORTANTE: ainda não publicado, em estágio ALPHA. Por favor, não faça fork neste estágio!!

## Introdução

No Brasil existe um método de pagamento largamente utilizado por bancos que é o Boleto ou Bloquete de Cobrança.

Existem implementações em várias linguagens, e a mais conhecida em PHP é o projeto BoletoPHP [http://www.boletophp.com.br/].

Este plugin é baseado no projeto BoletoPHP, com uma versão em orientação a objeto e podendo ser utilizado dentro ou fora do symfony

No momento somente migrado o banco HSBC.

## Instalação

*symfony*

Para instalar o plugin num projeto symfony, utilize a linha de comando usual do symfony:

    php symfony plugin:install sfBoletoPlugin

E está feito.

Alternativamente, se você não tem o PEAR instalado, pode baixar o último pacote anexado à pagina wiki deste plugin e extraí-lo no diretório de plugins do projeto.

Neste caso, ative o plugin em seu ProjectConfiguration.class.php.

    class ProjectConfiguration extends sfProjectConfiguration
    {
      public function setup()
      {
        $this->enablePlugins(..., 'sfBoletoPlugin', ...);

Limpe o cache para habilitar ao autoloading encontrar as classes:

    php symfony cc

E, por último, publique os assets.

    php symfony plugin:publish-assets

## Uso

Em uma action, para um boleto:

    // Instanciando através da factory
    $boleto = sfBoleto::create('HSBC');

    // caminho (web, e não físico) até as imagens
    $boleto->setImagePath('/sfBoletoPlugin/images/');

    // Parametrizando dados (dados do boleto)
    $parametros = array(
      'LINHA_DIGITAVEL' => '39993.94491 21000.000006 00640.100020 3 45680000004449',
      'CEDENTE' => 'Minha Empresa Ponto Com Cia Ltda',
      'AGENCIA' => '0999',
      'AGENCIA_CODIGO' => '0999',
      'CODIGO_CEDENTE' => '3999988',
      'ESPECIE' => 'R$',
      'QUANTIDADE' => '1',
      'NOSSO_NUMERO' => '0000000000640848',
      'NUMERO_DOCUMENTO' => '640',
      'CPF_CNPJ' => '01.011.111/0001-01',
      'DATA_VENCIMENTO' => '10/04/2010',
      'DATA_DOCUMENTO' => '30/03/2010',
      'DATA_PROCESSAMENTO' => '29/03/2010',
      'VALOR_BOLETO' => '44,49',
      'DESCONTO_ABATIMENTO' => '',
      'OUTRAS_DEDUCOES' => '',
      'MORA_MULTA' => '',
      'OUTROS_ACRESCIMOS' => '',
      'VALOR_COBRADO' => '',
      'SACADO' => 'João da Silva Silveira',
      'AVALISTA' => 'José da Silva e Silva',
      'DEMONSTRATIVO1' => 'Mensalidade 4/2010',
      'DEMONSTRATIVO2' => 'Ficou bom isso, apesar de tudo',
      'DEMONSTRATIVO3' => 'Não esqueça da minha calói',
      'LOGO_EMPRESA' => '/images/logo_empresa.png',
      'IDENTIFICACAO' => 'Minha Empresa Ponto Com Cia Ltda',
      'CPF_CNPJ' => '01.011.111/0001-01',
      'ENDERECO' => 'Rua da Casa, 159',
      'CIDADE_UF' => 'Santana do Livramento - RS',
      'ACEITE' => '',
      'ESPECIE_DOC' => '',
      'INSTRUCOES1' => '- Conceder desconto de pontualidade de R$ 5,00 para pagamento até a data do vencimento.',
      'INSTRUCOES2' => '- Após o vencimento, cobrar juros diário de R$ 0,20. ',
      'INSTRUCOES3' => '- 10 dias após o vencimento, cobrar valor fixo de R$ 47,00. (Serviços suspensos até o pagamento).',
      'ENDERECO1' => 'Av. Independência',
      'ENDERECO2' => 'Centro - Santana do Livramento - RS',
    );

    // Renderizando e não exibindo o layout do symfony
    echo  $boleto->render();
    return sfView::NONE;


## Outros Layouts

Basicamente um layout possui 3 arquivos:

- lib/layouts/sfBoletoBanco.class.php
- css/banco.css
- web/images/banco.png

Seguindo o exemplo do banco HSBC, fica:

- lib/banco/sfBoletoHSBC.class.php
- css/banco.css
- web/images/banco.png

Se o banco utilizar templates distintos do original, use como modelos os existentes no diretório `templates\`
e sobrescreva o atributo:

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

para definir um conjunto de templates distintos. Opcionalmente você pode sobrescrever o método
`__construct` (com o cuidado de rodar o `parent::__construct($parametros, $formato);' ao final
deste método) e utilizar o método `setTemplate($id, $template)`;


Como o projeto é baseado no BoletoPHP, <http://www.boletophp.com.br/>, para implementar novos layouts
pode-se e deve-se pegar o layout existente no BoletoPHP e ir convertendo as funções em métodos da classe
do layout. Importante: funções genéricas foram implementadas como métodos na classe abstrata sfBaseBoleto,
da qual todos os layouts derivam, então verifique apenas se há modificações. Assim evita-se retrabalho.

Mas fique atento, pois podem haver diferenças sutis entre um layout e outro. Na dúvida, sobrescreva o
método.

Faça um fork no GitHub e solicite pull de novos layouts!
</pre>