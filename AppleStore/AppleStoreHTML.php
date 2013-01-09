<?php
namespace PastFuture\MarketBot\AppleStore;
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



use PastFuture\MarketBot;
use PastFuture\MarketBot\App;

/**
 * Google Play
 *
 * @package MarketBot
 * @author Jon Ursenbach <jon@gdgt.com>
 * @since 0.1
 */
class AppleStoreHTML extends MarketBot\AppleStore
{
    /**
     * Search type
     *
     * @var string
     */
    private $search_type = 'apps';

    /**
     * Safe Search
     *
     * @var integer
     */
    private $safe_search = 0;

    /**
     * Price search
     *
     * @var integer
     */
    private $price = 0;

    /**
     * Search sort
     *
     * @var integer
     */
    private $sort = 1;

    /**
     * Pull start
     *
     * @var integer
     */
    private $pull_start = 0;

    /**
     * Pull total
     *
     * @var integer
     */
    private $pull_total = 24;

    /**
     * Details URL
     *
     * @var string
     */
    protected $details_url = 'https://play.google.com/store/%s/details?id=%s';

    /**
     * Search URL
     *
     * @var string
     */
    protected $search_url = 'https://play.google.com/store/search';

    /**
     * Given a market ID and a market item type, scrape content off of the
     * dedicated details page into an array.
     *
     * @param string $market_id
     * @param string $type
     *
     * @return array|false Array if the item and data were found, false otherwise.
     */
    public function get($market_id, $type)
    {
        // Only apps are supported right now.
        if (in_array($type, array('music', 'books', 'movies', 'magazines'))) {
            return false;
        }

        return $this->getApp($market_id);
    }

