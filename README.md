# XVideos PornHub RedTube API

This script scrapes the HTML from different web pages to get the information from the video and you can use it in your own video player.

## Features

* Get video sources (MP4, HLS)
* Get thumbnail (poster)
* Get thumbnails for preview (VTT)
* Add new sites / servers
* Data caching

## Installation

Copy the [dir](/src) to the root of a web server running PHP 7.

## Usage
Only get requests are accepted, so data can be entered directly in url

| Parameter | Description |
| :---: | :---: |
| site_id  | id of the site that wants to get the video (xvideos, pornhub, redtube)  |
| video_id  | id of the video that corresponds to the site  |
| data  | link to the video, with at least the domain name and video id  |

## Example
```php
<?php

$data = file_get_contents('http://localhost/api/?site_id=xvideos&video_id=59934029');
echo '<pre>';
print_r(json_decode($data, true));
echo '</pre>';

```
Output
```
Array
(
    [hls] => Array
        (
            [all] => https://cdn77-vid.xvideos-cdn.com/EzxFnSwy-9fX68oCPYYOhw==,1631685848/videos/hls/de/b6/c0/deb6c040575ef28dee0d5a0240c4b04d/hls.m3u8
        )

    [mp4] => Array
        (
            [high] => https://cdn77-vid.xvideos-cdn.com/ZeDUyLXiZDKCXlc_9G72Fg==,1631685848/videos/mp4/d/e/b/xvideos.com_deb6c040575ef28dee0d5a0240c4b04d.mp4?ui=MTg1LjE1Ni4yMTkuMTQ0LS9lbWJlZGZyYW1lLzU5OTM0MDI5P3I9MTYzMTY3NQ==
            [low] => https://cdn77-vid.xvideos-cdn.com/XSSyMypFsDouI49r6eYB1Q==,1631685848/videos/3gp/d/e/b/xvideos.com_deb6c040575ef28dee0d5a0240c4b04d.mp4?ui=MTg1LjE1Ni4yMTkuMTQ0LS9lbWJlZGZyYW1lLzU5OTM0MDI5P3I9MTYzMTY3NQ==
        )

    [thumb] => https://cdn77-pic.xvideos-cdn.com/videos/thumbs169lll/de/b6/c0/deb6c040575ef28dee0d5a0240c4b04d/deb6c040575ef28dee0d5a0240c4b04d.25.jpg
    [thumbnails] => http://localhost/api/vtt/eHZpZGVvc3h4eHh4eHh4eIQuPOZKjQDosFrBVWNZOX1KtJt9dZbonjIEXI50IzTCrzqFJoAHlahbZQvC2DdXp-Fx1XhsmsiS643j1783hqaMKYgHFUVR1ph-tCBP2ByTCxE2ni-4TgPLbJJgPrhn10OGgFbkncuOfWDjBrXTo_JBJVbsCq85Ipu_heIsBn3i3bUILlOw6zTr3HihbIGhvlgHiZc7ZG4nU0ra1FpYV5plkTAn8SQwnPyR9mGsujpEurWSj2YRL3Nd2fAlAn-CC93AYFt_YaSS2HA2ZtJw6J-W6wiO0V3V4DYuciNb2BB5lFd7N9sUfFnu9PPSj2Xnx9dsr8vFrbpwha17rAepm4c=
)
```

## Example online

[https://watchonline.nom.es/example/xv-ph-rt/](https://watchonline.nom.es/example/xv-ph-rt/)

## How to add more sites / servers

There's a file [sources.json](src/servers/sources.json), that has the information of all the sites to which the HTML will be extracted, in this it's indicated all the data that can be extracted to the site, for example, HLS, MP4, thumbnails.

> It's recommended that the sites have different IP addresses.

In case of adding your own site, add folder [api-for-own-servers](api-for-own-servers) anywhere on your site so that data can be extracted from it.

### Categorize sites

To avoid making dozens of requests and waiting for them all to complete, sites are categorized by loading speed, the first ones will be the fastest (1s, 2s), and the next ones will be slower (3s, 4s, 5s).

#### How it work

The data is extracted from all the sites in the first category, and if all the data is obtained, no more data is extracted, otherwise, it will continue with the next category until all the data is obtained.

### Caution

By default there's a list of sites to obtain data but as they're from third parties there is a risk that they may stop working, so it's recommended that you add your own sites / servers.

