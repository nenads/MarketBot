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

/**
 * App
 *
 * @package MarketBot
 * @author Jon Ursenbach <jon@gdgt.com>
 * @since 0.1
 */
abstract class App
{
    /**
     * Market ID
     *
     * @var string
     */
    protected $market_id;

    /**
     * Market URL
     *
     * @var string
     */
    protected $url;

    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Developer
     *
     * @var string
     */
    protected $developer;

    /**
     * Description
     *
     * @var string
     */
    protected $description;

    /**
     * Product category
     *
     * @var string
     */
    protected $category;

    /**
     * Release notes
     *
     * @var string
     */
    protected $release_notes;

    /**
     * Price
     *
     * @var float
     */
    protected $price;

    /**
     * Screenshots
     *
     * @var array
     */
    protected $screenshots = array();

    /**
     * Current Version
     *
     * @var string
     */
    protected $current_version;

    /**
     * Size
     *
     * @var string
     */
    protected $size;

    /**
     * Content rating
     *
     * @var string
     */
    protected $content_rating;

    /**
     * Number of installs
     *
     * @var string
     */
    protected $installs;

    /**
     * Rating
     *
     * @var integer|false
     */
    protected $rating = false;

    /**
     * Votes
     *
     * @var integer|false
     */
    protected $votes = false;

    /**
     * Last updated date
     *
     * @var string
     */
    protected $last_updated;

    /**
     * Related apps.
     *
     * @var array
     */
    protected $related = array();

    /**
     * @return void
     */
    public function __construct($data = array())
    {
        foreach ($data as $field => $value) {
            $method_name = str_replace('_', ' ', $field);
            $method_name = ucwords($method_name);
            $method_name = str_replace(' ', '', $method_name);
            $method = 'set' . $method_name;

            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * Set the market ID.
     *
     * @param string $market_id
     *
     * @return void
     */
    public function setMarketId($market_id)
    {
        $this->market_id = $market_id;
    }

    /**
     * Get the market ID.
     *
     * @return string
     */
    public function getMarketId()
    {
        return $this->market_id;
    }

    /**
     * Set the market URL.
     *
     * @param string $url
     *
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get the market URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the app name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the app name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the developer's name.
     *
     * @param string $developer
     *
     * @return string
     */
    public function setDeveloper($developer)
    {
        $this->developer = $developer;
    }

    /**
     * Get the developer's name.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return $this->developer;
    }

    /**
     * Set the description.
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set the release notes.
     *
     * @param string $release_notes
     *
     * @return void
     */
    public function setReleaseNotes($notes)
    {
        $this->release_notes = $notes;
    }

    /**
     * Get the release notes.
     *
     * @return string
     */
    public function getReleaseNotes()
    {
        return $this->release_notes;
    }

    /**
     * Set the price.
     *
     * @param string $price
     *
     * @return void
     */
    public function setPrice($price)
    {
        $this->price = (float)$price;
    }

    /**
     * Get the price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set an array of screenshots.
     *
     * @param array $screenshots
     *
     * @return void
     */
    public function setScreenshots($screenshots = array())
    {
        $this->screenshots = $screenshots;
    }

    /**
     * Add a screenshot.
     *
     * @param string $screenshot
     *
     * @return void
     */
    public function addScreenshot($screenshot)
    {
        $this->screenshots[] = $screenshot;
    }

    /**
     * Get screenshots.
     *
     * @return array
     */
    public function getScreenshots()
    {
        return $this->screenshots;
    }

    /**
     * Set the current version.
     *
     * @param string $version
     *
     * @return void
     */
    public function setCurrentVersion($version)
    {
        $this->current_version = $version;
    }

    /**
     * Get the current version.
     *
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->current_version;
    }

    /**
     * Set the app size.
     *
     * @param string $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Get the app size.
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the content rating.
     *
     * @param string $rating
     *
     * @return void
     */
    public function setContentRating($rating)
    {
        if (!empty($rating)) {
            $this->content_rating = $rating;
        }
    }

    /**
     * Get the content rating.
     *
     * @return string
     */
    public function getContentRating()
    {
        return $this->content_raitng;
    }

    /**
     * Set the number of installs.
     *
     * @param string $installs
     *
     * @return void
     */
    public function setInstalls($installs)
    {
        $this->installs = $installs;
    }

    /**
     * Get the number of installs.
     *
     * @return string
     */
    public function getInstalls()
    {
        return $this->installs;
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
            $this->rating = (float)$rating;
        }
    }

    /**
     * Get the rating.
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
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
            $this->votes = (int)str_replace(',', '', $votes);
        }
    }

    /**
     * Get the number of votes.
     *
     * @return integer
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set the last updated date.
     *
     * @param string $updated
     *
     * @return void
     */
    public function setLastUpdated($updated)
    {
        $this->last_updated = $updated;
    }

    /**
     * Get the last updated date.
     *
     * @return string
     */
    public function getLastUpdated()
    {
        return $this->last_updated;
    }

    /**
     * Set an array of market IDs for apps that are related to this one.
     *
     * @param array $apps
     *
     * @return void
     */
    public function setRelated($apps = array())
    {
        $this->related = $apps;
    }

    /**
     * Add the market ID of an app that is related to this one.
     *
     * @param string $app
     *
     * @return void
     */
    public function addRelated($app)
    {
        $this->related[] = $app;
    }

    /**
     * Get apps that are related to this. The returned array just contains market
     * IDs; if you want more data, run a get() call on the ID.
     *
     * @return array
     */
    public function getRelated()
    {
        return $this->related;
    }
}
