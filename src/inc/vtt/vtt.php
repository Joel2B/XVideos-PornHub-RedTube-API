<?php

if (count(get_included_files()) == 1) {
    die();
}

class Vtt {
    public $thumb_base_url;

    public $duration;
    public $type_thumb;
    public $total_links;
    public $sampling_frequency;

    public $cols;
    public $rows;
    public $thumb_width;
    public $thumb_height;

    public $x = 0;
    public $y = 0;
    public $w;
    public $h;

    public $encode;

    public function __construct($data) {
        // TODO: there must be better ways to do this
        $this->thumb_base_url     = $data['url'];
        $this->duration           = $data['duration'];
        $this->type_thumb         = $data['type_thumb'];
        $this->total_links        = $data['total_links'];
        $this->sampling_frequency = $data['sampling_frequency'];
        $this->cols               = $data['cols'];
        $this->rows               = $data['rows'];
        $this->thumb_width        = $data['width'];
        $this->thumb_height       = $data['height'];
        $this->w                  = $data['width'];
        $this->h                  = $data['height'];
        $this->encode             = isset($data['encode']) ? $data['encode'] : false;
    }

    public function print_vtt() {
        $thumbs_per_link = $this->cols * $this->rows;
        $time_elapsed    = 0;

        echo "WEBVTT\n\n";
        for ($i = 0; $i <= $this->total_links; $i++) {
            $current_col = 1;
            $current_row = 1;
            for ($j = 0; $j < $thumbs_per_link; $j++) {
                $from = gmdate('H:i:s', $time_elapsed);
                if ($this->type_thumb == 'single') {
                    $thumb_url = $this->thumb_base_url;
                    $time_elapsed += ($this->duration / $thumbs_per_link);
                } else {
                    if ($time_elapsed >= $this->duration) {
                        break;
                    }
                    $time_elapsed += $this->sampling_frequency;
                    $thumb_url = $this->thumb_base_url . $i . '.jpg';
                }
                if ($this->encode) {
                    $path      = dirname($_SERVER['PHP_SELF']);
                    $path      = explode('/', $path);
                    $path      = array_splice($path, 0, -2);
                    $path      = implode('/', $path);
                    $thumb_url = $path . '/redirect/' . (new Encryption('vtt', false))->encrypt($thumb_url);
                }
                $to = gmdate('H:i:s', $time_elapsed);
                echo "$from.000 --> $to.000\n$thumb_url#xywh={$this->x},{$this->y},{$this->w},{$this->h}\n\n";
                if ($current_col == $this->cols) {
                    if ($current_row == $this->rows) {
                        $current_row = 1;
                        $this->y     = 0;
                    } else {
                        $this->y += $this->thumb_height;
                        $current_row++;
                    }
                    $current_col = 1;
                    $this->x     = 0;
                } else {
                    $current_col++;
                    $this->x += $this->thumb_width;
                }
            }
        }
    }
}
