<?php

declare(strict_types=1);

namespace Redmine\Tests\End2End\Project;

use DateTimeImmutable;
use Redmine\Api\Project;
use Redmine\Tests\End2End\ClientTestCase;
use Redmine\Tests\RedmineExtension\RedmineVersion;

class ProjectTest extends ClientTestCase
{
    public static function getRedmineVersions(): array
    {
        $data = [];

        foreach (static::getAvailableRedmineVersions() as $version) {
            $data[] = [$version];
        }

        return $data;
    }

    /**
     * @dataProvider getRedmineVersions
     */
    public function testInteractionWithProject(RedmineVersion $redmineVersion): void
    {
        $client = $this->getNativeCurlClient($redmineVersion);

        /** @var Project */
        $api = $client->getApi('project');
        $now = new DateTimeImmutable();

        // Create project
        $projectName = 'test project ' . $now->format('Y-m-d H:i:s');
        $projectIdentifier = 'test_project_' . $now->format('Y-m-d_H-i-s');

        $xmlData = $api->create([
            'name' => $projectName,
            'identifier' => $projectIdentifier,
        ]);

        $projectDataJson = json_encode($xmlData);
        $projectData = json_decode($projectDataJson, true);

        $this->assertIsArray($projectData, $projectDataJson);
        $this->assertIsString($projectData['id'], $projectDataJson);
        $this->assertSame($projectName, $projectData['name'], $projectDataJson);
        $this->assertSame($projectIdentifier, $projectData['identifier'], $projectDataJson);

        $projectId = (int) $projectData['id'];

        // List projects
        $projectList = $api->list();

        $this->assertSame(
            [
                'projects',
                'total_count',
                'offset',
                'limit',
            ],
            array_keys($projectList)
        );

        $expected = [
            'projects' => [
                [
                    'id' => $projectId,
                    'name' => $projectName,
                    'identifier' => $projectIdentifier,
                    'description' => null,
                    'homepage' => '',
                    'status' => 1,
                    'is_public' => true,
                    'inherit_members' => false,
                    'created_on' => $projectList['projects'][0]['created_on'],
                    'updated_on' => $projectList['projects'][0]['updated_on'],
                ],
            ],
            'total_count' => 1,
            'offset' => 0,
            'limit' => 25,
        ];

        // field 'homepage' was added in Redmine 5.1.0, see https://www.redmine.org/issues/39113
        if (version_compare($redmineVersion->asString(), '5.1.0', '<')) {
            unset($expected['projects'][0]['homepage']);
        }

        $this->assertSame(
            $expected,
            $projectList
        );
    }
}
