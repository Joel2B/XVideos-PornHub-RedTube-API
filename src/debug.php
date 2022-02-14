<?php

    include 'config.php';

    if (!DEBUG_PAGE || empty($_GET['site_id'])) {
        die();
    }

    define('DEBUG', true);

    include 'inc/video.php';

    $site_id = $_GET['site_id'];

    $sites = [
        'xvideos' => '59934029',
        'pornhub' => 'ph6116a13a48187',
        'redtube' => '39697741',
    ];

    $data['site_id']  = $site_id;
    $data['video_id'] = $_GET['video_id'] ?? $sites[$site_id];

    LoadTime::start('total load');

    $video = new Video($data);
    $data  = $video->get_links();

    LoadTime::end('total load');

    _msg::msg($data);

    _msg::msg('memory usage', (memory_get_peak_usage(true) / 1024 / 1024) . 'MiB');

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>Debug</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                width: 100%;
                height: 100%;
                position: absolute;
            }

            .sites {
                display: flex;
            }

            .sites a {
                text-decoration: none;
                color: #000;
                font-family: sans-serif;
                padding: 10px;
                margin: 3px;
                border: 1px solid;
                border-radius: 4px;
            }

            #video-player {
                width: 50%;
            }
        </style>
    </head>
    <body>
        <div class="sites">
            <a href="./debug.php?site_id=xvideos">xvideos</a>
            <a href="./debug.php?site_id=pornhub">pornhub</a>
            <a href="./debug.php?site_id=redtube">redtube</a>
        </div>
        <video id="video-player">
            <?php
                if (!empty($data)) {
                    if ($site_id === 'xvideos') {
                        echo "<source src=\"{$data['hls']['all']}\" type=\"application/x-mpegURL\">";
                        echo "<source src=\"{$data['mp4']['high']}\" type=\"video/mp4\">";
                        echo "<source src=\"{$data['mp4']['low']}\" type=\"video/mp4\">";
                    } elseif ($site_id === 'pornhub' || $site_id === 'redtube') {
                        if ($site_id === 'redtube') {
                            echo "<source src=\"{$data['hls']['all']}\" type=\"application/x-mpegURL\">";
                        }
                        echo "<source src=\"{$data['mp4']['1080p']}\" type=\"video/mp4\">";
                        echo "<source src=\"{$data['mp4']['720p']}\" type=\"video/mp4\">";
                        echo "<source src=\"{$data['mp4']['480p']}\" type=\"video/mp4\">";
                        echo "<source src=\"{$data['mp4']['240p']}\" type=\"video/mp4\">";
                    }
                }
            ?>
        </video>
        <script src="https://appsdev.cyou/player/v1/current/player.min.js"></script>
        <script>
            <?php

                if (!empty($data)):

            ?>
            let instance = fluidPlayer('video-player', {
                layoutControls: {
                    mute: true,
                    posterImage: '<?php echo $data['thumb']; ?>',
                    timelinePreview: {
                        file: '<?php echo $data['thumbnails']; ?>',
                        type: 'VTT'
                    },
                },
                hls: {
                    overrideNative: true,
                },
                debug: true,
            });

            window.scrollTo({
                left: 0,
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });

            <?php

                endif;

            ?>
        </script>
    </body>
</html>
