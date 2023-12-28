<?php

namespace Redmine\Api;

use Redmine\Exception;
use Redmine\Exception\SerializerException;

/**
 * @see   http://www.redmine.org/projects/redmine/wiki/Rest_Search
 */
class Search extends AbstractApi
{
    private $results = [];

    /**
     * list search results by Query.
     *
     * @see   http://www.redmine.org/projects/redmine/wiki/Rest_Search
     *
     * @param string $query  string to search
     * @param array  $params optional parameters to be passed to the api (offset, limit, ...)
     *
     * @throws SerializerException if response body could not be converted into array
     *
     * @return array list of results (projects, issues)
     */
    final public function listByQuery(string $query, array $params = []): array
    {
        $params['q'] = $query;
        $this->results = $this->retrieveData('/search.json', $params);

        return $this->results;
    }

    /**
     * Search.
     *
     * @deprecated since v2.4.0, use listByQuery() instead.
     *
     * @see   http://www.redmine.org/projects/redmine/wiki/Rest_Search
     *
     * @param string $query  string to search
     * @param array  $params optional parameters to be passed to the api (offset, limit, ...)
     *
     * @return array|string|false list of results (projects, issues) found or error message or false
     */
    public function search($query, array $params = [])
    {
        @trigger_error('`'.__METHOD__.'()` is deprecated since v2.4.0, use `'.__CLASS__.'::listByQuery()` instead.', E_USER_DEPRECATED);

        try {
            return $this->listByQuery($query, $params);
        } catch (Exception $e) {
            if ($this->client->getLastResponseBody() === '') {
                return false;
            }

            return $e->getMessage();
        }
    }
}
