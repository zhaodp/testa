<?php

namespace Elastica\Index;

use Elastica\Index as BaseIndex;
use Elastica\Request;

/**
 * Elastica index settings object
 *
 * All settings listed in the update settings API (http://www.elasticsearch.org/guide/reference/api/admin-indices-update-settings.html)
 * can be changed on a running indices. To make changes like the merge policy (http://www.elasticsearch.org/guide/reference/index-modules/merge.html)
 * the index has to be closed first and reopened after the call
 *
 * @category Xodoa
 * @package Elastica
 * @author Nicolas Ruflin <spam@ruflin.com>
 * @link http://www.elasticsearch.org/guide/reference/api/admin-indices-update-settings.html
 * @link http://www.elasticsearch.org/guide/reference/index-modules/merge.html
 */
class Settings
{
    /**
     * Response
     *
     * @var \Elastica\Response Response object
     */
    protected $_response = null;

    /**
     * Stats info
     *
     * @var array Stats info
     */
    protected $_data = array();

    /**
     * Index
     *
     * @var \Elastica\Index Index object
     */
    protected $_index = null;

    const DEFAULT_REFRESH_INTERVAL = '1s';

    /**
     * Construct
     *
     * @param \Elastica\Index $index Index object
     */
    public function __construct(BaseIndex $index)
    {
        $this->_index = $index;
    }

    /**
     * Returns the current settings of the index
     *
     * If param is set, only specified setting is return.
     * 'index.' is added in front of $setting.
     *
     * @param  string            $setting OPTIONAL Setting name to return
     * @return array|string|null Settings data
     * @link http://www.elasticsearch.org/guide/reference/api/admin-indices-update-settings.html
     */
    public function get($setting = '')
    {
        $requestData = $this->request()->getData();
        $data = reset($requestData);

        $settings = $data['settings']['index'];

        if (!empty($setting)) {
            if (isset($settings[$setting])) {
                return $settings[$setting];
            } else {
                if (strpos($setting, '.') !== false) {
                    // translate old dot-notation settings to nested arrays
                    $keys = explode('.', $setting);
                    foreach ($keys as $key) {
                        if (isset($settings[$key])) {
                            $settings = $settings[$key];
                        } else {
                            return;
                        }
                    }

                    return $settings;
                }

                return;
            }
        }

        return $settings;
    }

    /**
     * Sets the number of replicas
     *
     * @param  int                $replicas Number of replicas
     * @return \Elastica\Response Response object
     */
    public function setNumberOfReplicas($replicas)
    {
        $replicas = (int) $replicas;

        $data = array('number_of_replicas' => $replicas);

        return $this->set($data);
    }

    /**
     * Sets the index to read only
     *
     * @param  bool               $readOnly (default = true)
     * @return \Elastica\Response
     */
    public function setReadOnly($readOnly = true)
    {
        return $this->set(array('blocks.read_only' => $readOnly));
    }

    /**
     * @return bool
     */
    public function getBlocksRead()
    {
        return (bool) $this->get('blocks.read');
    }

    /**
     * @param  bool               $state OPTIONAL (default = true)
     * @return \Elastica\Response
     */
    public function setBlocksRead($state = true)
    {
        $state = $state ? 1 : 0;

        return $this->set(array('blocks.read' => $state));
    }

    /**
     * @return bool
     */
    public function getBlocksWrite()
    {
        return (bool) $this->get('blocks.write');
    }

    /**
     * @param  bool               $state OPTIONAL (default = true)
     * @return \Elastica\Response
     */
    public function setBlocksWrite($state = true)
    {
        $state = $state ? 1 : 0;

        return $this->set(array('blocks.write' => (int) $state));
    }

    /**
     * @return bool
     */
    public function getBlocksMetadata()
    {
        return (bool) $this->get('blocks.metadata');
    }

    /**
     * @param  bool               $state OPTIONAL (default = true)
     * @return \Elastica\Response
     */
    public function setBlocksMetadata($state = true)
    {
        $state = $state ? 1 : 0;

        return $this->set(array('blocks.metadata' => (int) $state));
    }

    /**
     * Sets the index refresh interval
     *
     * Value can be for example 3s for 3 seconds or
     * 5m for 5 minutes. -1 refreshing is disabled.
     *
     * @param  int                $interval Number of milliseconds
     * @return \Elastica\Response Response object
     */
    public function setRefreshInterval($interval)
    {
        return $this->set(array('refresh_interval' => $interval));
    }

    /**
     * Returns the refresh interval
     *
     * If no interval is set, the default interval is returned
     *
     * @return string Refresh interval
     */
    public function getRefreshInterval()
    {
        $interval = $this->get('refresh_interval');

        if (empty($interval)) {
            $interval = self::DEFAULT_REFRESH_INTERVAL;
        }

        return $interval;
    }

    /**
     * Return merge policy
     *
     * @return string Merge policy type
     */
    public function getMergePolicyType()
    {
        return $this->get('merge.policy.type');
    }

    /**
     * Sets merge policy
     *
     * @param  string             $type Merge policy type
     * @return \Elastica\Response Response object
     * @link http://www.elasticsearch.org/guide/reference/index-modules/merge.html
     */
    public function setMergePolicyType($type)
    {
        $this->getIndex()->close();
        $response = $this->set(array('merge.policy.type' => $type));
        $this->getIndex()->open();

        return $response;
    }

    /**
     * Sets the specific merge policies
     *
     * To have this changes made the index has to be closed and reopened
     *
     * @param  string             $key   Merge policy key (for ex. expunge_deletes_allowed)
     * @param  string             $value
     * @return \Elastica\Response
     * @link http://www.elasticsearch.org/guide/reference/index-modules/merge.html
     */
    public function setMergePolicy($key, $value)
    {
        $this->getIndex()->close();
        $response = $this->set(array('merge.policy.'.$key => $value));
        $this->getIndex()->open();

        return $response;
    }

    /**
     * Returns the specific merge policy value
     *
     * @param  string $key Merge policy key (for ex. expunge_deletes_allowed)
     * @return string Refresh interval
     * @link http://www.elasticsearch.org/guide/reference/index-modules/merge.html
     */
    public function getMergePolicy($key)
    {
        $settings = $this->get();
        if (isset($settings['merge']['policy'][$key])) {
            return $settings['merge']['policy'][$key];
        }

        return;
    }

    /**
     * Can be used to set/update settings
     *
     * @param  array              $data Arguments
     * @return \Elastica\Response Response object
     */
    public function set(array $data)
    {
        return $this->request($data, Request::PUT);
    }

    /**
     * Returns the index object
     *
     * @return \Elastica\Index Index object
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * Updates the given settings for the index
     *
     * With elasticsearch 0.16 the following settings are supported
     * - index.term_index_interval
     * - index.term_index_divisor
     * - index.translog.flush_threshold_ops
     * - index.translog.flush_threshold_size
     * - index.translog.flush_threshold_period
     * - index.refresh_interval
     * - index.merge.policy
     * - index.auto_expand_replicas
     *
     * @param  array              $data   OPTIONAL Data array
     * @param  string             $method OPTIONAL Transfer method (default = \Elastica\Request::GET)
     * @return \Elastica\Response Response object
     */
    public function request(array $data = array(), $method = Request::GET)
    {
        $path = '_settings';

        $data = array('index' => $data);

        return $this->getIndex()->request($path, $method, $data);
    }
}
