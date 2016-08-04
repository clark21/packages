<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http;

use Cradle\Http\Dispatcher\DispatcherInterface;
use Cradle\Http\Response\ResponseInterface;

/**
 * This deals with the releasing of content into the
 * main output buffer. Considers headers and post processing
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class HttpDispatcher implements DispatcherInterface
{

    /**
     * @const string BACK The back keyword for redirect
     */
    const BACK = '<back>';
    
    /**
     * @const string HEADER_CONNECTION_CLOSE Template for closing
     */
    const HEADER_CONNECTION_CLOSE = "Connection: close\r\n";
    
    /**
     * @const string HEADER_CONTENT_ENCODING Template for encoding
     */
    const HEADER_CONTENT_ENCODING = "Content-Encoding: none\r\n";
    
    /**
     * @const string HEADER_CONTENT_LENGTH Template for length
     */
    const HEADER_CONTENT_LENGTH = 'Content-Length: %s';

    /**
     * @var bool $successful If we were able to output it
     */
    protected $successful = false;
    
    /**
     * Evaluates the response in order to determine the
     * output. Then of course, output it
     *
     * @param ResponseInterface $response The response object to evaluate
     * @param bool              $emulate  If you really want it to echo (for testing)
     *
     * @return HttpHandler
     */
    public function output(ResponseInterface $response, $emulate = false)
    {
        $code = $response->getStatus();
        $headers = $response->getHeaders();
        $body = $response->getContent(true);
        
        //make sure it's a string
        $body = (string) $body;
        
        if ($emulate) {
            $this->successful = true;
            return $body;
        }
        
        if (is_int($code)) {
            http_response_code($code);
        }
        
        foreach ($headers as $name => $value) {
            if (!$value) {
                header($name);
                continue;
            }
            
            header($name.':'.$value);
        }
        
        //there can be things echoed already
        //let's capture it so we can pass it later
        $trailer = ob_get_contents();

        //make sure nothing is already in the buffer
        ob_end_clean();
        
        //close the connection
        header(self::HEADER_CONNECTION_CLOSE);
        
        //add content encoding only if there is none set
        if (!isset($headers['Content-Encoding'])
            && !isset($headers['content-encoding'])
        ) {
            header(self::HEADER_CONTENT_ENCODING);
        }
        
        //if they were waiting for a response
        //and they hit stop, it should mean that
        //we should also stop
        ignore_user_abort(false);
        
        //startup the buffer again
        ob_start();
        
        //business as usual
        echo $trailer;
        echo $body;
        $this->successful = true;
        
        //send the content size
        $size = ob_get_length();
        header(sprintf(self::HEADER_CONTENT_LENGTH, $size));
        
        //send out the buffer
        //clean up the buffer
        //3 times a charm
        ob_end_flush();
        flush();
        ob_end_clean();
        
        //sorry no more sessions
        session_write_close();
        
        return $this;
    }
    
    /**
     * Starts to process the request
     *
     * @param ResponseInterface $response The response object to evaluate
     * @param bool              $emulate  If you really want it to echo (for testing)
     *
     * @return array with request and response inside
     */
    public function dispatch(ResponseInterface $response, $emulate = false)
    {
        $redirect = $response->getHeaders('Location');
        
        if ($redirect) {
            //if redirect is <BACK>
            if ($redirect === self::BACK) {
                //set up the redirect to something special
                $redirect = 'javascript://history.go(-1)';
                
                //but we prefer the referrer
                $referrer = $response->getServer('HTTP_REFERER');
                
                //we got one ?
                if ($referrer) {
                    //set it
                    $redirect = $referrer;
                }
            }
            
            return $this->redirect($redirect, false, $emulate);
        }

        if (!$response->isContentFlat()) {
            $response->addHeader('Content-Type', 'text/json');
        }
        
        if (!$response->hasContent()) {
            $response->setStatus(404, '404 Not Found');
            
            //throw an exception
            throw HttpException::forResponseNotFound();
        }
        
        if (!$response->getHeaders('Content-Type')) {
            $response->addHeader('Content-Type', 'text/html; charset=utf-8');
        }

        return $this->output($response, $emulate);
    }
    
    /**
     * Browser redirect
     *
     * @param *string $path  Where to redirect to
     * @param bool    $force Whether if you want to exit immediately
     * @param bool    $emulate  If you really want it to redirect (for testing)
     */
    public function redirect($path, $force = false, $emulate = false)
    {
        if ($emulate) {
            return $path;
        }
        
        if ($force) {
            header('Location: ' . $path);
            exit;
        }
        
        //if they were waiting for a response
        //and they hit stop, it should mean that
        //we should also stop
        ignore_user_abort(false);
        
        header('Location: ' . $path);
        
        //close the connection
        header(self::HEADER_CONNECTION_CLOSE);
        
        //add content encoding
        header(self::HEADER_CONTENT_ENCODING);
        
        //add 0 size
        header(sprintf(self::HEADER_CONTENT_LENGTH, 0));
        
        //clean up the buffer
        //2 times a charm
        flush();
        ob_flush();
        
        //sorry no more sessions
        session_write_close();
        
        return $this;
    }
    
    /**
     * Returns true if we were able to output
     * something
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->successful;
    }
}
