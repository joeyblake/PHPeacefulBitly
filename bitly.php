<?php

class Bitly {
  
  /**
   * Bitly API URL
   * @type {String}
   */
  const API_URL = 'https://api-ssl.bit.ly/v3/';

  /**
   * The API OAuth URL
   */
  const API_OAUTH_URL = 'https://api-ssl.bit.ly/v3/';

  /**
   * The OAuth token URL
   */
  const API_OAUTH_TOKEN_URL = 'https://api-ssl.bit.ly/oauth/';

  /**
   * The bitly authorization URL
   */
  const API_AUTHORIZATION_URL = 'https://bit.ly/oauth/authorize';

  /**
   * The API Key
   * 
   * @var string
   */
  private $_apikey;

  /**
   * The OAuth API secret
   * 
   * @var string
   */
  private $_apisecret;

  /**
   * The callback URL
   * 
   * @var string
   */
  private $_callbackurl;

  /**
   * The user access token
   * 
   * @var string
   */
  private $_accesstoken;

  /**
   * Takes an array of bit.ly hashes or shorturls and returns just the hashes
   * @param {array} $data an array of hashes or shorturls
   */
  static function flattenHashArray($data) {
    if (is_array($data)) {
      // we need to flatten this into one proper command
      $recs = array();
      foreach ($data as $rec) {
        $tmp = explode('/', $rec);
        array_push($recs, end($tmp));
      }
      $data = $recs;
    } else {
      $tmp = explode('/', $data);
      $data = end($tmp);
    }
    return $data;
  }

  function __construct($config) {
    if (true === is_array($config)) {
      // if you want to access user data
      $this->setApiKey($config['apiKey']);
      $this->setApiSecret($config['apiSecret']);
      $this->setApiCallback($config['apiCallback']);
    } else if (true === is_string($config)) {
      // if you only want to access public data
      $this->setApiKey($config);
    } else {
      throw new Exception("Error: __construct() - Configuration data is missing.");
    }
  }

  /**
   * Returns the authorization url for the oAuth user
   */
  function getAuthURL() {
    return self::API_AUTHORIZATION_URL . '?' . http_build_query(
      array(
        'client_id' => $this->getApiKey(),
        'redirect_uri' => $this->getApiCallback()
      )
    );
  }
  
  /**
   * Data APIs
   * http://dev.bitly.com/data_apis.html
   */
  
  function highvalue( $limit = 5) {
    $output = $this->_makeCall('highvalue', true, array('limit' => $limit) );
    return $output;
  }

  function search($query, $limit = 10, $offset = 0, $domain = '', $lang = '', $cities = '', $fields = array()) {
    $params = array(
      'query' => $query,
      'limit' => $limit,
      'offset' => $offset
    );
    
    if ($domain != '') {
      $params['domain'] = $domain;
    }
    if ($lang != '') {
      $params['lang'] = $lang;
    }
    if ($cities != '') {
      $params['cities'] = $cities;
    }
    if (!empty($fields)) {
      # only return certain fields
      $params['fields'] = $fields;
    }
    
    $output = $this->_makeCall('search', true, $params );
    return $output;
  }

  function realtime_bursting_phrases() {
    $output = $this->_makeCall('realtime/bursting_phrases', true );
    return $output;
  }

  function realtime_hot_phrases() {
    $output = $this->_makeCall('realtime/hot_phrases', true );
    return $output;
  }

  function realtime_clickrate($phrase) {
    $output = $this->_makeCall('realtime/clickrate', true, array('phrase' => $phrase) );
    return $output;
  }
  
  function link_info($link) {
    $output = $this->_makeCall('link/info', true, array('link' => $link) );
    return $output;
  }

  function link_content($link) {
    $output = $this->_makeCall('link/content', true, array('link' => $link) );
    return $output;
  }

  function link_category($link) {
    $output = $this->_makeCall('link/category', true, array('link' => $link) );
    return $output;
  }

  function link_social($link) {
    $output = $this->_makeCall('link/social', true, array('link' => $link) );
    return $output;
  }

  function link_location($link) {
    $output = $this->_makeCall('link/location', true, array('link' => $link) );
    return $output;
  }

  function link_language($link) {
    $output = $this->_makeCall('link/language', true, array('link' => $link) );
    return $output;
  }
  
