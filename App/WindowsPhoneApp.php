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

namespace PastFuture\MarketBot\App;

use PastFuture\MarketBot;

/**
 * Windows Phone App
 *
 * @package MarketBot
 * @author Jon Ursenbach <jon@gdgt.com>
 * @since 0.1
 */
class WindowsPhoneApp extends MarketBot\App
{
    protected $software_requirements = array();
    protected $hardware_requirements = array();
    protected $supported_languages = array();

    /**
     * Images
     *
     * @var array
     */
    protected $images = array(
        'thumbnail' => false,
        'icon' => false
    );

    /**
     * Given a Windows Phone Marketplace "data-ov" value, construct a market ID
     * from it.
     *
     * @example "Games;b1e9b73d-0cee-4bbe-8159-a5acfffe0239 urknall-online"
     *
     * @param string $market_id
     *
     * @return string
     */
    public static function constructMarketId($market_id)
    {
        $market_id = explode(';', $market_id);
        $market_id = explode(' ', $market_id[1]);
        $market_id = array_filter($market_id);
        $market_id = array_reverse($market_id);
        $market_id = implode('/', $market_id);

        return $market_id;
    }

    /**
     * Format and set the price for this app.
     *
     * @param string
     *
     * @return void
     */
    public function setPrice($price)
    {
        if (!empty($price)) {
            $price = trim(strtolower($price));
            if ($price == 'install' || $price == 'free') {
                $price = '0.00';
            } else {
                $price = str_replace('$', '', $price);
                $price = trim($price);
            }

            parent::setPrice($price);
        }
    }

    /**
     * Set the number of votes.
     *
     * @param string $votes
     *
     * @return void
     */
    public function setVotes($votes)
    {
        if (!empty($votes)) {
            $votes = trim($votes);
            $votes = str_replace('Ratings:', '', $votes);

            parent::setVotes($votes);
        }
    }

    /**
     * Set the rating.
     *
     * @param string $rating
     *
     * @return void
     */
    public function setRating($rating)
    {
        if (!empty($rating)) {
            $rating = explode('Pt', $rating);
            $rating = $this->wordToNumber($rating[0]) . '.' . $this->wordToNumber($rating[1]);

            $this->rating = (float)$rating;
        }
    }

    /**
     * Add a software requirement that this app requires.
     *
     * @param string $type
     * @param string $requirement
     *
     * @return void
     */
    public function addSoftwareRequirement($type, $requirement)
    {
        $this->software_requirements[$type] = $requirement;
    }

    /**
     * Get the software requirements that this app requires.
     *
     * @return array
     */
    public function getSoftwareRequirement()
    {
        return $this->software_requirements;
    }

    /**
     * Add a hardware requirement that this app requires.
     *
     * @param string $requirement
     *
     * @return void
     */
    public function addHardwareRequirement($requirement)
    {
        $this->hardware_requirements[] = $requirement;
    }

    /**
     * Get the hardware requirements that this app requires.
     *
     * @return array
     */
    public function getHardwareRequirement()
    {
        return $this->hardware_requirements;
    }

    /**
     * Add a language that this app supports.
     *
     * @param string $language
     *
     * @return void
     */
    public function addSupportedLanguage($language)
    {
        $this->supported_languages[] = $language;
    }

    /**
     * Get the languages that this app supports.
     *
     * @return array
     */
    public function getSupportedLanguages()
    {
        return $this->supported_languages;
    }

    /**
     * Set the image thumbnail.
     *
     * @param string $image
     *
     * @return void
     */
    public function setImageThumbnail($image)
    {
        if (!empty($image)) {
            if (strpos($image, 'width=120&height=120') === false) {
                $image = substr($image, 0, strpos($image, '?'));
                $image .= '?width=120&height=120&resize=true';
            }

            $this->images['thumbnail'] = $image;
        }
    }

    /**
     * Get the image thumbnail.
     *
     * @return string
     */
    public function getImageThumbnail()
    {
        return $this->images['thumbnail'];
    }

    /**
     * Set the image icon.
     *
     * @param string $image
     *
     * @return void
     */
    public function setImageIcon($image)
    {
        if (!empty($image)) {
            if (strpos($image, 'width=200&height=200') === false) {
                $image = substr($image, 0, strpos($image, '?'));
                $image .= '?width=200&height=200&resize=true';
            }

            $this->images['icon'] = $image;
        }
    }

    /**
     * Get the image icon.
     *
     * @return string
     */
    public function getImageIcon()
    {
        return $this->images['icon'];
    }

    /**
     * Convert the representation of a number as word back into a number (four
     * becomes "4", two is "2").
     *
     * @param string $word
     *
     * @return integer
     */
    private function wordToNumber($word)
    {
        $conv = array(
          'zero' => 0,
          'one' => 1,
          'two' => 2,
          'three' => 3,
          'four' => 4,
          'five' => 5
        );

        return $conv[strtolower($word)];
    }
}
