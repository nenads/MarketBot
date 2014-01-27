<?php
namespace PastFuture\MarketBot\Android;
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
class GooglePlay extends MarketBot\Android
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
    protected $details_url = 'https://play.google.com/store/%s/details?id=%s&hl=en'; //Note:: we force en version.

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

            $page = \pq('.details-wrapper');

            $name = $page->find('.document-title')->attr('itemprop','name')->text();
            if (empty($name)) {
                return false;
            }

            $app = new App\Android\GooglePlayApp(
                array(
                    'market_id' => $market_id,
                    'url' => $url,
                    'name' => $name,
                    'developer' => $page->find('.details-section.metadata a.dev-link')->text(),
                    'description' => $page->find('.details-section.description .app-orig-desc')->html(),
                    'release_notes' => $page->find('.details-section.whatsnew')->html(), //remove title

                    'rating' => $page->find('.details-section.reviews .score-container .score')->text(),
                    'votes' => $page->find('.details-section.reviews .score-container .reviews-num')->text()
                )
            );
			/*
            $similar = $page->find('.details-section.recommendation .rec-cluster .cards')->children();
            if (!empty($similar)) {
                foreach ($similar as $similar_type) {
                    $similar_type = \pq($similar_type);
					//dd($similar_type);

                    //$type = $similar_type->attr('data-analyticsid');
                    //$type = str_replace('-', '_', $type);

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
            }*/ //TODO:: check data on old store and compare

            $icon = $page->find('.details-info img.cover-image')->attr('src');
            $banner = $page->find('.details-info img.cover-image')->attr('src'); //TODO:: what is banner now ?

            $app->setImageThumbnail($icon);
            $app->setImageIcon($icon);
            $app->setImageIconLarge($icon);
            $app->setImageBanner($banner);

            $website = $page->find('.details-section.metadata a.dev-link');
            if ($website->length()) {
                $website = $website->attr('href');
                $app->setWebsiteUrl(substr($website, strlen('http://www.google.com/url?q=')));
            }

            $email = $page->find('.details-section.metadata a.dev-link[href^=mail]');
            
            if ($email->length()) {
                $email = str_replace('mailto:', '', $email->attr('href'));
                $app->setDeveloperEmail($email);
            }

            $videos = $page->find('.details-section.screenshots .details-trailer .play-action-container');
			
            if ($videos->length()) {
                foreach ($videos as $video) {
                    $video = \pq($video);
                    $app->addVideo($video->attr('data-video-url'));
                }
            }

            $screenshots = $page->find('.details-section.screenshots img.screenshot ');
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

            $metadata = $page->find('.details-section.metadata .meta-info');
			
			$category = $page->find('.document-subtitle.category [itemprop=genre]')->text();
			
			if(!is_null($category))
				$app->setCategory($category);
			
            foreach ($metadata as $meta) {
                $meta = \pq($meta);
                $field_name = $meta->find('.title');
				
                switch ($field_name->text()) {
                    case 'Updated':
                        $app->setLastUpdated($field_name->next()->text());
                        break;
                    case 'Current Version':
                        $app->setCurrentVersion($field_name->next()->text());
                        break;
                    case 'Requires Android':
                        $app->setRequires($field_name->next()->text());
                        break;
    	            //case 'Category': NOTE:: is removed now
                        //$app->setCategory($field_name->next()->text());
                        //break;
                    case 'Installs':
                        $installs = $field_name->next()->text();
                        //$installs = str_replace($installs, '', $meta->next()->text());
                        $app->setInstalls($installs);
                        break;
                    case 'Size':
                        $app->setSize($field_name->next()->text());
                        break;
                    case 'Price':
                        $app->setPrice($field_name->next()->text());
                        break;
                    case 'Content Rating':
                        $app->setContentRating($field_name->next()->text());
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

            $items = \pq('.card-list .card ');
            if (!$items->length()) {
                return false;
            }
			
            foreach ($items as $item) {
                $item = \pq($item);

                $market_id = $item->attr('data-docid');
				//dd($item->find('.cover img.cover-image')->attr('src'));
                $app = new App\Android\GooglePlayApp(
                    array(
                        'market_id' => $market_id,
                        'url' => $this->getDetailsUrl($market_id),
                        'name' => $item->find('.details a.title')->text(),
                        'description' => $item->find('.snippet .description')->html(),
                        'developer' => $item->find('.details a.subtitle')->text(),
                        //'category' => $item->find('.category')->text(),
                        'price' => $item->find('.price-container .price.buy:first')->html()
                    )
                );

                $image = $item->find('.cover img.cover-image')->attr('src');
                $app->setImageThumbnail($image);
                $app->setImageIcon($image);
                $app->setImageIconLarge($image);

                // This could be replaced with regex but I'm lazy.
                $rating_html = $item->find('.stars-container .current-rating')->attr('style');
				preg_match("/[width\s*:\s*]([0-9.]+)/", $rating_html, $rating);
				//dd($rating);
                /*$rating = strtolower($rating);
                $rating = str_replace('rating: ', '', $rating);
                $rating = substr($rating, 0, strpos($rating, 'stars'));
                $rating = trim($rating);
				 * */
                $app->setRating($rating[1]);

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
