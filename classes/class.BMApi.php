<?php
// getConfigItem requirements
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';

class BMApi {
    const BMAPI_BASEURL = 'https://api.brandmeister.network/v2/';
    const BMAPI_KEY_FILE = '/etc/bmapi.key';
    const BMAPI_CONFIG = '/etc/wpsd-bm-config.json';
    const BM_GROUPS_JSON = '/usr/local/etc/BM_TGs.json';
    const DEBUG_LOG = '/tmp/bm-debug.log';
    // to enable debug logging, touch this file & chown www-data:www-data

    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new BMApi();
        return self::$instance;
    }

    private function __construct() {
        $this->loadServicesConfig();

        if ($this->bmStatus == self::STATUS_OK) {
            $this->loadConfig();
            $this->loadKey();
        }
    }

    private $key = null;
    private $config = [
        //default config
        'favTGs' => [],
    ];

    public $dmrID = null;
    public $dmrNetName = '';

    const STATUS_OK = 0;
    const STATUS_NOKEY = 1;
    const STATUS_LEGACYKEY = 2;
    const STATUS_BADCONFIG = 3;
    const STATUS_BMDISABLED = 4;

    private $bmStatus = self::STATUS_BADCONFIG;
    private $keyStatus = self::STATUS_NOKEY;

    public function getStatus() {
        if ($this->bmStatus != self::STATUS_OK) return $this->bmStatus;
        return $this->keyStatus;
    }

    private function loadKey() {
        if (!file_exists(self::BMAPI_KEY_FILE)) {
            $this->keyStatus = self::STATUS_NOKEY;
            return;
        }

        $confBMapi = parse_ini_file(self::BMAPI_KEY_FILE, true);
        if (!isset($confBMapi['key']['apikey']) || (strlen($confBMapi['key']['apikey']) <= 20)) {
            $this->keyStatus = self::STATUS_LEGACYKEY;
            return;
        }

        $this->key = $confBMapi['key']['apikey'];
        $this->keyStatus = self::STATUS_OK;
    }

    private function loadServicesConfig() {
        $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
        if ($dmrMasterHost != '127.0.0.1') {
            $this->bmStatus = self::STATUS_BADCONFIG;  // DMRGateway only required, legacy isn't supported
            return $this->bmStatus;
        }

        if (!isset($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Id'])) {
            $this->bmStatus = self::STATUS_BADCONFIG;
            return $this->bmStatus;
        }

        $dmrMasterHost = $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'];
        $bmEnabled = ($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Enabled'] != "0" ? true : false);
        if (!$bmEnabled) {
            $this->bmStatus = self::STATUS_BMDISABLED;
            return $this->bmStatus;
        }

        $this->dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Id'];
        $this->dmrNetName = str_replace('_', ' ', $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Name']);
        $this->bmStatus = self::STATUS_OK;

        return $this->bmStatus;
    }


    //=========== Debug log
    protected function debugLog($message) {
        if (!file_exists(self::DEBUG_LOG)) return;
        file_put_contents(self::DEBUG_LOG, date('Y-m-d H:i:s') . ' ' . $message . "\n", FILE_APPEND);
    }


    //=========== Group names resolver
    private $cachedGroups = null;
    public function resolveGroupName($tg) {
        if ($this->cachedGroups === null)
            $this->cachedGroups = json_decode(file_get_contents(self::BM_GROUPS_JSON), TRUE);
        return $this->cachedGroups[$tg] ?? "";
    }


    //=========== Favorite TGs methods:
    private $cachedProfile = null;


    // gets bm profile and updates fav tg status
    public function getFavTGs() {
        if ($this->cachedProfile === null)
            $this->cachedProfile = $this->getProfile();

        $favTGs = $this->config['favTGs'];

        foreach ($favTGs as $tg => $tgdata)
            $favTGs[$tg]['linked'] = false;

        foreach ($this->cachedProfile['staticSubscriptions'] as $ss) {
            $favTGs[(int)$ss['talkgroup']] = ['slot' => (int)$ss['slot'], 'linked' => true];
            // save in config as well, if missing in favs (linked through BM)
            $this->config['favTGs'][(int)$ss['talkgroup']] = ['slot' => (int)$ss['slot']];
        }

        ksort($favTGs, SORT_NUMERIC);

        return $favTGs;
    }

    public function getDynamicTGs() {
        if ($this->cachedProfile === null)
            $this->cachedProfile = $this->getProfile();

        return $this->cachedProfile['dynamicSubscriptions'];
    }

    // add
    public function addFavTG($tg, $slot) {
        $this->linkStaticTG($tg, $slot);
        $this->config['favTGs'][(int)$tg] = ['slot' => $slot];
    }

    // delete
    public function delFavTG($tg, $slot) {
        $this->dropStaticTG($tg, $slot);
        unset($this->config['favTGs'][(int)$tg]);
    }

    public function loadConfig() {
        if (!file_exists(self::BMAPI_CONFIG)) return;
        $this->config = json_decode(file_get_contents(self::BMAPI_CONFIG), true);
    }

    public function saveConfig() {
        $tempConfigFilename = tempnam('/tmp', 'wpsd-bm-config');
        if (!$tempConfigFilename) return false;

        file_put_contents($tempConfigFilename, json_encode($this->config));

        //move to system config and set permissions
        exec(sprintf('sudo cp %s %s', $tempConfigFilename, self::BMAPI_CONFIG));
        exec(sprintf('sudo chmod 644 %s', self::BMAPI_CONFIG));
        exec(sprintf('sudo chown root:root %s', self::BMAPI_CONFIG));
    }


    //========== Mass Actions:
    public function linkAllStatic() {
        foreach ($this->getFavTGs() as $tg => $favTGData)
            if ($favTGData['linked'] == false)
                $this->linkStaticTG($tg, $favTGData['slot']);
    }

    public function dropAllStatic($forever = false) {
        foreach ($this->getFavTGs() as $tg => $favTGData)
            if ($favTGData['linked'] == true)
                $this->dropStaticTG($tg, $favTGData['slot']);

        if ($forever) {
            $this->config['favTGs'] = [];
            $this->saveConfig();
        }
    }


    //=========== BM API Methods
    private function apiCall($endpoint, $method = 'GET', $useKey = false, $postData = null) {
        $httpContext = [
            'method'  => $method,
            'header'  => [
                'User-Agent: WPSD Dashboard for ' . $this->dmrID,
            ],
            'password' => '',
            'timeout' => 10,
        ];

        if ($useKey) {
            $httpContext['header'] = array_merge($httpContext['header'], [
                'Authorization: Bearer ' . $this->key,
            ]);
        }

        if ($method == 'POST' && $postData !== null) {
            $postDataJson = json_encode($postData);
            $httpContext['header'] = array_merge($httpContext['header'], [
                'Content-Type: accept: application/json',
                'Content-Length: ' . strlen($postDataJson),
            ]);
            $httpContext['content'] = $postDataJson;
        }

        $streamContext = stream_context_create(['http' => $httpContext]);

        $apiResult = @file_get_contents(self::BMAPI_BASEURL . $endpoint, /*use_include_path=*/false, $streamContext);

        $this->debugLog("BM API call:\n" . var_export([
            'method'      => $method,
            'endpoint'    => self::BMAPI_BASEURL . $endpoint,
            'httpContext' => $httpContext,
            'result'      => $apiResult,
        ], TRUE));

        return json_decode($apiResult, /*associative=*/true);
    }

    // get BM profile
    public function getProfile() {
        return $this->apiCall("device/{$this->dmrID}/profile");
    }

    // link static TG to BM
    public function linkStaticTG($tg, $slot) {
        return $this->apiCall("device/{$this->dmrID}/talkgroup", 'POST', true, ['group' => $tg, 'slot' => $slot]);
    }

    // unlink static TG from BM
    public function dropStaticTG($tg, $slot) {
        return $this->apiCall("device/{$this->dmrID}/talkgroup/{$slot}/{$tg}", 'DELETE', true);
    }

    public function dropDynamicTGs($slot) {
        return $this->apiCall("device/{$this->dmrID}/action/dropDynamicGroups/{$slot}", 'GET', true);
    }

    public function dropQSO($slot) {
        return $this->apiCall("device/{$this->dmrID}/action/dropCallRoute/{$slot}", 'GET', true);
    }
}