  /**
   * Links
   * http://dev.bitly.com/links.html
   */
  
  function expand($data) {
    $data = self::flattenHashArray($data);
    
    $params = array('hash' => $data);
    
    $output = $this->_makeCall('expand', true, $params );
    return $output;
  }
  
  function info($data) {
    $data = self::flattenHashArray($data);
    
    $output = $this->_makeCall('info', true, array('hash' => $data) );
    
    return $output;
  }
  
  function link_lookup($data) {    
    $output = $this->_makeCall('link/lookup', true, array('url' => $data) );
    return $output;
  }
  
  function shorten($longUrl, $domain = '') {
    $params = array('longUrl' => $longUrl);
    
    if ($domain != '') {
      $params['domain'] = $domain;
    }
        
    $output = $this->_makeCall('shorten', true, $params );
    return $output;
  }
  
  function user_link_edit($params) {
        
    $output = $this->_makeCall('user/link_edit', true, $params );
    
    return $output;
  }

  function user_link_lookup($urls = array()) {
    if ( is_array($urls) ) {
      $urls = implode(',', $urls);
    }
    $params = array('url' => $urls);
    
    $output = $this->_makeCall('user/link_lookup', true, $params );
    return $output;
  }
  
  function user_link_save($params) {
    /*
    user/link_save
    array(
      'longUrl' => '',
      'title' => '',
      'note' => '',
      'private' => '',
      'user_ts' => ''
    )
     */
    
    $output = $this->_makeCall('user/link_save', true, $params );
    return $output;
  }
  
  function user_save_custom_domain_keyword($params) {
    /*
    user/save_custom_domain_keyword
    array(
      'keyword_link' => '',
      'target_link' => '',
      'overwrite' => ''
    )
     */
    $output = $this->_makeCall('user/save_custom_domain_keyword', true, $params);
    return $output;
  }
  
  /**
   * Link Metrics
   * http://dev.bitly.com/link_metrics.html
   */
  
  function link_clicks($data) {
    $params = array('link' => $data);
    
    $output = $this->_makeCall('link/clicks', true, $params );
    return $output;
  }
  
  function link_countries($data) {
    $params = array('link' => $data);
    $output = $this->_makeCall('link/countries', true, $params );
    return $output;
  }
  
  function link_encoders($params) {
    /**
     * link/encoders
     * array(
     *   'link' => '',
     *   'my_network' => '',
     *   'subaccounts' => '' ,
     *   'limit' => '',
     *   'expand_user' => ''
     * )
     */
    $output = $this->_makeCall('link/encoders', true, $params );
    return $output;
  }
  
  function link_encoders_by_count($params) {
    /*
    link/encoders_by_count
    array(
      'link' => '',
      'my_network' => '',
      'subaccounts' => '',
      'limit' => '',
      'expand_user' => ''
    )
    */
    $output = $this->_makeCall('link/encoders_by_count', true, $params);
    return $output;
  }
  
  function link_encoders_count($link) {
    /*
    link/encoders_count
    */
    $output = $this->_makeCall('link/encoders_by_count', true, array('link' => $link));
    return $output;
  }
  
  function link_referrers($data) {
    $params = array('link' => $data);
    $output = $this->_makeCall('link/referrers', true, $params );
    return $output;
  }
  
  function link_referrers_by_domain($params) {
    /*
    link/referrers_by_domain
    array(
      'link' => '      '
      'unit' => (minute,hour,day,week,month),
      'units' => '',
      'timezone' => '',
      'limit' => '',
      'unit_reference_ts' => ''
    )
    */
    $output = $this->_makeCall('link/referrers_by_domain', true, $params);
    return $output;
  }

  function link_referring_domains($params) {
    /*
    link/referring_domains
    array(
      'link' => '      '
      'unit' => (minute,hour,day,week,month),
      'units' => '',
      'timezone' => '',
      'limit' => '',
      'unit_reference_ts' => ''
    )
    */
    $output = $this->_makeCall('link/referring_domains', true, $params);
    return $output;
  }
  
  function link_shares($params) {
    /*
    link/shares
    array(
      'link' => '      '
      'unit' => (minute,hour,day,week,month),
      'units' => '',
      'timezone' => '',
      'rollup' => ,
      'limit' => '',
      'unit_reference_ts' => ''
    )
    */
    $output = $this->_makeCall('link/shares', true, $params);
    return $output;
  }
  
