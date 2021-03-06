<?php declare(strict_types=1);

namespace TheHorhe\ApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiClient implements ApiClientInterface
{
    /**
     * @param MethodInterface $method
     * @return mixed
     */
    public function executeMethod(MethodInterface $method)
    {
        $this->preprocessMethod($method);

        $client = $this->createClient();
        $request = $this->buildRequest($method);

        try {
            $response = $client->send($request, $method->getOptions());
            $this->preprocessResponse($response);

            return $method->processResponse($response);
        } catch (\Throwable $exception) {
            return $method->handleException($exception);
        }

    }

    /**
     * @param MethodInterface $method
     * @return RequestInterface
     */
    protected function buildRequest(MethodInterface $method)
    {
        $request = new Request(
            $method->getHttpMethod(),
            sprintf('%s://%s%s?%s', $method->getScheme(), $method->getHost(), $method->getMethodUrl(), $this->buildQueryString($method->getQueryParameters())),
            $method->getHeaders(),
            $method->getBody()
        );

        return $request;
    }
    
    /**
     * @param array $parametersArray
     * @return string
     */
    protected function buildQueryString($parametersArray)
    {
        $query = http_build_query($parametersArray, '', '&');
        
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
    } 

    /**
     * @return Client
     */
    protected function createClient()
    {
        return new Client();
    }

    /**
     * Methods allows add some common operations, e.g. add body parameter to the body of all methods executed by this client.
     *
     * @param MethodInterface $method
     * @return void
     */
    protected function preprocessMethod(MethodInterface $method)
    {

    }

    /**
     * Use this to modify response before processing
     *
     * @param ResponseInterface $response
     * @return void
     */
    protected function preprocessResponse(ResponseInterface $response)
    {

    }
}
