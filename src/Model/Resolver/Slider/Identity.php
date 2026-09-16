<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_SliderGraphQl
 * @copyright   Copyright © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace ScandiPWA\SliderGraphQl\Model\Resolver\Slider;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;
use Scandiweb\Slider\Model\Slide;
use Scandiweb\Slider\Model\Slider;

class Identity implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdentities(array $resolvedData): array
    {
        $sliderId = $resolvedData['slider_id'] ?? null;

        // an empty list tells the cache the answer carries nothing worth invalidating
        if ($sliderId === null) {
            return [];
        }

        $identities = [Slider::CACHE_TAG, Slider::CACHE_TAG . '_' . $sliderId];

        // a hotspot invalidates through its slide, so the maps in the answer need no tag of their own
        foreach ($resolvedData['slides'] ?? [] as $slide) {
            $identities[] = Slide::CACHE_TAG . '_' . $slide['slide_id'];
        }

        return $identities;
    }
}
