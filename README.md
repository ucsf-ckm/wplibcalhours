# LibCal Hours for WordPress

Embed opening hours for any given location from LibCal into WordPress via short codes.

![Opening hours displayed in a published post](assets/screenshot-1.png)

## Installation

Since this plugin is not available for automatic installation, please follow these [Manual Plugin Installation](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation) steps.

## Configuration


![Plugin settings](assets/screenshot-2.png)

In the plugin's Admin Settings form, enter the LibCal Hours API Endpoint URL and save the form.

Instructions on how to get that URL can be found on this [Wiki page](https://github.com/ucsf-ckm/wplibcalhours/wiki/How-to-get-the-LibCal-Hours-API-Endpoint-URL).

## Usage

![Embed shortcode into a post](assets/screenshot-3.png)

Embed the `[wplibcalhours]` short code into your posts and pages.

The short code has the following configuration options.

- `location` ... The name of the location that you want to display opening hours for. *(mandatory)*  
- `num_weeks` ... The number of weeks of opening hours to display. Accepted values are `1`, `2` and `3`. Defaults to `3`. *(optional)*.

## Styling

Please see this [Wiki page](https://github.com/ucsf-ckm/wplibcalhours/wiki/Styling-The-Output) for ideas on how to customize the styles of this plugin's generated markup.

## Attribution

This plugin was created using [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate).

## Copyright and License

Copyright (c) 2017 The Regents of the University of California

This is Open Source Software, published under the MIT license.
