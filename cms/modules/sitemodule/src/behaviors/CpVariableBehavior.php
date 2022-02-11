<?php
/**
 * Site module for Craft CMS 3.x
 *
 * Custom site module
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace modules\sitemodule\behaviors;

use Craft;
use craft\helpers\ArrayHelper;

use yii\base\Behavior;

class CpVariableBehavior extends Behavior
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the available CP template path suggestions for template inputs.
     *
     * @return string[]
     */
    public function getCpTemplateSuggestions(): array
    {
        $suggestions = [];
        $roots = Craft::$app->getView()->getCpTemplateRoots();
        foreach($roots as $handle => $paths) {
            foreach ($paths as $path) {
                $suggestions = array_merge($suggestions, $this->getTemplateSuggestions($handle, $path));
            }
        }

        return [
            [
                'label' => Craft::t('app', 'Templates'),
                'data' => $suggestions,
            ],
        ];
    }

    /**
     * Returns the available template path suggestions for passed in $root directory.
     *
     * @return string[]
     */
    private function getTemplateSuggestions(string $handle, string $root): array
    {
        if (!is_dir($root)) {
            return [];
        }

        $directory = new \RecursiveDirectoryIterator($root);

        $filter = new \RecursiveCallbackFilterIterator($directory, function($current) {
            // Skip hidden files and directories, as well as node_modules/ folders
            if ($current->getFilename()[0] === '.' || $current->getFilename() === 'node_modules') {
                return false;
            }
            return true;
        });

        $iterator = new \RecursiveIteratorIterator($filter);
        /** @var \SplFileInfo[] $files */
        $files = [];
        $pathLengths = [];

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if (!$file->isDir() && $file->getFilename()[0] !== '.') {
                $files[] = $file;
                $pathLengths[] = strlen($file->getRealPath());
            }
        }

        array_multisort($pathLengths, SORT_NUMERIC, $files);

        // Now build the suggestions array
        $suggestions = [];
        $templates = [];
        $config = Craft::$app->getConfig()->getGeneral();
        $rootLength = strlen($root);

        foreach ($files as $file) {
            $template = substr($file->getRealPath(), $rootLength + 1);

            // Can we chop off the extension?
            $extension = $file->getExtension();
            if (in_array($extension, $config->defaultTemplateExtensions, true)) {
                $template = substr($template, 0, strlen($template) - (strlen($extension) + 1));
            }
            $template = $handle . '/' . $template;

            $hint = $handle;

            // Avoid listing the same template path twice (considering localized templates)
            if (isset($templates[$template])) {
                continue;
            }

            $templates[$template] = true;
            $suggestions[] = [
                'name' => $template,
                'hint' => $hint,
            ];
        }

        ArrayHelper::multisort($suggestions, 'name');

        return $suggestions;
    }
}
