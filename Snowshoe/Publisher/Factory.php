<?php
/**
 *
 * @author Ed van Beinum <e@edvanbeinum.com>
 * @version $Id$
 * @package Factory
 */

namespace Snowshoe\Publisher;
/**
 * Creates an instance of the request Publish class.
 * So far Amazon's S3 is supported
 *
 * @package Factory
 * @author Ed van Beinum <e@edvanbeinum.com>
 */
class Factory
{
    /**
     * @var Snowshoe\Publisher\Adapter
     */
    protected static $_publisher;

    /**
     * @var \Snowshoe\Config\AConfig
     */
    protected $_config;

    /**
     * Construct-ola
     *
     * @param \Snowshoe\Config\AConfig $config
     */
    public function __construct(\Snowshoe\Config\AConfig $config)
    {
        $this->_config = $config;
    }

    /**
     * Simple parameterized factory method that returns an instance of the given Publisher name.
     * Class not found Exceptions are handled by the autoloader
     *
     * @param   string  $publisherName
     * @return  Snowshoe\Publisher\Adapter
     */
    public function getPublisher($publisherName = NULL)
    {
        if (is_null(self::$_publisher)) {
            $newClassName = '\Snowshoe\Publisher\Adapter\\' . ucwords(strtolower($publisherName));
            return new $newClassName($this->_config);
        }
        return self::$_publisher;
    }

    /**
     * Sets the Publisher object. This is only used by the unit tests
     *
     * @static
     * @param $publisher
     * @return void
     */
    public static function setPublisher($publisher)
    {
        self::$_publisher = $publisher;
    }
}