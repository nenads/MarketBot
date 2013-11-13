<?php
namespace PastFuture\MarketBot\AppleStore;

use PastFuture\MarketBot;
use PastFuture\MarketBot\App;

/**
 * AppleStoreAPI
 *
 * @package MarketBot
 * @author 
 * @since 0.1
 */
class AppleStoreAPI extends MarketBot\AppleStore
{
    /**
     * Search type
     *
     * @var string
     */
    private $search_type = 'software';

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
    protected $details_url = 'https://itunes.apple.com/lookup?%s=%s';

    /**
     * Search URL
     *
     * @var string
     */
    protected $search_url = 'https://itunes.apple.com/search';

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
        // Only software are supported right now.
        if ($type != "software"){
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
            $tmp_app = json_decode($this->initScraper($url, 'JSON'));
            $item = $tmp_app->results[0];
        
            $market_id = $item->trackId;
            /* @var $apps PastFuture\MarketBot\App\AppleStoreApp */
            $app = new \PastFuture\MarketBot\App\AppleStoreApp(
                array(
                  'market_id' => $market_id,
                  'name' => $item->trackName,
                  'description' => $item->description,
                  'release_notes' => $item->releaseNotes,
                  'url' => $item->trackViewUrl
                )
            );

            $app->setReleaseDate($item->releaseDate);
            $app->setImageIcon($item->artworkUrl512);
            $app->setImageThumbnail($item->artworkUrl512);
            
            foreach ($item->genres as $genre) {
              $app->addCategory($genre);
            }
            
            foreach ($item->screenshotUrls as $iphoneScreenshot) {
              $app->addScreenshotIphone($iphoneScreenshot);
            }

            foreach ($item->ipadScreenshotUrls as $ipadScreenshot) {
              $app->addScreenshotIpad($ipadScreenshot);
            }
            
            foreach ($item->languageCodesISO2A as $lang) {
              $app->addSupportedLanguage($lang);
            }
            
            foreach ($item->supportedDevices as $supported_device) {
              $app->addSupportedDevices($supported_device);
            }
            
            $app->setDeveloper($item->artistName);
            $app->setDeveloperUrl($item->artistViewUrl);
            
            $app->setPrice($item->price);
            $app->setFormattedPrice($item->formattedPrice);
            $app->setCurrentVersion($item->version);
            $app->setSize($item->fileSizeBytes);

            $rating = $item->averageUserRating;

            $app->setRating($rating);

            $apps[$market_id] = $app;            
            
        } catch (Exception $e) {
            return false;
        }

        return $app;
    }

    /**
     * With a search term, execute a search on Apple Store.
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
                'term' => $term,
                'media' => 'software'
            )
        );
        
        try {
            $apps = array();
            
            $items = json_decode($this->initScraper($url, 'JSON'));

            if (count($items) == 0) {
                return false;
            }
 
            /* @var $item PastFuture\MarketBot\App\AppleStoreApp */
            foreach ($items->results as $item) {
                
                $market_id = $item->trackId;
                /* @var $apps PastFuture\MarketBot\App\AppleStoreApp */
                $app = new \PastFuture\MarketBot\App\AppleStoreApp(
                    array(
                      'market_id' => $market_id,
                      'name' => $item->trackName,
                      'description' => $item->description,
                      'release_notes' => isset($item->releaseNotes)? $item->releaseNotes : null,
                      'url' => $item->trackViewUrl
                    )
                );

                $app->setReleaseDate($item->releaseDate);
                $app->setImageIcon($item->artworkUrl512);
                $app->setImageThumbnail($item->artworkUrl512);
                
                foreach ($item->genres as $genre) {
                  $app->addCategory($genre);
                }
                
                foreach ($item->screenshotUrls as $iphoneScreenshot) {
                  $app->addScreenshotIphone($iphoneScreenshot);
                }

                foreach ($item->ipadScreenshotUrls as $ipadScreenshot) {
                  $app->addScreenshotIpad($ipadScreenshot);
                }
                
                foreach ($item->languageCodesISO2A as $lang) {
                  $app->addSupportedLanguage($lang);
                }
                
                foreach ($item->supportedDevices as $supported_device) {
                  $app->addSupportedDevices($supported_device);
                }
                
                $app->setDeveloper($item->artistName);
                $app->setDeveloperUrl($item->artistViewUrl);
                
                $app->setPrice($item->price);
                $app->setFormattedPrice($item->formattedPrice);
                $app->setCurrentVersion($item->version);
                $app->setSize($item->fileSizeBytes);

                $rating = isset($item->averageUserRating)? $item->averageUserRating : null;

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
      return sprintf($this->details_url, 'id', $market_id);
    }
}