    /**
     * Scrape the dedicated details page for a specific application and return an
     * array containing its data.
     *
     * @param string $market_id
     *
     * @return array|false Array if the item and data were found, false otherwise.
     */
    private function getApp($market_id)
    {
        $app = false;

        try {
            $url = $this->getDetailsUrl($market_id);
            $this->initScraper($url);

            $page = \pq('.details-page');

            $name = $page->find('.doc-banner-title')->text();
            if (empty($name)) {
                return false;
            }

            $app = new App\Android\GooglePlayApp(
                array(
                    'market_id' => $market_id,
                    'url' => $url,
                    'name' => $name,
                    'developer' => $page->find('.doc-banner-title-container a')->text(),
                    'description' => $page->find('#doc-original-text')->html(),

                    'release_notes' => $page->find('.doc-whatsnew-container')->html(),

                    'rating' => $page->find('.average-rating-value')->text(),
                    'votes' => $page->find('.votes:first')->text()
                )
            );

            $similar = $page->find('.doc-similar')->children();
            if (!empty($similar)) {
                foreach ($similar as $similar_type) {
                    $similar_type = \pq($similar_type);

                    $type = $similar_type->attr('data-analyticsid');
                    $type = str_replace('-', '_', $type);

                    $similar_apps = $similar_type->find('.snippet-list')->children();
                    if (!empty($similar_apps)) {
                        foreach ($similar_apps as $similar_app) {
                            $similar_app = \pq($similar_app);
                            $similar_app = $similar_app->find('div:first')->attr('data-docid');

                            switch ($type) {
                                case 'more_from_developer':
                                    $app->addMoreFromDeveloper($similar_app);
                                    break;
                                case 'related':
                                    $app->addRelated($similar_app);
                                    break;
                                case 'users_also_installed':
                                    $app->addUsersAlsoInstalled($similar_app);
                                    break;
                            }
                        }
                    }
                }
            }

            $icon = $page->find('.doc-banner-icon img')->attr('src');
            $banner = $page->find('.doc-banner-image-container img')->attr('src');

            $app->setImageThumbnail($icon);
            $app->setImageIcon($icon);
            $app->setImageIconLarge($icon);
            $app->setImageBanner($banner);

            $website = $page->find('.doc-overview a:contains("Visit Developer\'s Website")');
            if ($website->length()) {
                $website = $website->attr('href');
                $app->setWebsiteUrl(substr($website, strlen('http://www.google.com/url?q=')));
            }

            $email = $page->find('.doc-overview a:contains("Email Developer")');
            if ($email->length()) {
                $email = str_replace('mailto:', '', $email->attr('href'));
                $app->setDeveloperEmail($email);
            }

            $videos = $page->find('.doc-video-section object');
            if ($videos->length()) {
                foreach ($videos as $video) {
                    $video = \pq($video);

                    $app->addVideo($video->find('embed')->attr('src'));
                }
            }

            $screenshots = $page->find('.screenshot-carousel-content-container img');
            if ($screenshots->length()) {
                // Could rewrite this with pq->map() if they had better documentation on
                // how to use it.
                foreach ($screenshots as $screenshot) {
                    $screenshot = \pq($screenshot);

                    $app->addScreenshot($screenshot->attr('src'));
                }
            }

            $permission_types = array('dangerous', 'safe');
            foreach ($permission_types as $permission_type) {
                $permissions = $page->find('#doc-permissions-' . $permission_type . ' .doc-permission-group');
                if ($permissions->length()) {
                    foreach ($permissions as $permission) {
                        $permission = \pq($permission);

                        $title = $permission->find('.doc-permission-group-title')->text();
                        foreach ($permission->find('.doc-permission-description') as $description) {
                            $description = \pq($description);

                            $app->addPermission(
                                array(
                                  'security' => $permission_type,
                                  'group' => $title,
                                  'description' => $description->text(),
                                  'description_full' => $description->next()->text()
                                )
                            );
                        }
                    }
                }
            }

            $metadata = $page->find('.doc-metadata dt');
            foreach ($metadata as $meta) {
                $meta = \pq($meta);
                $field_name = $meta->text();

                switch ($field_name) {
                    case 'Updated:':
                        $app->setLastUpdated($meta->next()->text());
                        break;
                    case 'Current Version:':
                        $app->setCurrentVersion($meta->next()->text());
                        break;
                    case 'Requires Android:':
                        $app->setRequires($meta->next()->text());
                        break;
                    case 'Category:':
                        $app->setCategory($meta->next()->text());
                        break;
                    case 'Installs:':
                        $installs = $meta->next()->find('div')->text();
                        $installs = str_replace($installs, '', $meta->next()->text());

                        $app->setInstalls($installs);
                        break;
                    case 'Size:':
                        $app->setSize($meta->next()->text());
                        break;
                    case 'Price:':
                        $app->setPrice($meta->next()->text());
                        break;
                    case 'Content Rating:':
                        $app->setContentRating($meta->next()->text());
                        break;
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
        $url = $this->search_url . '?';
        $url .= http_build_query(
            array(
                'q' => $term,
                'start' => $this->getPullStart(),
                'num' => $this->getPullTotal(),
                'hl' => $this->getLanguage(),

                'c' => $this->getSearchType(),
                'safe' => $this->getSafeSearch(),
                'price' => $this->getPrice(),
                'sort' => $this->getSort()
            )
        );

        try {
            $apps = array();
            $this->initScraper($url);

            $items = \pq('.search-results-item');
            if (!$items->length()) {
                return false;
            }

            foreach ($items as $item) {
                $item = \pq($item);

                $market_id = $item->attr('data-docid');

                $app = new App\Android\GooglePlayApp(
                    array(
                        'market_id' => $market_id,
                        'url' => $this->getDetailsUrl($market_id),
                        'name' => $item->find('.details a')->attr('title'),
                        'description' => $item->find('.snippet .description')->html(),
                        'developer' => $item->find('.attribution a')->text(),
                        'category' => $item->find('.category')->text(),
                        'price' => $item->find('.buy-button-price:first')->html()
                    )
                );

                $image = $item->find('.snippet .thumbnail img')->attr('src');
                $app->setImageThumbnail($image);
                $app->setImageIcon($image);
                $app->setImageIconLarge($image);

                // This could be replaced with regex but I'm lazy.
                $rating = $item->find('.ratings')->attr('title');
                $rating = strtolower($rating);
                $rating = str_replace('rating: ', '', $rating);
                $rating = substr($rating, 0, strpos($rating, 'stars'));
                $rating = trim($rating);
                $app->setRating($rating);

                $apps[$market_id] = $app;
            }

            return (!empty($apps)) ? $apps : false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Set the type of price search we want to execute.
     *
     * Available options:
     *  - 0: All
     *  - 1: Free
     *  - 2: Paid
     *
     * @return void
     */
    public function setPrice($type)
    {
        $this->price = (int)$type;
    }

    /**
     * Gets the type of price search we are executing.
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets how we want our search results to be sorted.
     *
     * Available options:
     *  - 1: Relevance
     *  - 0: Popularity
     *
     * @return void
     */
    public function setSort($type)
    {
        $this->sort = (int)$type;
    }

    /**
     * Gets how our search results are going to be sorted.
     *
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Sets the type of SafeSearch that you want to execute.
     *
     * Available options:
     *  - 0: Off - All results will be included in your search.
     *  - 1: Low - Filter results rated High Maturity.
     *  - 2: Moderate - Filter results rated High or Medium Maturity.
     *  - 3: Strict - Filter results rated High, Medium, or Low Maturity.
     *
     * @return void
     */
    public function setSafeSearch($type)
    {
        $this->safe_search = (int)$type;
    }

    /**
     * Gets the type of SafeSearch that you're executing.
     *
     * @return integer
     */
    public function getSafeSearch()
    {
        return $this->safe_search;
    }

    /**
     * Sets the type of search you want to execute.
     *
     * Available options:
     *  - null: All results
     *  - apps: Android apps
     *  - music: Music
     *  - books: Books
     *  - movies: Movies & TV
     *  - magazines: Magazines
     *
     * @param string $type
     *
     * @return void
     */
    public function setSearchType($type)
    {
        $this->search_type = (string)$type;
    }

    /**
     * Gets the current type of search we're executing.
     *
     * @return string
     */
    public function getSearchType()
    {
        return $this->search_type;
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
     * Sets the total amount of results from a page we want to pull. Default is
     * 24.
     *
     * @param integer $number
     *
     * @return void
     */
    public function setPullTotal($total)
    {
        $this->pull_total = (int)$total;
    }

    /**
     * Gets the total amount of results from a apge we want to pull.
     *
     * @return integer
     */
    public function getPullTotal()
    {
        return $this->pull_total;
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
        return sprintf($this->details_url, $this->getSearchType(), $market_id);
    }
}
