# ScandiPWA SliderGraphQl

Fork of [scandipwa/slider-graphql](https://github.com/scandipwa/slider-graphql) 2.1.1, maintained by Selveq for Magento 2.4.9 and PHP 8.3. Module name and namespace are unchanged, and the package replaces `scandipwa/slider-graphql` at every version, so it installs as a drop-in replacement. Selveq is not affiliated with or endorsed by Scandiweb.

## What it does

- Serves the `scandiwebSlider(id)` query, whose `Slider`, `Slide` and `Map` types carry a Scandiweb Slider slider with its active, in-schedule, store-scoped slides and their active hotspots, every image path media-prefixed.
- Answers `null` with a not-found error for a slider that is disabled or does not exist.
- Declares the answer's cache identity, the slider's tag and its slides', so a store that allows the query can cache and invalidate it.
- Feeds the ScandiPWA theme's slider widget over the persisted-query path, and `route717`, which preloads a CMS page's slider through `getSlider()` and hands it to that widget.

## Install

```sh
composer require selveq/slider-graphql
bin/magento setup:upgrade
```

## License

[OSL-3.0](LICENSE), the license of the original work. Scandiweb's copyright notices are kept in every file, and each file Selveq changed carries a `Modifications © Selveq` notice.
