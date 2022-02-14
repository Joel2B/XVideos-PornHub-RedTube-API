<?php

class _msg {
    public static $msg_counter = 0;

    public static function msg($title = '', $msg = '', $encode = true) {
        if (!defined('DEBUG')) {
            return;
        }

        $num_args = func_num_args();
        if ($num_args === 1) {
            self::$msg_counter++;

            echo '<b>Msg: ' . self::$msg_counter . '</b><br>';

            $msg = func_get_arg(0);
        } else {
            if (!empty($title)) {
                echo "<b>$title</b><br>";
            }
        }

        if (is_array($msg) || is_object($msg)) {
            echo '<pre>';
            var_dump($msg);
            echo '</pre>';
        } else {
            if ($encode) {
                echo htmlspecialchars($msg);
            } else {
                echo $msg;
            }
        }

        echo '<hr>';
    }
}
