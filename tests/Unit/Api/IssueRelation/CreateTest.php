<?php

namespace Redmine\Tests\Unit\Api\IssueRelation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Redmine\Api\IssueRelation;
use Redmine\Exception\MissingParameterException;
use Redmine\Exception\SerializerException;
use Redmine\Http\HttpClient;
use Redmine\Tests\Fixtures\AssertingHttpClient;

#[CoversClass(IssueRelation::class)]
class CreateTest extends TestCase
{
    /**
     * @dataProvider getCreateData
     */
    #[DataProvider('getCreateData')]
    public function testCreateReturnsCorrectResponse($issueId, $parameters, $expectedPath, $expectedBody, $responseCode, $response, $expectedReturn): void
    {
        $client = AssertingHttpClient::create(
            $this,
            [
                'POST',
                $expectedPath,
                'application/json',
                $expectedBody,
                $responseCode,
                'application/json',
                $response,
            ],
        );

        // Create the object under test
        $api = new IssueRelation($client);

        // Perform the tests
        $return = $api->create($issueId, $parameters);

        $this->assertIsArray($return);
        $this->assertSame($expectedReturn, $return);
    }

    public static function getCreateData(): array
    {
        return [
            'test with minimal parameters' => [
                5,
                ['issue_to_id' => 10],
                '/issues/5/relations.json',
                '{"relation":{"issue_to_id":10,"relation_type":"relates"}}',
                201,
                '{"relation":{}}',
                ['relation' => []],
            ],
        ];
    }

    public function testCreateThrowsExceptionIfResponseContainsEmptyString(): void
    {
        $client = AssertingHttpClient::create(
            $this,
            [
                'POST',
                '/issues/5/relations.json',
                'application/json',
                '{"relation":{"issue_to_id":10,"relation_type":"relates"}}',
                500,
                '',
                '',
            ],
        );

        // Create the object under test
        $api = new IssueRelation($client);

        $this->expectException(SerializerException::class);
        $this->expectExceptionMessage('Catched error "Syntax error" while decoding JSON: ');

        // Perform the tests
        $api->create(5, ['issue_to_id' => 10]);
    }

    public function testCreateThrowsExceptionWithEmptyParameters(): void
    {
        // Create the used mock objects
        $client = $this->createMock(HttpClient::class);

        // Create the object under test
        $api = new IssueRelation($client);

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage('Theses parameters are mandatory: `issue_to_id`');

        // Perform the tests
        $api->create(5);
    }

    /**
     * @dataProvider incompleteCreateParameterProvider
     */
    #[DataProvider('incompleteCreateParameterProvider')]
    public function testCreateThrowsExceptionIfMandatoyParametersAreMissing($parameters): void
    {
        // Create the used mock objects
        $client = $this->createMock(HttpClient::class);

        // Create the object under test
        $api = new IssueRelation($client);

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage('Theses parameters are mandatory: `issue_to_id`');

        // Perform the tests
        $api->create(5, $parameters);
    }

    /**
     * Provider for incomplete create parameters.
     *
     * @return array[]
     */
    public static function incompleteCreateParameterProvider(): array
    {
        return [
            'missing all mandatory parameters' => [
                [],
            ],
            'missing `issue_to_id` parameter' => [
                [
                    'relation_type' => 'relates',
                ],
            ],
        ];
    }
}