  /**
   * User Info/History
   * http://dev.bitly.com/user_info.html
   */
  function oauth_app() {
    /*
    oauth/app
     */
  }
  
  function user_info() {
    /*
    user/info
     */
  }
  
  function user_link_history() {
    $results = array();
    $output = $this->_makeCall('user/link_history', true );
    return $output;
  }
  
  function user_network_history() {
    /*
    user/network_history
     */
  }
  
  function user_tracking_domain_list() {
    /*
    user/tracking_domain_list
     */
  }
  
  /**
   * User Metrics
   * http://dev.bitly.com/user_metrics.html
   */

  function bitly_oauth_access_token($code, $redirect) {
    $results = array();
    $url = bitly_oauth_access_token . "access_token";
    $params = array();
    $params['client_id'] = bitly_clientid;
    $params['client_secret'] = bitly_secret;
    $params['code'] = $code;
    $params['redirect_uri'] = $redirect;
    $output = bitly_post_curl($url, $params);
    $parts = explode('&', $output);
    foreach ($parts as $part) {
      $bits = explode('=', $part);
      $results[$bits[0]] = $bits[1];
    }
    return $results;
  }

  function user_clicks($days = 7) {
    $results = array();
    $output = $this->_makeCall('user/clicks', true, array('days' => $days) );
    return $output;
  }
  
  function user_countries( $days = 7 ) {
    $results = array();
    $output = $this->_makeCall('user/countries', true, array('days' => $days) );
    return $output;
  }
  
  function user_popular_earned_by_clicks() {
    /*
    user/popular_earned_by_clicks
     */
  }
  
  function user_popular_earned_by_shortens() {
    /*
    user/popular_earned_by_shortens
     */
  }
  
  function user_popular_links() {
    /*
    user/popular_links
     */
  }
  
  function user_popular_owned_by_clicks() {
    /*
    user/popular_owned_by_clicks
     */
  }
  
  function user_popular_owned_by_shortens() {
    /*
    user/popular_owned_by_shortens
     */
  }

  function user_referrers($days = 7) {
    $results = array();
    $output = $this->_makeCall('user/referrers', true, array('days' => $days) );
    return $output;
  }
  
  function user_referring_domains() {
    /*
    user/referring_domains
     */
  }

  function user_share_counts() {
    /*
    user/share_counts
     */
  }
  
  function user_share_counts_by_share_type() {
    /*
    user/share_counts_by_share_type
     */
  }
  
  function user_shorten_counts() {
    /*
    user/shorten_counts
     */
  }
  
  /**
   * Organization Metrics
   * http://dev.bitly.com/organization_metrics.html
   */
  function org_brand_messages() {
    /*
    organization/brand_messages
     */
  }
  
  function org_clicks() {
    /*
    organization/clicks
     */
  }
  
  function org_intersecting_links() {
    /*
    organization/intersecting_links
     */
  }
  
  function org_leaderboard() {
    /*
    organization/leaderboard
     */
  }
  
  function org_missed_opportunities() {
    /*
    organization/missed_opportunities
     */
  }
  
  function org_popular_links() {
    /*
    organization/popular_links
     */
  }
  
  function org_shorten_counts() {
    /*
    organization/shorten_counts
     */
  }

  /**
   * Bundles
   * http://dev.bitly.com/bundles.html
   */
  function bundle_archive() {
   /*bundle/archive*/
  }
  
  function bundles_by_user() {
   /*bundle/bundles_by_user*/
  }
  
  function bundle_clone() {
   /*bundle/clone*/
  }
  
  function bundle_collaborator_add() {
   /*bundle/collaborator_add*/
  }
  
  function bundle_collaborator_remove() {
   /*bundle/collaborator_remove*/
  }
  
  function bundle_contents() {
   /*bundle/contents*/
  }
  
  function bundle_create() {
   /*bundle/create*/
  }
  
  function bundle_edit() {
   /*bundle/edit*/
  }
  
  function bundle_link_add() {
   /*bundle/link_add*/
  }
  
  function bundle_link_comment_add() {
   /*bundle/link_comment_add*/
  }
  
