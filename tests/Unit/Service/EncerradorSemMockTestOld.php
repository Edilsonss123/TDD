<?php

namespace Alura\Leilao\Tests\Unit\Service;

use Alura\Leilao\Dao\Leilao as DaoLeilao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class EncerradorSemMockTesteOld extends TestCase {

    /** @test */
    public function testEncerarLeiloesComMaisDeUmaSemana1()
    {
    
        $fiat = new Leilao("Fiat 147", new DateTimeImmutable("8 days september"));
        $saveiro = new Leilao("Saveiro Cross", new DateTimeImmutable("15 days september"));

        $leilaiDao = new DaoLeilao();
        $leilaiDao->salva($fiat);
        $leilaiDao->salva($saveiro);
        
        (new Encerrador())->encerra();
        
        $leiloesFinalizados = $leilaiDao->recuperarFinalizados();

        self::assertCount(2, $leiloesFinalizados);
        self::assertEquals("Fiat 147", $leiloesFinalizados[0]->recuperarDescricao());
        self::assertEquals("Saveiro Cross", $leiloesFinalizados[2]->recuperarDescricao());
       
    }
    
    
}