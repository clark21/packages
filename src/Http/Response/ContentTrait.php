<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http\Response;

/**
 * Designed for the Response Object; Adds methods to process raw content
 *
 * @vendor   Cradle
 * @package  Server
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait ContentTrait
{
	/**
	 * Returns the content body
	 *
	 * @param bool $toString whether to actually make this a string
	 *
	 * @return mixed
	 */
	public function getContent($toString = false)
	{
		$content = $this->get('body');

		if(!$toString) {
			return $content;
		}
		
		//if it's not scalar
        if (!is_scalar($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
		
		$content = (string) $content;
		
		 if (!$content) {
            $content = '';
        }
		
		return $content;
	}
	
	/**
	 * Returns true if content is set
	 *
	 * @return bool
	 */
	public function hasContent()
	{
		$body = $this->get('body');
		return !is_scalar($body) || strlen($body);
	}
	
	/**
	 * Returns true if content is scalar
	 *
	 * @return bool
	 */
	public function isContentFlat()
	{
		return is_scalar($this->get('body'));
	}
	
	/**
	 * Sets the content
	 *
	 * @param *mixed $content Can it be an array or string please?
	 *
	 * @return Response
	 */
	public function setContent($content)
	{	
		if(!is_scalar($content)) {
			$content = (array) $content;
		}
		
		return $this->set('body', $content);
	}
}
