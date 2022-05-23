# XVideos PornHub RedTube API

This script scrapes the HTML from different web pages to get the information from the video and you can use it in your own video player.

## Features

- Get video sources (MP4, HLS)
- Get thumbnail (poster)
- Get thumbnails for preview (VTT)
- Add new sites / servers
- Data caching

## Installation

Copy the [dir](/src) to the root of a web server running PHP 7.

## Usage

Only get requests are accepted, so data can be entered directly in url

| Parameter |                              Description                               |
| :-------: | :--------------------------------------------------------------------: |
|  site_id  | id of the site that wants to get the video (xvideos, pornhub, redtube) |
| video_id  |              id of the video that corresponds to the site              |
|   data    |     link to the video, with at least the domain name and video id      |

## Example

```php
<?php

$data = file_get_contents('https://appsdev.cyou/xv-ph-rt/api/?site_id=xvideos&video_id=59934029');

echo '<pre>';
echo json_encode(json_decode($data), JSON_PRETTY_PRINT);
echo '</pre>';
```

Output

```json
{
    "hls": {
        "all": "https:\/\/cdn77-vid.xvideos-cdn.com\/OydUr0ucV-1QWXmSI8PZ2Q==,1653275126\/videos\/hls\/de\/b6\/c0\/deb6c040575ef28dee0d5a0240c4b04d-1\/hls.m3u8"
    },
    "mp4": {
        "high": "",
        "low": "https:\/\/cdn77-vid-mp4.xvideos-cdn.com\/eBuh1wuU8JQIhDfyyfbtQg==,1653275128\/videos\/3gp\/d\/e\/b\/xvideos.com_deb6c040575ef28dee0d5a0240c4b04d-1.mp4?ui=MTg1LjM3LjIzMS4xMTItL2VtYmVkZnJhbWUvNTk5MzQwMjk_cj0xNjUzMjY0"
    },
    "thumb": "https:\/\/cdn77-pic.xvideos-cdn.com\/videos\/thumbs169lll\/de\/b6\/c0\/deb6c040575ef28dee0d5a0240c4b04d-1\/deb6c040575ef28dee0d5a0240c4b04d.25.jpg",
    "thumbnails": "https:\/\/appsdev.cyou\/xv-ph-rt\/api\/vtt\/eHZpZGVvc3h4eHh4eHh4eIQuPOZKjQDosFrBVWNZOX1KtJt9dZbonjIEXI50IzTCrzqFJoAHlahbZQvC2DdXp-Fx1XhsmsiS643j1783hqaMKYgHFUVR1ph-tCBP2ByTCxE2ni-4TgPLbJJgPrhn1zkDstz7gokdykVZJHnq4Xv0BMKHt801XamPlei3llRW7v1uzFOnKJ816vAHvWwF42CXwAaYtmgOn0VyAW2T6f2zhWaYfFEmAZl0RrlQglvURO7i6RCl5hczo3lOqSt_Oi3U0_jVE_izvU6I0118vTxYFednTzqPgvKCtULWdaJ1anu2mVchMKx3RVG3EOHI-ErxgCCV8YoY4WSVrGmF25o="
}
```

## Example online

[https://appsdev.cyou/xv-ph-rt/](https://appsdev.cyou/xv-ph-rt/)

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
