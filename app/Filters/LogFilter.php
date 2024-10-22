<?php

namespace App\Filters;

use App\Services\LoggerService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LogFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        $body = $request->getBody();

        // PUT 요청에서 body가 null인 경우, php://input을 사용하여 본문을 읽어옵니다.
        if ($body !== null) {
            if (strpos($contentType, 'application/json') !== false) {
                $bodyArray = json_decode($body, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    parse_str($body, $bodyArray);
                }
            } elseif (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                parse_str($body, $bodyArray);
            }

            if (isset($bodyArray['password'])) {
                $bodyArray['password'] = str_repeat('*', strlen($bodyArray['password']));
            }
            $body = (strpos($contentType, 'application/json') !== false) ? json_encode($bodyArray, JSON_UNESCAPED_UNICODE) : urldecode(http_build_query($bodyArray));
        }
        $data = [
            'type' => 'web',
            'scheme' => $request->getServer('REQUEST_SCHEME') ?? '',
            'host' => $request->getServer('HTTP_HOST') ?? '',
            'path' => $request->getServer('PATH_INFO') ?? '',
            'method' => $request->getMethod() ?? '',
            'query_string' => urldecode($request->getServer('QUERY_STRING')) ?? '',
            'data' => $body,
            'content_type' => $request->getHeaderLine('Content-Type'),
            'remote_addr' => getUserIP(),//$request->getIPAddress(),
            'server_addr' => $request->getServer('SERVER_ADDR'),
            'nickname' => auth()->user()->username ?? ''
        ];

        if ($request->isCLI())
        {
            $data['type'] = 'command';
            $data['command'] = implode(' ', $_SERVER['argv']);
        }

        $logger = new LoggerService();
        $logger->insertLog($data);
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
