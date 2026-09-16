# Changelog

## 3.0.0

Forked from `scandipwa/slider-graphql` 2.1.1. Module name and namespace are unchanged, and the package replaces `scandipwa/slider-graphql` at every version, so it installs as a drop-in replacement.

- A disabled slider is no longer served: `scandiwebSlider` answers for a slider whose `is_active` is 0 exactly as it does for one that does not exist.
- A slider that does not exist answers `null` with one `graphql-no-such-entity` error, the way core's `cmsPage` answers for an unknown identifier, instead of an object whose every field is null; the message is deliberately generic and names no id, and an id that is not a plain number — `-1`, `abc`, an empty string — answers the same.
- `scandiwebSlider` declares a cache identity, so its answer carries the slider's and its slides' invalidation tags (`sw_sldr`, `sw_sldr_<id>`, `sw_sld_<slide id>`) and can be cached once a store allows the query.
- `getSlider()` takes a string and returns `null` for a missing slider instead of an untyped value and a one-key array, so a caller that preloads a slider onto a page can tell that case from a real slider.
- All six slide image fields are media-prefixed; 2.1.1 prefixed only `mobile_image` and `desktop_image`, leaving the second and third blocks' four fields without it.
- The empty `@doc` descriptions on the `Slider`, `Slide` and `Map` types are replaced with real ones, leaving the type and field set unchanged.
