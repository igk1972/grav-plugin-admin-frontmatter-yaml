<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use Grav\Common\File\CompiledYamlFile;

require_once __DIR__ . '/utils.php';

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
            'onAdminAfterSave' => ['onAdminAfterSave', 0],
        ]);
    }

    /**
     *
     *
     * @param Event $e
     */
    public function onAdminSave(Event $e)
    {
        $blocks = $this->config->get('plugins.admin-frontmatter-yaml.blocks');
        if (!is_array($blocks)) {
            $blocks = explode(',', $blocks);
        }
        $frontmatter = [];
        $header = [];
        $header_all = (array)$e['object']->header();
        $blocks_root_keys = [];
				foreach ($blocks as $block_path) {
          $blocks_root_keys[$block_path] = current(explode('.', $block_path));
        }
        $frontmatter_blocks = [];
        $header_blocks = [];
        foreach ($header_all as $block_name=>$block_data) {
            if (in_array($block_name, $blocks_root_keys)) {
                $header_blocks[$block_name] = $header_all[$block_name];
            } else {
                $frontmatter_blocks[$block_name] = $header_all[$block_name];
            }
        }
        foreach ($blocks_root_keys as $block_path=>$block_name) {
            $key_paths = explode('.', $block_path);
            if (in_array($block_name, array_keys($header_blocks))) {
                $value = get_array_by_key_path($header_blocks, $key_paths);
                $header[$block_name] = isset($header[$block_name]) && is_array($value) ? array_merge_recursive_distinct($header[$block_name], $value) : $value;
            }
        }
        $frontmatter = array_merge_recursive($frontmatter_blocks, array_diff_assoc_recursive($header_blocks, $header));
        $e['object']->header((object)$header);
        $e['object']->addContentMeta('frontmatter', $frontmatter);
    }

    /**
     *
     *
     * @param Event $e
     */
    public function onAdminAfterSave(Event $e)
    {
    	  $frontmatter = $e['object']->getContentMeta('frontmatter');
        $frontmatterFile = $this->getFile($e['object']);
        $frontmatterFile->save($frontmatter);
        $frontmatterFile->free();
        $e['object']->addContentMeta('frontmatter', null);
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
