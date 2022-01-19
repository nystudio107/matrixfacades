<?php

namespace modules\sitemodule\fields;

interface FacadeInterface
{
    /**
     * See if facades should be globally disabled
     *
     * @return bool
     */
    public function disableFacades(): bool;
}
