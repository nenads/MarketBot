<?php

/**
 * MarketBot
 *
 * @author Jon Ursenbach <jon@gdgt.com>
 * @link http://github.com/pastfuture/MarketBot
 * @license Modified BSD
 * @version 0.1
 *
 * Copyright (c) 2012, PastFuture, Inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  * Neither the name of PastFuture, Inc., gdgt, nor the names of its
 *    contributors may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace PastFuture\MarketBot;

/**
 * MarketBot
 *
 * @package MarketBot
 * @author Jon Ursenbach <jon@gdgt.com>
 * @since 0.1
 */
class MarketBot
{
    /**
     * Version
     *
     * @var string
     */
    protected $version = '0.1';

    /**
     * Default language
     *
     * @var string
     */
    protected $language = 'en';

    /**
     * Initialize the phpQuery scraper for use.
     *
     * @param string $url
     * @param string $format
     *
     * @return void
     */
    protected function initScraper($url, $format = 'HTML')
    {
       $ch = curl_init();
		
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //XXX:: We should use 1 but server setup.
		
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		
        $response = curl_exec($ch);
		
		if(curl_error($ch)){
			
			$message = 'CURL - ERRNO CODE: '. curl_errno($ch) . ' - '. curl_error($ch) . ' - url: '. $url;  
			
			curl_close($ch);
			
			throw new \Exception($message);
		}
		
		$res = curl_getinfo($ch);

		curl_close($ch);
		
		//We only want to read valid reposnses since google can sometime return some strange page with not valid html
		if($res['http_code'] != '200'){  
			\phpQuery::newDocument(''); //Create dummy so we have nice flow
			return;	
		}
		
        if ($format == 'JSON') {
          return $response;
        } else {
          \phpQuery::newDocument($response);
        }
    }

    /**
     * Sets the language we want to deal with.
     *
     * @param string $language
     *
     * @return null
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Gets the language we're dealing with.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
