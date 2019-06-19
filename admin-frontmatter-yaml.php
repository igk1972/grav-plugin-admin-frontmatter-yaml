<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use Grav\Common\File\CompiledYamlFile;

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
        $blocks = explode(',', $this->config->get('plugins.admin-frontmatter-yaml.blocks', ''));
        $frontmatter = [];
        $header = [];
        $header_all = (array)$e['object']->header();
        foreach ($header_all as $block_name=>$block_data) {
            if (in_array($block_name, $blocks)) {
                $header[$block_name] = $header_all[$block_name];
            } else {
                $frontmatter[$block_name] = $header_all[$block_name];
            }
        }
        $frontmatterFile = $this->getFile($e['object']);
        $frontmatterFile->save($frontmatter);
        $frontmatterFile->free();
        $e['object']->header((object)$header);
    }

    /**
     *
     *
     * @param Event $e
     */
    public function onPageProcessed(Event $e)
    {
        // If there's a `frontmatter.yaml` file merge that in with the page header
        // note page's own frontmatter has precedence and will overwrite any values from page file
        $frontmatterFile = $this->getFile($e['page']);
        if ($frontmatterFile->exists()) {
            $frontmatter_data = (array)$frontmatterFile->content();
            $e['page']->header( (object)array_replace_recursive($frontmatter_data, (array)$e['page']->header()) );
            $frontmatterFile->free();
        }
    }

    /**
     * Get a file.
     *
     * @param Page $page
     * @return CompiledYamlFile
     */
    protected function getFile($page)
    {
        $file = CompiledYamlFile::instance(dirname($page->filePath()) . '/frontmatter.yaml');
        return $file;
    }

}
