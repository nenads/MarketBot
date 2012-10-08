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
 *  * Neither the name of PastFuture, Inc nor the names of its contributors may
 *    be used to endorse or promote products derived from this software without
 *    specific prior written permission.
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

use PastFuture\MarketBot\App;

/**
 * Windows Phone
 *
 * @package MarketBot
 * @author Jon Ursenbach <jon@gdgt.com>
 * @since 0.1
 */
class WindowsPhone extends MarketBot
{
    /**
     * Default language
     *
     * @var string
     */
    protected $language = 'en-us';

    /**
     * Pull start
     *
     * @var integer
     */
    private $pull_start = 0;

    /**
     * Details URL
     *
     * @var string
     */
    protected $details_url = 'http://www.windowsphone.com/%s/store/app/%s';

    /**
     * Search URL
     *
     * @var string
     */
    protected $search_url = 'http://www.windowsphone.com/%s/store/search';

    /**
     * Scrape the dedicated details page for a specific application and return an
     * array containing its data.
     *
     * @param string $market_id
     *
     * @return array|false Array if the item and data were found, false otherwise.
     */
    public function get($market_id)
    {
        $app = false;

        try {
            $url = $this->getDetailsUrl($market_id);
            $this->initScraper($url);

            $page = \pq('#application');

            $name = $page->find('h1[itemprop="name"]')->html();
            if (empty($name)) {
                return false;
            }

            $app = new App\WindowsPhoneApp(
                array(
                    'market_id' => $market_id,
                    'url' => $url,
                    'name' => $name,
                    'developer' => $page->find('#publisher a')->html(),
                    'description' => $page->find('pre[itemprop="description"]')->html(),
                    'votes' => $page->find('#rating meta[itemprop="ratingCount"]')->attr('content'),
                    'last_updated' => $page->find('#releaseDate span')->html(),
                    'current_version' => $page->find('#version span')->html(),
                    'category' => $page->find('[itemprop="applicationCategory"]')->html(),
                    'size' => $page->find('#packageSize span')->html(),
                    'price' => $page->find('span[itemprop="price"]')->html(),
                    'content_rating' => $page->find('#softwareRatingLink a')->html()
                )
            );

            $rating = $page->find('#rating div.ratingLarge')->attr('class');
            $rating = trim(str_replace('ratingLarge', '', $rating));
            $app->setRating($rating);

            $related_apps = $page->find('#appRelatedApps td.medium');
            if ($related_apps->length()) {
                foreach ($related_apps as $related_app) {
                    $related_app = \pq($related_app);

                    $related_market_id = $related_app->find('a.title')->attr('data-ov');
                    $related_market_id = App\WindowsPhoneApp::constructMarketId($related_market_id);

                    $app->addRelated($related_market_id);
                }
            }

            $image = $page->find('img.xlarge')->attr('src');
            $app->setImageThumbnail($image);
            $app->setImageIcon($image);

            $screenshots = $page->find('#screenshots li img');
            if ($screenshots->length()) {
                foreach ($screenshots as $screenshot) {
                    $screenshot = \pq($screenshot);

                    $app->addScreenshot($screenshot->attr('src'));
                }
            }

            $software_requirements = $page->find('#softwareRequirements')->children('span');
            if ($software_requirements->length()) {
                foreach ($software_requirements as $software) {
                    $software = \pq($software);

                    $app->addSoftwareRequirement($software->attr('itemprop'), $software->html());
                }
            }

            $hardware_requirements = $page->find('#hardwareRequirements ul li');
            if ($hardware_requirements->length()) {
                foreach ($hardware_requirements as $hardware) {
                    $hardware = \pq($hardware);

                    $app->addHardwareRequirement($hardware->html());
                }
            }

            $supported_languages = $page->find('#languageList span');
            if ($supported_languages->length()) {
                foreach ($supported_languages as $language) {
                    $language = \pq($language);

                    $app->addSupportedLanguage($language->html());
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return $app;
    }

    /**
     * With a search term, execute a search on Google Play.
     *
     * @param string $term
     *
     * @return array|false If results are found, an array is returned, otherwise false.
     */
    public function search($term)
    {
        $url = $this->getSearchUrl() . '?';
        $url .= http_build_query(
            array(
                'q' => $term,
                'startIndex' => $this->getPullStart()
            )
        );

        try {
            $apps = array();
            $this->initScraper($url);

            $items = \pq('td.medium:not(.empty)');
            if (!$items->length()) {
                return false;
            }

            foreach ($items as $item) {
                $item = \pq($item);

                $title = $item->find('a.title');

                $market_id = $title->attr('data-ov');
                $market_id = App\WindowsPhoneApp::constructMarketId($market_id);

                $app = new App\WindowsPhoneApp(
                    array(
                        'market_id' => $market_id,
                        'url' => $title->attr('href'),
                        'name' => $title->html(),
                        'description' => null,
                        'developer' => null,
                        'category' => null,
                        'price' => $item->find('.cost')->html(),
                        'votes' => $item->find('.ratingCount')->html()
                    )
                );

                $rating = $item->find('.ratingSmall')->attr('class');
                $rating = trim(str_replace('ratingSmall', '', $rating));
                $app->setRating($rating);

                $image = $item->find('a.appImage img');
                $image = $image->attr('src');
                $app->setImageThumbnail($image);

                $apps[$market_id] = $app;
            }

            return (!empty($apps)) ? $apps : false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Sets the number index we wish to start pulling search results from. Starts
     * at 0 and increments.
     *
     * @param integer $number
     *
     * @return void
     */
    public function setPullStart($start)
    {
        $this->pull_start = (int)$start;
    }

    /**
     * Gets the number index we wish to start pulling search results from.
     *
     * @return integer
     */
    public function getPullStart()
    {
        return $this->pull_start;
    }

    /**
     * Get the dedicated details market URL for a specific market ID.
     *
     * @param string $market_id
     *
     * @return string
     */
    private function getDetailsUrl($market_id)
    {
        return sprintf($this->details_url, $this->getLanguage(), $market_id);
    }

    /**
     * Get the search URL.
     *
     * @return string
     */
    private function getSearchUrl()
    {
        return sprintf($this->search_url, $this->getLanguage());
    }
}
