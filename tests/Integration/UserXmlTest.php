<?php

namespace Redmine\Tests\Integration;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use Redmine\Exception\MissingParameterException;
use Redmine\Tests\Fixtures\MockClient;
use SimpleXMLElement;

class UserXmlTest extends TestCase
{
    public function testCreateBlank()
    {
        /** @var \Redmine\Api\User */
        $api = MockClient::create()->getApi('user');
        $this->assertInstanceOf('Redmine\Api\User', $api);

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage('Theses parameters are mandatory: `login`, `lastname`, `firstname`, `mail`');

        $api->create();
    }

    public function testCreateComplex()
    {
        /** @var \Redmine\Api\User */
        $api = MockClient::create()->getApi('user');
        $res = $api->create([
            'login' => 'test',
            'firstname' => 'test',
            'lastname' => 'test',
            'mail' => 'test@example.com',
        ]);
        $res = json_decode($res, true);

        $xml = '<?xml version="1.0"?>
<user>
    <login>test</login>
    <lastname>test</lastname>
    <firstname>test</firstname>
    <mail>test@example.com</mail>
</user>';
        $this->assertEquals($this->formatXml($xml), $this->formatXml($res['data']));
    }

    public function testUpdate()
    {
        /** @var \Redmine\Api\User */
        $api = MockClient::create()->getApi('user');
        $res = $api->update(1, [
            'firstname' => 'Raul',
        ]);
        $res = json_decode($res, true);

        $xml = '<?xml version="1.0"?>
<user>
    <id>1</id>
    <firstname>Raul</firstname>
</user>';
        $this->assertEquals($this->formatXml($xml), $this->formatXml($res['data']));
    }

    private function formatXml($xml)
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML((new SimpleXMLElement($xml))->asXML());

        return $dom->saveXML();
    }
}
