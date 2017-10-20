<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JoueurControllerTest extends WebTestCase
{
    public function testMes_parties()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/mes_parties');
    }

}