  function bundle_link_comment_edit() {
   /*bundle/link_comment_edit*/
  }
  
  function bundle_link_comment_remove() {
   /*bundle/link_comment_remove*/
  }
  
  function bundle_link_edit() {
   /*bundle/link_edit*/
  }
  
  function bundle_link_remove() {
   /*bundle/link_remove*/
  }
  
  function bundle_link_reorder() {
   /*bundle/link_reorder*/
  }
  
  function bundle_pending_collaborator_remove() {
   /*bundle/pending_collaborator_remove*/
  }
  
  function bundle_reorder() {
   /*bundle/reorder*/
  }
  
  function bundle_view_counts() {
   /*bundle/view_count*/
  }
  
  function user_bundle_history() {
   /*user/bundle_history*/
  }
  
  /**
   * Domains
   * http://dev.bitly.com/domains.html
   */
  function bitly_pro_domain($domain) {
    $result = array();
    $output = $this->_makeCall('bitly_pro_domain', true, array('domain' => $domain) );
    return $output;
  }
  
  function user_tracking_domain_clicks() { 
    /*/v3/user/tracking_domain_clicks */
  
  }
  
  function user_tracking_domain_shorten_counts() { 
    /*/v3/user/tracking_domain_shorten_counts */
    
  }

  private function _makeCall($function, $auth = false, $params = null, $method = 'GET') {
    if (false === $auth) {
      // if the call doesn't require authentication
      $authMethod = '?client_id=' . $this->getApiKey();
    } else {
      // if the call needs an authenticated user
      if (true === isset($this->_accesstoken)) {
        $authMethod = '?access_token=' . $this->getAccessToken();
      } else {
        throw new Exception("Error: _makeCall() | $function - This method requires an authenticated users access token.");
      }
    }
    
    if (isset($params) && is_array($params)) {
      $paramString = '&' . http_build_query($params);
    } else {
      $paramString = null;
    }
    
    $apiCall = self::API_URL . $function . $authMethod . (('GET' === $method) ? $paramString : null);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiCall);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    
    if ('POST' === $method) {
      curl_setopt($ch, CURLOPT_POST, count($params));
      curl_setopt($ch, CURLOPT_POSTFIELDS, ltrim($paramString, '&'));
    } else if ('DELETE' === $method) {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $jsonData = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($jsonData);
  }

  /**
   * The OAuth call operator
   *
   * @param array $apiData                The post API data
   * @return mixed
   */
  private function _makeOAuthCall($apiData) {
    $apiHost = self::API_OAUTH_TOKEN_URL;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiHost);
    curl_setopt($ch, CURLOPT_POST, count($apiData));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $jsonData = curl_exec($ch);
    
    if (false === $jsonData) {
      echo 'Curl error: ' . curl_error($ch);
    }
    
    curl_close($ch);
    
    return json_decode($jsonData);
  }
  
  /**
   * Access Token Setter
   * 
   * @param object|string $data
   * @return void
   */
  function setAccessToken($data) {
    (true === is_object($data)) ? $token = $data->access_token : $token = $data;
    $this->_accesstoken = $token;
  }

  /**
   * Access Token Getter
   * 
   * @return string
   */
  function getAccessToken() {
    return $this->_accesstoken;
  }

  /**
   * API-key Setter
   * 
   * @param string $apiKey
   * @return void
   */
  function setApiKey($apiKey) {
    $this->_apikey = $apiKey;
  }

  /**
   * API Key Getter
   * 
   * @return string
   */
  function getApiKey() {
    return $this->_apikey;
  }

  /**
   * API Secret Setter
   * 
   * @param string $apiSecret 
   * @return void
   */
  function setApiSecret($apiSecret) {
    $this->_apisecret = $apiSecret;
  }

  /**
   * API Secret Getter
   * 
   * @return string
   */
  function getApiSecret() {
    return $this->_apisecret;
  }

  /**
   * API Callback URL Setter
   * 
   * @param string $apiCallback
   * @return void
   */
  function setApiCallback($apiCallback) {
    $this->_callbackurl = $apiCallback;
  }

  /**
   * API Callback URL Getter
   * 
   * @return string
   */
  function getApiCallback() {
    return $this->_callbackurl;
  }
}
