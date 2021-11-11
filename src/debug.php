<?php

    include 'config.php';
    define('DEBUG', true);
    if (!DEBUG_PAGE) {
        die();
    }

    include 'inc/video.php';

    $site_id  = 'xvideos';
    $video_id = '59934029';

    // $site_id  = 'pornhub';
    // $video_id = 'ph6116a13a48187';

    // $site_id  = 'redtube';
    // $video_id = '39518521';

    LoadTime::start('total load');

    $data['site_id']  = $site_id;
    $data['video_id'] = $video_id;

    $video = new Video($data);
    $data  = $video->get_links();
    _msg::msg($data);

    LoadTime::end('total load');

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

            #video-player {
                width: 50%;
            }
        </style>
    </head>
    <body>
        <video id="video-player">
            <!-- xvideos -->
            <source src="<?php echo $data['hls']['all']; ?>" type="application/x-mpegURL" />
            <!-- <source src="<?php echo $data['mp4']['high']; ?>" type="video/mp4" />
            <source src="<?php echo $data['mp4']['low']; ?>" type="video/mp4" /> -->
            <!-- pornhub / redtube -->
            <!-- <source src="<?php echo $data['hls']['all']; ?>" type="application/x-mpegURL" />
            <source src="<?php echo $data['mp4']['1080p']; ?>" type="video/mp4" />
            <source src="<?php echo $data['mp4']['720p']; ?>" type="video/mp4" />
            <source src="<?php echo $data['mp4']['480p']; ?>" type="video/mp4" />
            <source src="<?php echo $data['mp4']['240p']; ?>" type="video/mp4" /> -->
        </video>
        <script src="https://appsdev.cyou/player/v1/current/player.min.js"></script>
        <script>
            var instance = fluidPlayer('video-player', {
                layoutControls: {
                    posterImage: '<?php echo $data['thumb']; ?>',
                    loop: true,
                    timelinePreview: {
                        file: '<?php echo $data['thumbnails']; ?>',
                        type: 'VTT'
                    },
                    playPauseAnimation: true,
                    menu: {
                        loop: true
                    }
                }
            });
        </script>
    </body>
</html>
