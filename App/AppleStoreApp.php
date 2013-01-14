<?php

namespace PastFuture\MarketBot\App;

use PastFuture\MarketBot;

/**
 * AppStore Phone App
 *
 * @package MarketBot
 * @author Jon Ursenbach <jon@gdgt.com>
 * @since 0.1
 */
class AppleStoreApp extends MarketBot\App
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
        'icon' => false,
    );
    
    /**
     * Screenshots
     *
     * @var array
     */
    protected $screenshots = array(
      'iphone' => false,
      'ipad' => false,
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
    
    /*protected $kind = "software";
    protected $features = array();
    protected $supportedDevices = array();
    protected $isGameCenterEnabled = array(); 
    protected $artistViewUrl  = "";
    protected $artworkUrl60 = "";
    protected $screenshotUrls = array();
    protected $ipadScreenshotUrls = array();
    protected $artworkUrl512 ="";
    protected $artistId = "";
    protected $artistName ="";
    protected $price = 0;
    protected $version = "";
    protected $description = "";
    protected $genreIds = array();
    protected $releaseDate = "";
    protected $sellerName = "";
    protected $currency = "";
    protected $genres = array();
    protected $bundleId = "";
    protected $trackId = "";
    
    protected $primaryGenreName = "";
    protected $primaryGenreId = "";
    protected $releaseNotes = "";
    protected $wrapperType = "software";
    protected $trackCensoredName = "";
    protected $trackViewUrl = "";
    protected $contentAdvisoryRating = "";
    protected $artworkUrl100 = "";
    protected $languageCodesISO2A = array();
    protected $fileSizeBytes = "";
    protected $sellerUrl = "";
    protected $formattedPrice = "";
    protected $averageUserRatingForCurrentVersion = "";
    protected $userRatingCountForCurrentVersion = "";
    protected $trackContentRating = "";
    protected $averageUserRating = "";
    protected $userRatingCount = "";*/
    
    protected $release_date =""; //app release date
    
    protected $artworkUrl60 =""; //app icon
    
    protected $supported_devices = array();
    
    protected $developer_url = "";
    
    /**
     * @var array $category.
     */
    protected $category = array();
    
    protected $formatted_price = "";
    
                
    /**
     * Set the image icon 60x60px.
     *
     * @param string $image
     *
     * @return void
     */
    public function setImageIcon($image)
    {
        $this->images['icon'] = $image;
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
     * Get the image icon with gloss effect and round edges. Dimension is 175x175
     *
     * @return string
     */
    public function getImageGlossIcon()
    {
      //we need to change ".png" in url to ".175x175-75.png"
      return str_replace('.png', '.175x175-75.png', $this->images['thumbnail']);
    }    
    
     /**
     * Set the image icon 512x512px.
     *
     * @param string $image
     *
     * @return void
     */
    public function setImageThumbnail($image)
    {
      $this->images['thumbnail'] = $image;
    }

    /**
     * Get the image icon.
     *
     * @return string
     */
    public function getImageThumbnail()
    {
      return $this->images['thumbnail'];
    }
    
     /**
     * Set the product category that this app belongs to.
     *
     * @param string $category
     *
     * @return void
     */
    public function addCategory($category)
    {
        $this->category[] = $category;
    }
    /**
     * Set the product category that this app belongs to.
     *
     * @param string $category
     *
     * @return void
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Get the product category that this app belongs to.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * Set an array of screenshots for iPhone.
     *
     * @param array $screenshots
     *
     * @return void
     */
    public function setScreenshotsIphone($screenshots = array())
    {
        $this->screenshots['iphone'] = $screenshots;
    }
    
    /**
     * Set an array of screenshots for iPad.
     *
     * @param array $screenshots
     *
     * @return void
     */
    public function setScreenshotsIpad($screenshots = array())
    {
        $this->screenshots['ipad'] = $screenshots;
    }

    /**
     * Add a screenshot.
     * @method addScreenshot add screenshot for iPhone and Ipad array keys.
     * @param string $screenshot
     *
     * @return void
     */
    public function addScreenshot($screenshot)
    {
        $this->screenshots[] = $screenshot;
    }
    
    /**
     * Add a screenshot.
     * @method addScreenshot add screenshot for iPhone.
     * @param string $screenshot
     *
     * @return void
     */
    public function addScreenshotIphone($screenshot)
    {
        $this->screenshots['iphone'][] = $screenshot;
    }
    
    /**
     * Add a screenshot.
     * @method addScreenshot add screenshot for Ipad.
     * @param string $screenshot
     *
     * @return void
     */
    public function addScreenshotIpad($screenshot)
    {
        $this->screenshots['ipad'][] = $screenshot;
    }

    /**
     * @method getScreenshots init
     * 
     * @return array iphone and ipad assoc keys.
     */
    public function getScreenshots()
    {
        return $this->screenshots;
    }
    
    /**
     * @method getScreenshotsIpone init
     * 
     * @return array iphone screenshots.
     */
    public function getScreenshotsIphone()
    {
        return $this->screenshots['iphone'];
    }
    
    /**
     * @method getScreenshotsIpad init
     * 
     * @return array of Ipad scrennshots.
     */
    public function getScreenshotsIpad()
    {
        return $this->screenshots['ipad'];
    }  
    
    /**
     * @method setFormattedPrice string
     *  
     */
    function setFormattedPrice($formatted_price){
      $this->formatted_price = $formatted_price;
    }
    /**
     * @method getFormattedPrice 
     * 
     * @return string 
     */
    function getFormattedPrice(){
      return $this->formatted_price;
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
    
    public function addSupportedDevices($supported_devices){
      $this->supported_devices = $supported_devices;
    }
    
    public function getSupportedDevices(){
      return $this->supported_devices;
    }

    public function setDeveloperUrl($developer_url)
    {
      $this->developer_url = $developer_url;
    }
    
    public function getDeveloperUrl()
    {
      return $this->developer_url;
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
    
    public function setReleaseDate($release_date){
      $this->release_date = $release_date;
    }
    public function getReleaseDate(){
      return $this->release_date;
    }
}
