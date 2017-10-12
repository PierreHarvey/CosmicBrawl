<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JouerControllerControllerTest extends WebTestCase
{
    public function testNouvellepartie()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'nouvelle-partie');
    }

    public function testDistribuercartes()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/distribuer-cartes');
    }

    public function testPiocher()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/piocher');
    }

    public function testRevendiquerborne()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/revendiquer-borne');
    }

    public function testAfficherplateau()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/afficher');
    }

}
