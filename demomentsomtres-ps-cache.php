<?php

/**
 * Classe que implementa la cache de continguts de prestashop webservices
 * @since 1.0
 */
require_once 'lib/PSWebServiceLibrary.php';

class demomentsomtres_ps_cache {

    private $cachetime = 3600;
    private $cachedir = './wp-content/plugins/demomentsomtres-prestashop/ps-cache/';
    private $cacheext = '.html';
    private $cachepsurl = '';
    private $cachepskey = '';
    private $cachedebug = false;

    public function setCacheTime($new) {
        $this->cachetime = $new;
    }

    public function setCacheDir($new) {
        $this->cachedir = $new;
    }

    public function setCacheExt($new) {
        $this->cacheext = $new;
    }

    public function setCacheDebug($new) {
        $this->cachedebug = $new;
    }

    public function setPrestaShopURL($url) {
        $this->cachepsurl = $url;
    }

    public function setPrestaShopKey($key) {
        $this->cachepskey = $key;
    }

    public function showCacheInfo() {
        print_r($this);
    }

    public function getPrestaShopResource($type, $id='') {
        if (!file_exists($this->cachedir)):
            mkdir($this->cachedir);
        endif;
        $time = time();
        $fitxer = md5($type . $id) . $this->cacheext;
        $nomcomplet = $this->cachedir . $fitxer;
        $updatetime = (file_exists($nomcomplet)) ? filemtime($nomcomplet) : 0;
        if ($updatetime + $this->cachetime < $time):
            try {
                $webService = new PrestaShopWebservice(
                                $this->cachepsurl,
                                $this->cachepskey,
                                $this->cachedebug
                );
                $xml = $webService->get(array(
                    'resource' => $type,
                    'id' => $id,
                        ));
                $contingut = $xml->asXML();
            } catch (PrestaShopWebserviceException $ex) {
                $trace = $ex->getTrace(); // Retrieve all information on the error
                $errorCode = $trace[0]['args'][0]; // Retrieve the error code 
                if ($errorCode == 401):
                    $contingut = 'Bad auth key';
                else:
                    $contingut = $ex->getMessage();
                endif;
            }
            file_put_contents($nomcomplet, $contingut);
        endif;
        return simplexml_load_file($nomcomplet);
    }
}
?>
