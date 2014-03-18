<?php

Class NetUtils {

    private static $ch = null;

    // public functions

    public static function init() {
        self::$ch = curl_init();
        if (self::$ch) {
            Log::$logger->trace('curl obj created');
        } else {
            Log::$logger->error('curl obj creation error');
        }
        self::curlBaseConfig();
    }

    private static function curlSSLConfig() {
        curl_setopt(self::$ch, CURLOPT_SSLVERSION, 3);
        curl_setopt(self::$ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(self::$ch, CURLOPT_SSL_VERIFYHOST, 0);
    }

    private static function curlBaseConfig() {
        curl_setopt(self::$ch, CURLOPT_VERBOSE, 1);
        curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt(self::$ch, CURLOPT_TIMEOUT, Settings::CURL_TIMEOUT);
        self::curlSetUserAgent();
        self::curlSSLConfig();

        Log::$logger->trace("curl obj inited by default");
    }

    public static function curlSetUserAgent($useragent='opera') {
        if ($useragent == 'mozilla') {
            $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ru; rv:1.9.2.13) ' .
                'Gecko/20101203 Firefox/3.6.13 ( .NET CLR 3.5.30729)';
        } else {
            $user_agent = "Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.14912/870; U; id) Presto/2.4.15";
        }
        curl_setopt(self::$ch, CURLOPT_USERAGENT, $user_agent);
    }

    private static function curlExecQuery($debug = false) {

        $answer = '';
        Log::$logger->debug("Sending curl query. URL " .
                curl_getinfo(self::$ch, CURLINFO_EFFECTIVE_URL));
        if ($debug) {
            return '';
        }

        $answer = curl_exec(self::$ch);
        if (curl_errno(self::$ch)) {
            Log::$logger->error('curl error code: ' . curl_errno(self::$ch) . ' ' . curl_error(self::$ch));

            if (curl_errno(self::$ch) == 35) {
                Log::$logger->error("This code number means that problems are on the server side.
                        Restart the script later.");
            }

            return false;
        }
        /*
          if (!$answer) {
          log::$logger->error("Empty answer received on curl query, url = \n" .
          curl_getinfo(self::$ch, CURLINFO_EFFECTIVE_URL).'\n\n');
          }
         */
        //log::$logger->info('URL after ' . curl_getinfo(self::$ch, CURLINFO_EFFECTIVE_URL));

        return $answer;
    }

    public static function goToPage($page) {
        Log::$logger->debug("(Curl) Going to link $page");
        curl_setopt(self::$ch, CURLOPT_URL, $page);
        curl_setopt(self::$ch, CURLOPT_POST, false);
        //curl_setopt(self::$ch, CURLOPT_HTTPGET, 1);
        $go = self::curlExecQuery();
        $status = curl_getinfo(self::$ch, CURLINFO_HTTP_CODE);

        return $go;
    }

    public static function endWorkCurl() {
        curl_close(self::$ch);
    }

    public static function goToPageAndSaveAnsw($page, $file_path) {

        $go = self::goToPage($page);
        self::storeDataToFile($go, $file_path);

        return $go;
    }

    public static function storeDataToFile($data, $file_path) {
        if ($file_path) {

            Log::$logger->trace("Storing data to file " . $file_path);
            $dump_to_file = file_put_contents($file_path, $data);

            if (!$dump_to_file) {
                Log::$logger->error("Error storing data to file " . $file_path);
            }
        }
    }

}

?>
