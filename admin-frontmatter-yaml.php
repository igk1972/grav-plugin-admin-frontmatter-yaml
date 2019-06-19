<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class AdminFrontmatterYamlPlugin
 * @package Grav\Plugin
 */
class AdminFrontmatterYamlPlugin extends Plugin
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        // if ($this->isAdmin()) {
        //     return;
        // }

        // Enable the main event we are interested in
        $this->enable([
            'onPageProcessed' => ['onPageProcessed', 0],
            'onAdminSave' => ['onAdminSave', 0],
        ]);
    }

    /**
     *
     *
     * @param Event $e
     */
    public function onAdminSave(Event $e)
    {
    }

    /**
     *
     *
     * @param Event $e
     */
    public function onPageProcessed(Event $e)
    {
    }

}
