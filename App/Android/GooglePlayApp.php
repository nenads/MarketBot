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

namespace PastFuture\MarketBot\App\Android;

use PastFuture\MarketBot;

/**
 * Google Play App
 *
 * @package MarketBot
 * @author Jon Ursenbach <jon@gdgt.com>
 * @since 0.1
 */
class GooglePlayApp extends MarketBot\App\AndroidApp
{
    /**
     * Website URL
     *
     * @var string|false
     */
    protected $website_url = false;

    /**
     * Developer email
     *
     * @var string|false
     */
    protected $developer_email = false;

    /**
     * More apps from this developer.
     *
     * @var array
     */
    protected $more_from_developer = array();

    /**
     * Other apps users have installed.
     *
     * @var array
     */
    protected $users_also_installed = array();

    /**
     * Images
     *
     * @var array
     */
    protected $images = array(
        'thumbnail' => false,
        'icon' => false,
        'icon_large' => false,
        'banner' => false
    );

    /**
     * Videos
     *
     * @var array
     */
    protected $videos = array();

    /**
     * Product category
     *
     * @var string
     */
    protected $category;

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
            if ($price == 'install') {
                $price = '0.00';
            } else {
                $price = str_replace('buy', '', $price);
                $price = str_replace('$', '', $price);
                $price = trim($price);
            }

            parent::setPrice($price);
        }
    }

    /**
     * Set the website URL for this app. This is not the market URL, but the
     * website that the developer has set up.
     *
     * @param string $website
     *
     * @return void
     */
    public function setWebsiteUrl($website)
    {
        $this->website_url = $website;
    }

    /**
     * Get the website URL.
     *
     * @return string
     */
    public function getWebsiteUrl()
    {
        return $this->website_url;
    }

    /**
     * Set the developer's email.
     *
     * @param string $email
     *
     * @return void
     */
    public function setDeveloperEmail($email)
    {
        if (!empty($email)) {
            $this->developer_email = $email;
        }
    }

    /**
     * Get the developer's email.
     *
     * @return string
     */
    public function getDeveloperEmail()
    {
        return $this->developer_email;
    }

    /**
     * Add the market ID of an app that this developer has created.
     *
     * @param string $app
     *
     * @return void
     */
    public function addMoreFromDeveloper($app)
    {
        $this->more_from_developer[] = $app;
    }

    /**
     * Get more apps that this developer has created. The returned array just
     * contains market IDs; if you want more data, run a get() call on the ID.
     *
     * @return array
     */
    public function getMoreFromDeveloper()
    {
        return $this->more_from_developer;
    }

    /**
     * Add the market ID of an app that users have also installed.
     *
     * @param array $app
     *
     * @return void
     */
    public function addUsersAlsoInstalled($app)
    {
        $this->users_also_installed[] = $app;
    }

    /**
     * Get apps that users have also installed. The returned array just contains
     * market IDs; if you want more data, run a get() call on the ID.
     *
     * @return array
     */
    public function getUsersAlsoInstalled()
    {
        return $this->users_also_installed;
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
            if (strpos($image, '=w78-h78') === false) {
                $image = substr($image, 0, strpos($image, '=w'));
                $image .= '=w78-h78';
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
            if (strpos($image, '=w124') === false) {
                $image = substr($image, 0, strpos($image, '=w'));
                $image .= '=w124';
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
     * Set the large image icon.
     *
     * @param string $image
     *
     * @return void
     */
    public function setImageIconLarge($image)
    {
        if (!empty($image)) {
            if (strpos($image, '=w512') === false) {
                $image = substr($image, 0, strpos($image, '=w'));
                $image .= '=w512';
            }

            $this->images['icon_large'] = $image;
        }
    }

    /**
     * Get the large image icon.
     *
     * @return string
     */
    public function getImageIconlarge()
    {
        return $this->images['icon_large'];
    }

    /**
     * Set the image banner.
     *
     * @param string $image
     *
     * @return void
     */
    public function setImageBanner($image)
    {
        if (!empty($image)) {
            if (strpos($image, '=w1024') === false) {
                $image = substr($image, 0, strpos($image, '=w'));
                $image .= '=w1024';
            }

            $this->images['banner'] = $image;
        }
    }

    /**
     * Get the image banner.
     *
     * @return stirng
     */
    public function getImageBanner()
    {
        return $this->images['banner'];
    }

    /**
     * Add a video.
     *
     * @param string $video
     *
     * @return array
     */
    public function addVideo($video)
    {
        $this->videos[] = $video;
    }

    /**
     * Get videos.
     *
     * @return array
     */
    public function getVideos()
    {
        return $this->videos;
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
     * Add a permission this app utilizes.
     *
     * @param array $permission
     *
     * @return void
     */
    public function addPermission($permission)
    {
        $this->permissions[] = $permission;
    }
}
