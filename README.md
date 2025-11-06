# AtRest Show Ads Plugin

## Overview

For proper functionality on death-notices pages, the Advertiser must have the same email as the Funeral Director (FD). The email is used to link the death-notices post author to their Advertiser account.

## Shortcode Usage

Use the `[at_rest_show_ads]` shortcode to display advertisements.

### Parameters

-   **`type`** (default: `header`)

    -   Available options: `header`, `sidebar`, `death-notices`
    -   The `death-notices` type is specifically designed for death-notice post pages and will not display on other page types

-   **`max_repeat_count`** (default: `50`)

    -   Maximum number of ad rotations allowed per page session
    -   Resets on page reload
    -   Prevents infinite server requests if a user leaves the page open

-   **`duration`** (default: `30`)

    -   Time in seconds between ad rotations

-   **`per_page`** (default: `1`)
    -   Number of advertisements to display in a single block

### Example for DN single page

```
[at_rest_show_ads type="death-notices" duration="45" per_page="3" max_repeat_count="100"]
```
