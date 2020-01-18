<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiExtension.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 5, '20
 */

namespace FabianFroehlich\Core\Api\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use function dirname;


/**
 * BundleTemplateExtension.
 *
 * @author Fabian Fröhlich <mail@f-froehlich.de>
 */
class ApiExtension
    extends Extension {

    /**
     * Responds to the app.config configuration parameter.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container) {

        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));

        $loader->load('service.xml');
    }
}
