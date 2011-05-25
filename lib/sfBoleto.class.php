<?php
/**
 * Classe Factory para Geração de Boleto no Symfony
 * Baseado no projeto BoletoPHP
 *
 * @package   sfBoleto
 * @author    Rafael Goulart <rafaelgou@gmail.com>
 */
class sfBoleto {

  /*
   * Factory method
   * @param string $layout
   * @param array  $parametros
   * @return sfBaseBoleto ==> sfBoleto*LAYOUT* -> extended from sfBaseBoleto
   */
  static public function create($layout, $parametros=false)
  {
 
    // Criando nome da classe
    $classe = "sfBoleto{$layout}";

    // Se classe existe, a retorna
    if (class_exists($classe))
    {

      return new $classe($parametros);

    } else {

      // Classe não existe, tentando carregar seu arquivo
      try
      {

        $arquivo_classe = "{$classe}.class.php";
        require_once(dirname(__FILE__) . "layouts/$arquivo_classe");

        if (class_exists($classe))
        {
          return new $classe($parametros);
        } else {
          throw new Exception('Impossivel carregar classe: ', 500);
        }
        
      } catch (Exception $e) {

        echo 'Impossivel carregar classe: ' . $e->getMessage();
        exit;

      }

    }
  }
}