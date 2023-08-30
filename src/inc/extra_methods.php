<?php

class Extra_methods {
    public function pornhub() {
        Performance::now($this->data);

        // this avoids making more requests
        if ($this->tmp === $this->data) {
            return;
        }

        // ph breaks video sources into fragments
        // TODO: for now the last "media" has everything, if ph makes it random, this code needs to be changed.
        if (!preg_match("/(media_[0-9]);\n\t\tvar/", $this->full_content, $media)) {
            return;
        }

        $media = $media[1];
        $data  = explode('_', $media);
        $data[1] -= 1;
        $previous = implode('_', $data);

        if (!preg_match("/$previous;(.*?);var $media/", $this->full_content, $raw_variables)) {
            if (preg_match("/'];\n\t\t(.*?);var media/", $this->full_content, $raw_variables)) {
                $media = 'media_0';
            } else {
                return;
            }
        }

        preg_match_all('/var (.*?(| )=(| )".*?")(;|$)/', $raw_variables[1], $parsed_variable);

        $vars = [];

        foreach ($parsed_variable[1] as $value) {
            $var           = explode('=', str_replace(['"', '+', ' '], ['', '', ''], $value), 2);
            $vars[$var[0]] = $var[1];
        }

        preg_match("/var $media=(.*?);/", $this->full_content, $match);
        preg_match_all("/(\*\/|\*\/ \+)(| )(\w+)(| )(\+|\/\*|)/", $match[1], $solution);

        $link = '';

        foreach ($solution[3] as $value) {
            $link .= $vars[$value];
        }

        $format = 'a';

        if ($this->data === 'mp4') {
            $format = 'p';
        }

        $link = substr($link, 0, strlen($link) - 1) . $format;

        if (!empty($this->own_server)) {
            $url = str_replace('{url}', urlencode($link), $this->own_server);
            $url .= '&cookie_id=' . $this->server_id;
            $link = $url;
        }

        $this->new_content = Utils::get_url_content($link, true);

        $this->tmp = $this->data;

        Performance::now($this->data);
    }

    public function redtube() {
        Performance::now($this->data);

        // this avoids making more requests
        if ($this->tmp === $this->data) {
            return;
        }

        if (!preg_match('/hls","videoUrl":"(.*?)"/', $this->full_content, $link)) {
            return;
        }

        $format = 'hls';

        if ($this->data === 'mp4') {
            $format = 'mp4';
        }

        $link              = str_replace(['\/', 'hls'], ['/', $format], $link[1]);
        $this->new_content = Utils::get_url_content($link, true);

        $this->tmp = $this->data;

        Performance::now($this->data);
    }

    public function ms() {
        $this->new_content = null;

        Performance::now('mothersleep');

        if (!preg_match('/source/', $this->full_content, $match)) {
            return;
        }

        if ($this->data === 'hls' && preg_match('/src="(.*m3u8.*?)"/', $this->full_content, $match)) {
            $this->new_content = $match[1];
        } else if ($this->data === 'mp4' && preg_match('/src="(.*mp4.*?)"/', $this->full_content, $match)) {
            $this->new_content = $match[1];
        } else {
            // check if the video source is encoded
            if (!preg_match('/t">\s+(.*?)\s+var/', $this->full_content, $raw_variables)) {
                return;
            }

            preg_match_all('/var (.*?=".*?");/', $raw_variables[1], $parsed_variable);

            $vars = [];

            foreach ($parsed_variable[1] as $value) {
                $var           = explode('=', str_replace(['"', '+', ' '], ['', '', ''], $value), 2);
                $vars[$var[0]] = $var[1];
            }

            preg_match_all("/(\w+\+|\w+;)/", $this->full_content, $solution);

            $link = '';

            foreach ($solution[1] as $value) {
                $link .= $vars[str_replace(['+', ';'], ['', ''], $value)];
            }

            $this->new_content = Utils::get_redirect_url($link);

            if ($this->data === 'hls' && preg_match('/(.*m3u8.*)/', $this->new_content, $match)) {
                $this->new_content = $match[1];
            } else if ($this->data === 'mp4' && preg_match('/(.*mp4.*)/', $this->new_content, $match)) {
                $this->new_content = $match[1];
            } else {
                $this->new_content = '';
            }
        }

        Performance::now('mothersleep');
    }

