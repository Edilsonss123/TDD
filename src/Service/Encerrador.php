<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;

class Encerrador
{
    private $leilaoDao;
    private $email;

    public function __construct(LeilaoDao $leilaoDao, EnviaEmail $email) 
    {
        $this->leilaoDao = $leilaoDao;
        $this->email = $email;
    }

    public function encerra()
    {
        $leiloes = $this->leilaoDao->recuperarNaoFinalizados();

        foreach ($leiloes as $leilao) {
            try {   
                if ($leilao->temMaisDeUmaSemana()) {
                    $leilao->finaliza();
                    $this->leilaoDao->atualiza($leilao);
                    $this->email->notificarTerminoLeilao($leilao);
                }
            } catch (\DomainException $e) {
                error_log($e->getMessage());
            }   
        }
    }
}
