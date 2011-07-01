<?php
/**
 * Classe para Geração de Boleto no Symfony através do BoletoPHP
 * Baseado no projeto BoletoPHP http://www.boletophp.com.br/  
 * 
 * @author    Rafael Goulart <rafaelgou@gmail.com>
 * @version   $Revision: 0.1 $
 */
class sfBoletoHSBC extends sfBaseBoleto {

  protected $config = array(
    'css'        => 'hsbc.css',
    'title'      => 'Boleto HSBC',
    'logo_banco' => 'hsbc.jpg',
  );

  public function configurar($parametros = false)
  {

    if($parametros) $this->setParametros($parametros);
    $parametros = $this->getParametros();
//      echo "<pre>";
//      print_r($this->parametros);
//      echo "</pre>";
    $codigobanco = "399";
    $parametros['CODIGO_DO_BANCO_COM_DV'] = $this->_geraCodigoBanco($codigobanco);
    $parametros['CARTEIRA'] = 'CNR';
    $nummoeda         = "9";

    if (true
           //isset($this->parametros["DATA_VENCIMENTO"]) && $this->parametros["DATA_VENCIMENTO"] != ''
           //&& isset($this->parametros["VALOR_BOLETO"]) && $this->parametros["VALOR_BOLETO"] != ''
           //&& isset($this->parametros["CODIGO_CEDENTE"]) && $this->parametros["CODIGO_CEDENTE"] != ''
           //&& isset($this->parametros["NUMERO_DOCUMENTO"]) && $this->parametros["NUMERO_DOCUMENTO"] != ''
       )
    {
      $fator_vencimento = $this->_fatorVencimento($parametros["DATA_VENCIMENTO"]);
      $valor            = $this->_formataNumero($parametros["VALOR_BOLETO"],10,0,"valor");
      $codigocedente    = $this->_formataNumero($parametros["CODIGO_CEDENTE"],7,0);
      $ndoc             = $parametros["NUMERO_DOCUMENTO"];
      $vencimento       = $parametros["DATA_VENCIMENTO"];
      $nnum             = $this->_formataNumero($parametros["NUMERO_DOCUMENTO"],13,0);
      $vencjuliano      = $this->_dataJuliano($vencimento);
      $app              = "2";
      $barra            = "$codigobanco$nummoeda$fator_vencimento$valor$codigocedente$nnum$vencjuliano$app";
      $dv               = $this->_digitoVerificadorBarra($barra, 9, 0);
      
      // Numero para o codigo de barras com 44 digitos
      $linha = substr($barra,0,4) . $dv . substr($barra,4);

      $parametros['AGENCIA_CODIGO']  = $this->parametros['AGENCIA'];
      $parametros['CODIGO_BARRAS']   = $this->formataBarcode($linha);
      $parametros['LINHA_DIGITAVEL'] = $this->montaLinhaDigitavel($linha);
      $parametros['NOSSO_NUMERO']    = $this->geraNossoNumero($nnum,$codigocedente,$vencimento,'4');
    }

    $this->setParametros($parametros);

    parent::configurar($parametros);

  }

