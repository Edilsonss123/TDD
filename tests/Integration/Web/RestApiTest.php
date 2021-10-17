<?php

namespace Alura\Tests\Integration\Web;

use PHPUnit\Framework\TestCase;

class RestApiTest extends TestCase {

    private static $urlRequest = "https://teste123milhas.herokuapp.com"; 

    /** 
     * @test 
     * @dataProvider recuperarTodosVoos
     * @dataProvider recuperarVoosAgrupados
    */
    public function testRecuperarVoosDisponiveis($retorno)
    {
        self::assertCount(15, $retorno["flights"]);
        self::assertIsArray($retorno["flights"]);
        self::assertEquals(200, $retorno["status"]);
    }

    /** 
     * @test 
     * @dataProvider recuperarVoosAgrupados
    */
    public function testRecuperarVoosGrupadosPorEscalaCompativel($retorno)
    {
        self::assertArrayHasKey("groups", $retorno);
        self::assertCount($retorno["totalGroups"], $retorno["groups"]);

        self::assertIsArray($retorno["flights"]);
        self::assertCount($retorno["totalFlights"], $retorno["flights"]);
        
        self::assertEquals(200, $retorno["status"]);
    }


    public function recuperarTodosVoos()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => self::$urlRequest."/api/flight/search"
        ]);
        
        $response = curl_exec($curl);
        $voos = json_decode($response);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            "todos os voos" => [
                ["status" => $httpCode, "flights" => $voos ] 
            ]
        ];
    }

    public function recuperarVoosAgrupados()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => self::$urlRequest."/api/flight/search/group"
        ]);
        
        $response = curl_exec($curl);
        $voosAgrupados = json_decode($response, true);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            "voos agrupados" => [
                array_merge([ "status" => $httpCode ], $voosAgrupados)
            ]
        ];
    }
    
    
}