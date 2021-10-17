<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Leilao;

class EnviaEmail {
    
    public function notificarTerminoLeilao(Leilao $leilao)
    {
        $sucesso = mail(
            "usuario@gmail.com",
            "Leilão finalizado",
            "O leilão para {$leilao->recuperarDescricao()} foi finalizado"
        );
        if (!$sucesso) {
            throw new \DomainException("Não foi possível enviar o email");
            
        }
    }
}