    public function iporntv() {
        Performance::now('iporntv');

        if (!preg_match('/source/', $this->full_content, $match)) {
            return;
        }

        if ($this->data === 'hls' && preg_match('/src="(.*m3u8.*?)"/', $this->full_content, $match)) {
            $this->new_content = $match[1];
        } else if ($this->data === 'mp4' && preg_match('/src="(.*mp4.*?)"/', $this->full_content, $match)) {
            $this->new_content = $match[1];
        } else {
            // check if the video source is encoded
            if (!preg_match('/t">\s+(.*?)\s+var/', $this->full_content, $raw_variables)) {
                return;
            }

            preg_match_all('/var (.*?=".*?");/', $raw_variables[1], $parsed_variable);

            $vars = [];

            foreach ($parsed_variable[1] as $value) {
                $var           = explode('=', str_replace(['"', '+', ' '], ['', '', ''], $value), 2);
                $vars[$var[0]] = $var[1];
            }

            preg_match("/;var \w+ =((\w+|\+)+)/", $this->full_content, $solution);
            preg_match_all("/(\w+\+|\w+)/", $solution[1], $parsed_solution);

            $link = '';

            foreach ($parsed_solution[1] as $value) {
                $link .= $vars[str_replace(['+', ';'], ['', ''], $value)];
            }

            $this->new_content = Utils::get_redirect_url($link);
        }

        Performance::now('iporntv');
    }

    public function tubebaba() {
        $this->new_content = base64_decode($this->full_content);
    }

    public function embed_mp4_center() {
        $this->new_content = Utils::get_url_content($this->full_content);
    }

    public function xvideos_xyz() {
        $this->new_content = Utils::get_redirect_url($this->full_content);
    }

    // TODO: refactor these methods
    public function get_thumnails_xv() {
        $this->data['thumbnails'] = '';

        $thumb    = $this->data['thumb'];
        $duration = $this->data['duration'];

        if (empty($thumb) || !is_numeric($duration)) {
            $this->new_content = $this->data;
            return;
        }

        $thumb       = str_replace(['poster', 'lll', 'll'], ['', '', ''], $thumb);
        $thumb       = substr($thumb, 0, strrpos($thumb, '/') + 1);
        $total_links = 0;

        if ($duration <= 60 || Utils::get_http_code($thumb . 'mozaiquemin_0.jpg') === 404) {
            $thumb .= 'mozaiquefull.jpg';
            $type_thumb = 'single';
        } else {
            $thumb .= 'mozaiquemin_';
            $type_thumb  = 'multiple';
            $total_links = floor($duration / 60);
        }

        $thumb_data = [
            'url'                => $thumb,
            'duration'           => $duration,
            'type_thumb'         => $type_thumb,
            'total_links'        => $total_links,
            'sampling_frequency' => 1,
        ];

        $thumb_data               = array_merge($thumb_data, $this->full_content['thumbnails'][$type_thumb]);
        $thumb_data               = (new Encryption($this->site_id, false))->encrypt(json_encode($thumb_data));
        $this->data['thumbnails'] = PATH . "vtt/$thumb_data";
        $this->new_content        = $this->data;
    }

    public function get_thumnails_ph() {
        $url = $this->data['thumbnails'];

        $this->data['thumbnails'] = '';

        if (empty($url)) {
            $this->new_content = $this->data;
            return;
        }

        preg_match("/{(.*)}/", $url, $total_links);

        $url = str_replace('\/', '/', $url);

        if (strpos($url, ')') !== false) {
            $url = substr($url, 0, strrpos($url, ')') + 2);
        } else {
            $url = substr($url, 0, strrpos($url, '/') + 1);
        }

        $type_thumb = 'multiple';

        $thumb_data = [
            'url'                => $url,
            'duration'           => $this->data['duration'],
            'type_thumb'         => $type_thumb,
            'total_links'        => $total_links[1],
            'sampling_frequency' => $this->data['sampling_frequency'],
        ];

        $thumb_data               = array_merge($thumb_data, $this->full_content['thumbnails'][$type_thumb]);
        $thumb_data['encode']     = true;
        $thumb_data               = (new Encryption($this->site_id, false))->encrypt(json_encode($thumb_data));
        $this->data['thumbnails'] = PATH . "vtt/$thumb_data";
        $this->new_content        = $this->data;
    }
}
