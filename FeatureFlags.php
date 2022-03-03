<?php

/**
 * A class to deal with feature flags. By default all feature flags
 * are enabled. Features that are being actively developed can be
 * disabled by adding them to the list of flags.
 */
class FeatureFlags {

    private $disabled;

    public function __construct() {
        // A list of disabled feature flags
        $this->disabled = [
            'search'
        ];
    }

    /**
     * Check if a feature flag is on. Returns false for disabled flags
     * and true otherwise. Flags are enabled by default.
     */
    public function on($name) {
        // If this flag in in the disabled list, return false
        if (in_array($name, $this->disabled)) {
            return false;
        }
        // Return true because all feature flags are enabled by default
        return true;
    }

}
