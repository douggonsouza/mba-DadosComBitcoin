<?php

namespace Douggonsouza\Coincap;

class Coincap
{
  protected $url = null;


  public function __construct(string $url = null)
  {
    $this->setUrl($url);
  }


  public function request(string $url = null, array $data = null)
  {
    if(isset($url) || !empty($url)){
      $this->setUrl($url);
    }

    $response = $this->curl($this->getUrl(), $data);
    if(!isset($response) || empty($response)){
      return null;
    }

    return json_decode($response);
  }


  private function curl(string $url, array $data = null)
  {
    // Inicializa a sessão cURL
    $curl = curl_init();

    // Define as opções da requisição
    curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL            => $url,
      CURLOPT_FOLLOWLOCATION => 1,
      CURLOPT_MAXREDIRS      => 5
    ]);

    // curl_setopt($curl, CURLOPT_URL, $url);
    // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Define as opções da requisição para os dados
    if(isset($data) && !empty($data)){
      $dataString = json_encode($data);
      curl_setopt_array($curl, [
        CURLOPT_POST       => 1,
        CURLOPT_POSTFIELDS => $dataString,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($dataString)
          )
      ]);
    }

    // Envia a requisição e obtém a resposta
    $response = curl_exec($curl);

    // Fecha a sessão cURL
    curl_close($curl);

    // Exibe a resposta
    return $response;
  }


  public function getUrl()
  {
    return $this->url;
  }


  public function setUrl(string $url)
  {
    if(isset($url) && !empty($url)){
      $this->url = $url;
    }
    return $this;
  }
}
