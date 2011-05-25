<?php

require_once dirname(__FILE__).'/../lib/Baseboleto_demoActions.class.php';

/**
 * boleto_demo actions.
 * 
 * @package    sfBoletoPlugin
 * @subpackage boleto_demo
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
class boleto_demoActions extends Baseboleto_demoActions
{

  public function executeIndex(sfWebRequest $request)
  {
  }

  public function executePagina(sfWebRequest $request)
  {
    $boletos = new sfBlocoBoleto('HSBC');
    $boletos->setFormatoCarne();
    $boletos->setImagePath('/sfBoletoPlugin/images/');

    // Definindo parâmetros globais
    $parametros_globais = array(
      'LOGO_EMPRESA' => '/sfBoletoPlugin/images/sfBoletoLogo167x50.png',
      'CEDENTE' => 'Minha Empresa Ponto Com Cia Ltda',
      'AGENCIA' => '0999',
      'AGENCIA_CODIGO' => '0999',
      'CODIGO_CEDENTE' => '3999988',
      'ESPECIE' => 'R$',
      'QUANTIDADE' => '1',
      'ACEITE' => '',
      'ESPECIE_DOC' => '',
      'INSTRUCOES1' => '- Conceder desconto de pontualidade de R$ 5,00 para pagamento até a data do vencimento.',
      'INSTRUCOES2' => '- Após o vencimento, cobrar juros diário de R$ 0,20. ',
      'INSTRUCOES3' => '- 10 dias após o vencimento, cobrar valor fixo de R$ 47,00. (Serviços suspensos até o pagamento).',
      'ENDERECO1' => 'Av. Independência',
      'ENDERECO2' => 'Centro - Santana do Livramento - RS',
    );
    $boletos->setParametrosGlobais($parametros_globais);

    // Adicionando dados
    $parametros = array(
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
      'IDENTIFICACAO' => 'Minha Empresa Ponto Com Cia Ltda',
      'CPF_CNPJ' => '01.011.111/0001-01',
      'ENDERECO' => 'Rua da Casa, 159',
      'CIDADE_UF' => 'Santana do Livramento - RS',
    );

    // Adicionando e configurando boleto
    $boletos->addBoleto($parametros);

    // Renderizando
    echo $boletos->renderPagina();

    // Se quiser apenas renderizar um exemplo:
    //echo $boletos->renderPaginaExemplo(5);
    return sfView::NONE;
  }

  public function executeCarne(sfWebRequest $request)
  {
    $boletos = new sfBlocoBoleto('HSBC');
    $boletos->setFormatoCarne();
    $boletos->setImagePath('/sfBoletoPlugin/images/');

    // Definindo parâmetros globais
    $parametros_globais = array(
      'LOGO_EMPRESA' => '/sfBoletoPlugin/images/sfBoletoLogo167x50.png',
      'CEDENTE' => 'Minha Empresa Ponto Com Cia Ltda',
      'AGENCIA' => '0999',
      'AGENCIA_CODIGO' => '0999',
      'CODIGO_CEDENTE' => '3999988',
      'ESPECIE' => 'R$',
      'QUANTIDADE' => '1',
      'ACEITE' => '',
      'ESPECIE_DOC' => '',
      'INSTRUCOES1' => '- Conceder desconto de pontualidade de R$ 5,00 para pagamento até a data do vencimento.',
      'INSTRUCOES2' => '- Após o vencimento, cobrar juros diário de R$ 0,20. ',
      'INSTRUCOES3' => '- 10 dias após o vencimento, cobrar valor fixo de R$ 47,00. (Serviços suspensos até o pagamento).',
      'ENDERECO1' => 'Av. Independência',
      'ENDERECO2' => 'Centro - Santana do Livramento - RS',
    );
    $boletos->setParametrosGlobais($parametros_globais);

    // Adicionando dados
    $parametros = array(
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
      'IDENTIFICACAO' => 'Minha Empresa Ponto Com Cia Ltda',
      'CPF_CNPJ' => '01.011.111/0001-01',
      'ENDERECO' => 'Rua da Casa, 159',
      'CIDADE_UF' => 'Santana do Livramento - RS',
    );

    // Adicionando e configurando boleto
    $boletos->addBoleto($parametros);

    // Renderizando
    echo $boletos->render();

    // Se quiser apenas renderizar um exemplo:
    //echo $carne->renderPaginaExemplo(5);

    return sfView::NONE;
  }

  public function executeBoleto(sfWebRequest $request)
  {
    // Instanciando através da factory
    $boleto = sfBoleto::create('HSBC');

    // caminho (web, e não físico) até as imagens
    $boleto->setImagePath('/sfBoletoPlugin/images/');

    // Parametrizando dados (dados do boleto)
    $parametros = array(
      'LOGO_EMPRESA' => '/sfBoletoPlugin/images/sfBoletoLogo167x50.png',
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

    // Configurar boleto
    $boleto->configurar($parametros);

    // Renderizando
    echo  $boleto->render();

    // Se quiser apenas renderizar um exemplo:
    //echo $boleto->renderExemplo();
    return sfView::NONE;

  }

}
