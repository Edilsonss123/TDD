<?php

namespace Alura\Leilao\Tests\Unit\Service;

use Alura\Leilao\Dao\Leilao as DaoLeilao;
use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviaEmail;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class EncerradorCriandoClassMockTest extends TestCase {

    /** @test */
    public function testEncerarLeiloesComMaisDeUmaSemana2()
    {
        $fiat = new Leilao("Fiat 147", new DateTimeImmutable("8 days september"));
        $saveiro = new Leilao("Saveiro Cross", new DateTimeImmutable("15 days september"));
        $cgStart = new Leilao("CG Start 150", new DateTimeImmutable("08 days october"));

        $leilaiDao = new LeilaoDaoMock();
        $leilaiDao->salva($fiat);
        $leilaiDao->salva($saveiro);
        $leilaiDao->salva($cgStart);
        
        (new Encerrador($leilaiDao, new EnviarEmailMock()))->encerra();
        
        $leiloesFinalizados = $leilaiDao->recuperarFinalizados();

        self::assertCount(2, $leiloesFinalizados);
        self::assertEquals("Fiat 147", $leiloesFinalizados[0]->recuperarDescricao());
        self::assertEquals("Saveiro Cross", $leiloesFinalizados[1]->recuperarDescricao());
    }
    
}

class EnviarEmailMock extends EnviaEmail 
{
    public function notificarTerminoLeilao(Leilao $leilao)
    {
        
    }
}

class LeilaoDaoMock extends DaoLeilao 
{

    public function __construct() {
        parent::__construct(ConnectionCreator::getConnection());
    }

    private $leiloes = [];

    public function salva(Leilao $leilao) : Leilao 
    {
        $this->leiloes[] = $leilao;
        return $leilao;
    }

    public function recuperarFinalizados(): array
    {
        return array_filter($this->leiloes, function(Leilao $leilao) {
            return $leilao->estaFinalizado();
        });
    }

    public function recuperarNaoFinalizados(): array
    {
        return array_filter($this->leiloes, function(Leilao $leilao) {
            return !$leilao->estaFinalizado();
        });
    }

    function atualiza(Leilao $leilao)
    {
        
    }
}