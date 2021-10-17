<?php

namespace Alura\Tests\Integration\Dao;

use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\{ Model\Leilao as LeilaoModel, Dao\Leilao as LeilaoDao };
use PDO;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase 
{
    /** @var PDO */
    private static $pdo;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$pdo = ConnectionCreator::getConnection();
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$pdo->beginTransaction();
    }
    
    /**
     * @dataProvider leiloes
     *
     * @return void
     */
    public function testBuscarLeiloesNaoFinalizados($leiloes)
    {
        $leilaoDao = new LeilaoDao(self::$pdo);
        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }

        $leiloesNaoFinalizados = $leilaoDao->recuperarNaoFinalizados();

        self::assertCount(1, $leiloesNaoFinalizados);
        self::assertContainsOnlyInstancesOf(LeilaoModel::class, $leiloesNaoFinalizados);
        self::assertSame("Apt Pampulha", $leiloesNaoFinalizados[0]->recuperarDescricao());
    }

    /**
     * @dataProvider leiloes
     *
     * @return void
     */
    public function testBuscarLeiloesFinalizados(array $leiloes)
    {
        $leilaoDao = new LeilaoDao(self::$pdo);
        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }

        $leiloesFinalizados = $leilaoDao->recuperarFinalizados();

        self::assertCount(1, $leiloesFinalizados);
        self::assertContainsOnlyInstancesOf(LeilaoModel::class, $leiloesFinalizados);
        self::assertSame("Camaro", $leiloesFinalizados[0]->recuperarDescricao());
    }

    /** @test */
    public function testAlterarStatusAoAtualizarLeialo()
    {
        $leilaoDao = new LeilaoDao(self::$pdo);

        $leilao = $leilaoDao->salva(new LeilaoModel("Camaro"));
        $leilao->finaliza();

        $leilaoDao->atualiza($leilao);

        $leiloes = $leilaoDao->recuperarFinalizados();

        self::assertCount(1, $leiloes);
        self::assertEquals('Camaro', $leiloes[0]->recuperarDescricao());

    }
    

    public function leiloes()
    {
        $leilaoNaoFinalizado = new LeilaoModel("Apt Pampulha");
        $leilaoFinalizado = new LeilaoModel("Camaro");
        $leilaoFinalizado->finaliza();

        return [
            "leilões" => [
                [$leilaoNaoFinalizado, $leilaoFinalizado]
            ],
        ];
    }
    
    
    protected function tearDown(): void
    {
        parent::tearDown();
        self::$pdo->rollBack();
    }
    
    
}