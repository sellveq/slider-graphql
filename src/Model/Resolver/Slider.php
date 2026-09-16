<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_SliderGraphQl
 * @copyright   Copyright © 2018 Scandiweb, Ltd (https://scandiweb.com)
 * @copyright   Modifications © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace ScandiPWA\SliderGraphQl\Model\Resolver;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Scandiweb\Slider\Model\ResourceModel\Map\CollectionFactory as MapCollectionFactory;
use Scandiweb\Slider\Model\ResourceModel\Slide\CollectionFactory as SlideCollectionFactory;
use Scandiweb\Slider\Model\ResourceModel\Slider\CollectionFactory as SliderCollectionFactory;

class Slider implements ResolverInterface
{
    // the storefront prepends only "/", so every image path leaves this resolver media-prefixed
    private const IMAGE_FIELDS = [
        'mobile_image',
        'desktop_image',
        'mobile_image_2',
        'desktop_image_2',
        'mobile_image_3',
        'desktop_image_3',
    ];

    /**
     * @param SliderCollectionFactory $sliderCollectionFactory
     * @param SlideCollectionFactory $slideCollectionFactory
     * @param MapCollectionFactory $mapCollectionFactory
     */
    public function __construct(
        private readonly SliderCollectionFactory $sliderCollectionFactory,
        private readonly SlideCollectionFactory $slideCollectionFactory,
        private readonly MapCollectionFactory $mapCollectionFactory
    ) {}

    /**
     * the slider row with its active slides and their active hotspots, or null when there is no such slider
     * @param string $id
     * @return array|null
     * @throws NoSuchEntityException
     */
    public function getSlider(string $id): ?array
    {
        // the column is an unsigned integer, so anything else is a missing slider rather than a query
        if (!ctype_digit($id)) {
            return null;
        }

        $sliderId = (int)$id;
        $slider = $this->sliderCollectionFactory->create();
        $slider->addFieldToFilter('slider_id', $sliderId)
            ->addFieldToFilter('is_active', 1)
            ->load();
        $sliderData = $slider->getFirstItem()->getData();

        // a disabled slider is a missing slider: nothing on the wire distinguishes the two
        if (!$sliderData) {
            return null;
        }

        $slides = $this->slideCollectionFactory->create();
        $slides->addSliderFilter($sliderId)
            ->addStoreFilter()
            ->addDateFilter()
            ->addIsActiveFilter()
            ->addPositionOrder();

        $sliderData['slides'] = $slides->getData();

        $maps = $this->mapCollectionFactory->create();
        $maps = $maps->addSliderFilter($sliderId)
            ->addIsActiveFilter()
            ->getItems();

        foreach ($sliderData['slides'] as &$slide) {
            foreach (self::IMAGE_FIELDS as $imageField) {
                if (isset($slide[$imageField])) {
                    $slide[$imageField] = DirectoryList::MEDIA . '/' . $slide[$imageField];
                }
            }
            foreach ($maps as $map) {
                if ((int)$map['slide_id'] === (int)$slide['slide_id']) {
                    $slide['maps'][] = $map;
                }
            }
        }

        unset($slide);

        return $sliderData;
    }

    /**
     * {@inheritdoc}
     * @throws GraphQlNoSuchEntityException
     * @throws NoSuchEntityException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        // the schema declares id as ID!, which reaches a resolver as a string and is never absent
        $sliderData = $this->getSlider((string)$args['id']);

        if ($sliderData === null) {
            // deliberately generic: an answer to an untrusted caller names no id and no reason
            throw new GraphQlNoSuchEntityException(__('The slider does not exist.'));
        }

        return $sliderData;
    }
}
