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
 * Amazon Appstore App
 *
 * @package MarketBot
 * @author Jon Ursenbach <jon@gdgt.com>
 * @since 0.1
 */
class AmazonAppstoreApp extends MarketBot\App\AndroidApp
{
    /**
     * Images
     *
     * @var array
     */
    protected $images = array(
        'thumbnail' => false,
        'icon' => false
    );

    protected $product_features = array();

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
            $price = str_replace('$', '', $price);
            $price = trim($price);

            parent::setPrice($price);
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
            $rating = str_replace(' out of 5 stars', '', $rating);
            parent::setRating($rating);
        }
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
     * Add a permission this app utilizes.
     *
     * @param string $type
     * @param string $text
     *
     * @return void
     */
    public function addPermission($type, $text)
    {
        $this->permissions[$type] = trim($text);
    }

    /**
     * Add a product feature.
     *
     * @param string $feature
     *
     * @return void
     */
    public function addProductFeature($feature)
    {
        $this->product_features[] = $feature;
    }

    /**
     * Get product features.
     *
     * @return array
     */
    public function getProductFeatures()
    {
        return $this->product_features;
    }
}
