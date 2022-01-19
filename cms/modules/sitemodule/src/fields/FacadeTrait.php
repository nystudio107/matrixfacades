<?php

namespace modules\sitemodule\fields;

use craft\helpers\App;

trait FacadeTrait
{
    /**
     * @inerhitDoc
     */
    public function disableFacades(): bool
    {
        $result = false;
        // If the `DISABLE_FACADES` environment variable exists,
        // use it to determine whether facades are enabled
        $envVal = App::env('DISABLE_FACADES');
        if ($envVal !== false) {
            $result = (bool)$envVal;
        }

        return $result;
    }
}
