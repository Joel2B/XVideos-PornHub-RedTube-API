<?php

include 'utils.php';
include 'cache.php';
include 'extra_steps.php';

include 'tests/load-time.php';
include 'msg.php';

class Video {
    public $site_id;
    public $video_id;

    public $data;
    public $extra_steps;
    public $cache;

    public function __construct($data) {
        if (isset($data['data'])) {
            $this->site_id = $this->get_site_id($data['data']);

            if (!isset($this->site_id)) {
                die();
            }

            $this->load_sources();

            $this->video_id = $this->get_video_id($this->data['video_id'], $data['data']);

            if (!isset($this->video_id)) {
                die();
            }
        } else {
            $this->site_id = $data['site_id'];
            $this->load_sources();
            $this->video_id = $data['video_id'];
        }

        // load metadata
        $this->metadata = $this->data['metadata'];

        // extra steps to get the data
        $this->extra_steps = new Extra_steps($this->site_id);

        // data caching
        $this->cache = new Cache($this->site_id, $this->video_id, $this->data['expiration_time']);
    }

    public function load_sources() {
        // load all the information from the websites
        $this->sources = json_decode(file_get_contents(__DIR__ . '/../servers/sources.json'), true);

        if (!isset($this->sources[$this->site_id])) {
            die();
        }

        $this->data = $this->sources[$this->site_id];
    }

    public function get_site_id($data) {
        return Utils::_match('(?:\.|^)(\w+)\..*\/', $data);
    }

    public function get_video_id($regex, $data) {
        return Utils::_match($regex, $data);
    }

    public function get_data() {
        if ($this->cache->check()) {
            $this->metadata = $this->cache->data;

            return;
        }

        // websites grouped by load times and achievable data
        $categories = [
            // must be able to get all the data
            'primary',
            'secundary',
            // some other data is obtained
            'load-time-1',
            'load-time-2',
            'load-time-3',
            // penultimate alternative for obtaining the missing data
            'final',
            // last alternative to get the data
            'origin-ip',
        ];

        $missing_data = [];

        // add all the data that need to be filled out
        Utils::get_deeper_keys($this->metadata, $missing_data);

        _msg::msg('missing_data', $missing_data);

        // cycle all the categories until we get all the data
        foreach ($categories as $key => $category) {
            _msg::msg("Category: $key - $category");

            // get the content of all sites in a category
            $content  = $this->get_links_content($category);
            $continue = false;

            LoadTime::start('lc');

            foreach ($content as $server_index => $full_content) {
                $server     = $this->sources[$this->site_id]['servers'][$server_index];
                $own_server = preg_match('/{url}/', $server['url']) ? $server['url'] : '';
                $content    = $full_content;

                // media
                if (isset($server['media'])) {
                    $media = $server['media'];

                    foreach ($media as $format_index => $format) {
                        foreach ($format as $quality_index => $quality) {
                            if ($this->extra_steps->extra_steps('media', $server['id'], $full_content, $format_index, $own_server)) {
                                $content = $this->extra_steps->new_content;
                            }

                            $data = &$this->metadata['media'][$format_index][$quality_index];
                            $url  = Utils::_match($quality, $content);
                            $url  = Utils::clear_url($url);

                            if (!Utils::is_url($url)) {
                                Utils::add_missing_data($data, $quality_index, $missing_data);
                                continue;
                            }

                            if ($data === '') {
                                $data = $url;
                                Utils::remove_missing_data($quality_index, $missing_data);
                            }
                        }
                    }
                }

                // data
                if (isset($server['data'])) {
                    foreach ($server['data'] as $data_index => $video_data) {
                        if ($this->extra_steps->extra_steps('data', $server['id'], $full_content, $data_index)) {
                            $content = $this->extra_steps->new_content;
                        }

                        $data = &$this->metadata['data'][$data_index];
                        $url  = Utils::_match($video_data, $content);
                        $url  = Utils::clear_url($url);

                        if ($url === '') {
                            Utils::add_missing_data($data, $data_index, $missing_data);
                            continue;
                        }

                        if ($data === '') {
                            $data = $url;
                            Utils::remove_missing_data($data_index, $missing_data);
                        }
                    }}
            }

            LoadTime::end('lc');

            _msg::msg('missing_data', $missing_data);

            if (Utils::in_array_any($this->data['include'], $missing_data)) {
                $continue = true;
            }

            // if the necessary data is obtained, the search is stopped
            if (!$continue) {
                if (!$this->cache->check()) {
                    if (!Utils::is_empty_array($this->metadata['media'])) {
                        $this->cache->save($this->metadata);
                    }
                }

                break;
            }
        }
    }

    public function get_links_content($category) {
        $urls    = [];
        $servers = $this->sources[$this->site_id]['servers'];

        foreach ($servers as $server_index => $server) {
            if (!in_array($category, $server['category'])) {
                continue;
            }

            $search = [
                '{site_id}',
                '{video_id}',
            ];

            $replace = [
                $this->site_id,
                $this->video_id,
            ];

            $url    = str_replace($search, $replace, $server['url']);
            $cookie = isset($server['cookie']);
            $bypass = isset($server['bypass']);

            // use own servers
            if (preg_match('/{url}/', $server['url'])) {
                $url = str_replace('{video_id}', $this->video_id, $this->data['url']);
                $url = str_replace('{url}', urlencode($url), $server['url']);

                if ($cookie) {
                    $url .= "&cookie_action=write&cookie_id={$server['id']}";
                }
            }

            $urls[$server_index] = [
                'url'    => $url,
                'bypass' => $bypass,
            ];

            _msg::msg('URL', "<a style='color: #0000ee' target='_blank' href='$url'>$url</a>", false);
        }

        return Utils::get_multiple_urls($urls);
    }

    public function get_derived_data() {
        $this->extra_steps->extra_steps('derived_data', '', $this->data, $this->metadata['data']);

        $this->metadata['data'] = $this->extra_steps->new_content;
    }

    public function remove_useful_data() {
        $this->extra_steps->extra_steps('remove_data', '', $this->data, $this->metadata);

        $this->metadata = $this->extra_steps->new_content;
    }

    public function get_links() {
        $this->get_data();
        $this->get_derived_data();
        $this->remove_useful_data();

        return $this->metadata;
    }
}
