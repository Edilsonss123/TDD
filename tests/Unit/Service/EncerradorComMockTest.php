<?php

namespace Alura\Leilao\Tests\Unit\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviaEmail;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EncerradorComMockTest extends TestCase {

    private $encerradorLeilao;
    private $lancesLeilao;
    private $enviaEmail;

    public function setUp() : void
    {
        $fiat = new Leilao("Fiat 147", new DateTimeImmutable("8 days september"));
        $saveiro = new Leilao("Saveiro Cross", new DateTimeImmutable("15 days september"));
        $this->lancesLeilao = [
            $fiat, $saveiro
        ];
        /* 
            //se precisar personalizar a criação da classe mock8 
            $leilaiDao = $this->getMockBuilder(LeilaoDao::class)
            ->setConstructorArgs([new PDO()])//chama construtor original
            ->getMock();
            tipos diferentes de dublês de testes: Dummy,    Fake, Stub,    Spy,    Mock
        */

        /** @var LeilaoDao&MockObject */
        $leilaiDao = $this->createMock(LeilaoDao::class);
        $leilaiDao->method('recuperarFinalizados')->willReturn([$fiat, $saveiro]);
        $leilaiDao->method('recuperarNaoFinalizados')->willReturn([$fiat, $saveiro]);
        $leilaiDao->expects($this->exactly(2))//espera que o metodo  ocorra duas chamadas
        ->method('atualiza') //para este metodo
        ->withConsecutive([$fiat], [$saveiro]);//passando estes parametros em ordem consecutivas
        
        /** @var EnviaEmail&MockObject */
        $this->enviaEmail = $this->createMock(EnviaEmail::class);
        $this->encerradorLeilao = new Encerrador($leilaiDao, $this->enviaEmail);

    }
    
    /** @test */
    public function testEncerarLeiloesComMaisDeUmaSemana()
    {
        
        $this->encerradorLeilao->encerra();
        
        self::assertCount(2, $this->lancesLeilao);
        self::assertEquals("Fiat 147", $this->lancesLeilao[0]->recuperarDescricao());
        self::assertEquals("Saveiro Cross", $this->lancesLeilao[1]->recuperarDescricao());
    }

    /** @test */
    public function testProcessarFinalizacaoDeLeilaoComFalhaNaNotificacaoPorEmail()
    {
        $this->enviaEmail->expects($this->exactly(2))
        ->method("notificarTerminoLeilao")
        ->willThrowException(new DomainException("Não foi possível enviar o email"));
        
        $this->encerradorLeilao->encerra();
    }
    

    /** @test */
    public function testNotificarSomenteLeilaoFinalizado()
    {
        $this->enviaEmail->expects($this->exactly(2))
        ->method("notificarTerminoLeilao")
        ->willReturnCallback(function(Leilao $leilao) {
            self::assertTrue($leilao->estaFinalizado());
        });
        
        $this->encerradorLeilao->encerra();
    }
    
    
}