  protected function getDadosExemplo()
  {
    $parametros = array(
      //'LINHA_DIGITAVEL' => '39993.94491 21000.000006 00640.100020 3 45680000004449',
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
      'LOGO_EMPRESA' => self::DEFAULT_IMAGE_PATH . self::DEFAULT_LOGO_EMPRESA,
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

    return $parametros;
  }

  protected function geraNossoNumero($ndoc,$cedente,$venc,$tipoid) {
    $ndoc = $ndoc . $this->_modulo11Invertido($ndoc) . $tipoid;
    $venc = substr($venc,0,2).substr($venc,3,2).substr($venc,8,2);
    $res = $ndoc + $cedente + $venc;
    return $ndoc . $this->_modulo11Invertido($res);
  }

  protected function digitoVerificadorNossoNumero($numero) {
    $resto2 = modulo_11($numero, 9, 1);
       $digito = 11 - $resto2;
       if ($digito == 10 || $digito == 11) {
          $dv = 0;
       } else {
          $dv = $digito;
       }
     return $dv;
  }

  public function formataBarcode($codigo_barras){

    $valor = $codigo_barras;
    $fino = 1 ;
    $largo = 3 ;
    $altura = 50 ;

    $barcodes[0] = "00110" ;
    $barcodes[1] = "10001" ;
    $barcodes[2] = "01001" ;
    $barcodes[3] = "11000" ;
    $barcodes[4] = "00101" ;
    $barcodes[5] = "10100" ;
    $barcodes[6] = "01100" ;
    $barcodes[7] = "00011" ;
    $barcodes[8] = "10010" ;
    $barcodes[9] = "01010" ;
    for($f1=9;$f1>=0;$f1--)
    {
      for($f2=9;$f2>=0;$f2--)
      {
        $f = ($f1 * 10) + $f2 ;
        $texto = "" ;
        for($i=1;$i<6;$i++)
        {
          $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
        }
        $barcodes[$f] = $texto;
      }
    }

    //Guarda inicial
    $barra = '<img src="' . $this->getImagePath() . '/p.png" style="width:'.$fino.'px;" alt="p"/>' .
             '<img src="' . $this->getImagePath() . '/b.png" style="width:'.$fino.'px;" alt="b" />' .
             '<img src="' . $this->getImagePath() . '/p.png" style="width:'.$fino.'px;" alt="p" />' .
             '<img src="' . $this->getImagePath() . '/b.png" style="width:'.$fino.'px;" alt="b" />' ;

    $texto = $valor ;
    if((strlen($texto) % 2) <> 0)
    {
      $texto = "0" . $texto;
    }

    // Draw dos dados
    while (strlen($texto) > 0)
    {
      $i = round($this->_esquerda($texto,2));
      $texto = $this->_direita($texto,strlen($texto)-2);
      $f = $barcodes[$i];
      for($i=1;$i<11;$i+=2)
      {
        if (substr($f,($i-1),1) == "0")
        {
          $f1 = $fino ;
        } else {
          $f1 = $largo ;
        }

        $barra .= '<img src="' . $this->getImagePath() . '/p.png" style="width:'.$f1.'px;" alt="p" />';

        if (substr($f,$i,1) == "0")
        {
          $f2 = $fino ;
        } else {
          $f2 = $largo ;
        }

        $barra .= '<img src="' . $this->getImagePath() . '/b.png" style="width:'.$f2.'px;" alt="b" />';
      }
    }

    // Draw guarda final
    $barra .= '<img src="' . $this->getImagePath() . '/p.png" style="width:'.$largo.'px;" alt="p" />';
    $barra .= '<img src="' . $this->getImagePath() . '/b.png" style="width:'.$fino.'px;" alt="b" />';
    $barra .= '<img src="' . $this->getImagePath() . '/p.png" style="width: 1px;" alt="p" />';

    return $barra;

  } //Fim da função


  protected function montaLinhaDigitavel($codigo) {
    // Posição 	Conteúdo
    // 1 a 3    Número do banco
    // 4        Código da Moeda - 9 para Real
    // 5        Digito verificador do Código de Barras
    // 6 a 9    Fator de Vencimento
    // 10 a 19  Valor (8 inteiros e 2 decimais)
    //          Campo Livre definido por cada banco (25 caracteres)
    // 20 a 26  Código do Cedente
    // 27 a 39  Código do Documento
    // 40 a 43  Data de Vencimento em Juliano (mmmy)
    // 44       Código do aplicativo CNR = 2


    // 1. Campo - composto pelo código do banco, código da moéda, as cinco primeiras posições
    // do campo livre e DV (modulo10) deste campo
    $campo1 = substr($codigo,0,4) . substr($codigo,19,5);
    $campo1 = $campo1 . $this->_modulo10($campo1);
    $campo1 = substr($campo1,0,5) . '.' . substr($campo1,5,5);

    // 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
    // e livre e DV (modulo10) deste campo
    $campo2 = substr($codigo,24,2) . substr($codigo,26,8);
    $campo2 = $campo2 . $this->_modulo10($campo2);
    $campo2 = substr($campo2,0,5) . '.' . substr($campo2,5,6);


    // 3. Campo composto pelas posicoes 16 a 25 do campo livre
    // e livre e DV (modulo10) deste campo
    $campo3 = substr($codigo,34,5) . substr($codigo,39,4) . substr($codigo,43,1);
    $campo3 = $campo3 . $this->_modulo10($campo3);
    $campo3 = substr($campo3,0,5) . '.' . substr($campo3,5,6);

    // 4. Campo - digito verificador do codigo de barras
    $campo4 = substr($codigo, 4, 1);

    // 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
    // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
    // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
    $campo5 = substr($codigo, 5, 4) . substr($codigo, 9, 10);

    return "$campo1 $campo2 $campo3 $campo4 $campo5";
  }

}