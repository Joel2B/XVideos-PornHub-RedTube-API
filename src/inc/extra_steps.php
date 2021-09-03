<?php

include 'encryption.php';
include 'extra_methods.php';

// TODO: use interfaces for everything
class Extra_steps extends Extra_methods {
    // xv, ph, rt
    public $site_id;
    // id of a website
    public $server_id;
    // content of the website
    public $full_content;
    // if there were extra steps, a new content is used
    public $new_content = null;

    // variables to perform different operations
    public $data;
    public $own_server;
    public $tmp  = null;
    public $tmp2 = null;

    public function __construct($site_id) {
        $this->site_id = $site_id;
    }

    public function extra_steps(
        $section,
        $server_id,
        $full_content,
        $data = '',
        $own_server = ''
    ) {
        $this->full_content = $full_content;
        $this->data         = $data;
        $this->own_server   = $own_server;
        $return             = true;
        switch ($this->site_id) {
            case 'xvideos':
                switch ($section) {
                    case 'media':
                        switch ($server_id) {
                            case 'ms':
                                $this->ms();
                                break;
                            case 'iporntv':
                                $this->iporntv();
                                break;
                            case 'tubebaba':
                                $this->tubebaba();
                            case 'embed.mp4.center':
                            case 'embed.mp4.center2':
                                $this->embed_mp4_center();
                                break;
                            case 'xvideos.xyz':
                                $this->xvideos_xyz();
                                break;
                            default:
                                return;
                                $return = false;
                                break;
                        }
                        break;
                    case 'data':
                        switch ($data) {
                            default:
                                $return = false;
                                break;
                        }
                        break;
                    case 'derived_data':
                        $this->get_thumnails_xv();
                        break;
                    case 'remove_data':
                        $this->remove_data('media', 'data', 'duration');
                        break;
                    default:
                        $return = false;
                        break;
                }
                break;
            case 'pornhub':
                switch ($section) {
                    case 'media':
                        switch ($server_id) {
                            case 'pornhub':
                                $this->pornhub();
                                break;
                            default:
                                return;
                                $return = false;
                                break;
                        }
                        break;
                    case 'data':
                        switch ($data) {
                            // restore the original content
                            case 'thumb':
                                $this->new_content = $full_content;
                                break;
                            default:
                                $return = false;
                                break;
                        }
                        break;
                    case 'derived_data':
                        $this->get_thumnails_ph();
                        break;
                    case 'remove_data':
                        $this->remove_data('media', 'data', 'duration', 'sampling_frequency');
                        break;
                    default:
                        $return = false;
                        break;
                }
                break;
            case 'redtube':
                switch ($section) {
                    case 'media':
                        switch ($server_id) {
                            case 'redtube':
                                $this->redtube();
                                break;
                            default:
                                return;
                                $return = false;
                                break;
                        }
                        break;
                    case 'data':
                        switch ($data) {
                            // restore the original content
                            case 'thumb':
                                $this->new_content = $full_content;
                                break;
                            default:
                                $return = false;
                                break;
                        }
                        break;
                    case 'derived_data':
                        $this->get_thumnails_ph();
                        break;
                    case 'remove_data':
                        $this->remove_data('media', 'data', 'duration', 'sampling_frequency');
                        break;
                    default:
                        $return = false;
                        break;
                }
                break;
            default:
                $return = false;
                break;
        }
        return $return;
    }

    public function remove_data(...$args) {
        Utils::remove_multiple_parent_array($this->data, ...$args);
        $this->new_content = $this->data;
    }
}
