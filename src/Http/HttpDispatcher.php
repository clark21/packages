<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http;

/**
 * Express style server class implementation
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
     * @var bool $successful If we were able to process all middleware
     */
    protected $successful = false;
    
    /**
     * We might as well...
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->dispatch();
        } catch (Exception $e) {
        }
        
        return '';
    }
    
    /**
     * Evaluates the response
     * in order to determine the
     * output. Then of course,
     * output it
     *
     * @param Response $response The response object to evaluate
     *
     * @return HttpHandler
     */
    public function output(ResponseInterface $response)
    {
		$code = $response->getStatus();
        $headers = $response->getHeaders();
        $body = $response->getContent(true);
		
		//make sure it's a string
		$body = (string) $body;
		
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
		
		//make sure nothing is already in the buffer
		ob_end_clean();
		
		//close the connection
		header(self::HEADER_CONNECTION_CLOSE);
		
		//add content encoding only if there is none set
		if(!isset($headers['Content-Encoding']) 
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
     * @return array with request and response inside
     */
    public function dispatch(ResponseInterface $response)
    {
		$redirect = $response->getHeaders('Location');
		
		if($redirect) {
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
			
			return $this->redirect($redirect);
		}

		if(!$response->isContentFlat()
			&& !isset($headers['Content-Type']) 
			&& !isset($headers['content-type'])
		) 
		{
			$headers['Content-Type'] = 'text/json';
		}
		
		if(!$response->hasContent()) {
			$response->setStatus(404, '404 Not Found');
			
			//throw an exception
			throw HttpException::forResponseNotFound();
		}
		
		if (!isset($headers['Content-Type']) 
			&& !isset($headers['content-type'])
		) {
            $headers['Content-Type'] = 'text/html; charset=utf-8';
        }

        return $this->output($response);
    }
    
    /**
     * Browser redirect
     *
     * @param *string $path Where to redirect to
     */
    public function redirect($path, $force = false)
    {
		if($force) {
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
     * Returns if we were able to output
     * something
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->successful;
    }
}
