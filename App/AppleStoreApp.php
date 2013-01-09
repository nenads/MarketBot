<?php

namespace PastFuture\MarketBot\App;

use PastFuture\MarketBot;

/**
 * Windows Phone App
 *
 * @package MarketBot
 * @author Jon Ursenbach <jon@gdgt.com>
 * @since 0.1
 */
class AppleStoreApp extends MarketBot\App
{
    //protected $software_requirements = array();
    //protected $hardware_requirements = array();
    //protected $supported_languages = array();

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
    
    protected $kind = "software";
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
    protected $trackName = "";
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
    protected $userRatingCount = "";
                
    

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
