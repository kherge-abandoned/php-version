<?php

namespace Herrera\Version;

use Herrera\Version\Exception\InvalidStringRepresentationException;

/**
 * Parses the string representation of a version number.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Parser
{
    /**
     * The build metadata component.
     */
    const BUILD = 'build';

    /**
     * The major version number component.
     */
    const MAJOR = 'major';

    /**
     * The minor version number component.
     */
    const MINOR = 'minor';

    /**
     * The patch version number component.
     */
    const PATCH = 'patch';

    /**
     * The pre-release version number component.
     */
    const PRE_RELEASE = 'pre-release';

    /**
     * Returns a Version builder for the string representation.
     *
     * @param string $version The string representation.
     *
     * @return Builder A Version builder.
     */
    public static function toBuilder($version)
    {
        return Builder::create()->importComponents(
            self::toComponents($version)
        );
    }

    /**
     * Returns the components of the string representation.
     *
     * @param string $version The string representation.
     *
     * @return array The components of the version.
     *
     * @throws InvalidStringRepresentationException If the string representation
     *                                              is invalid.
     */
    public static function toComponents($version)
    {
        if (!Validator::isVersion($version)) {
            throw new InvalidStringRepresentationException($version);
        }

        if (false !== strpos($version, '+')) {
            list($version, $build) = explode('+', $version);

            $build = explode('.', $build);
        }

        if (false !== strpos($version, '-')) {
            list($version, $pre) = preg_split('/-/', $version, 2);

            $pre = explode('.', $pre);
        }

        list(
            $major,
            $minor,
            $patch
        ) = explode('.', $version);

        return array(
            self::MAJOR => intval($major),
            self::MINOR => intval($minor),
            self::PATCH => intval($patch),
            self::PRE_RELEASE => isset($pre) ? $pre : array(),
            self::BUILD => isset($build) ? $build : array(),
        );
    }

    /**
     * Returns a Version instance for the string representation.
     *
     * @param string $version The string representation.
     *
     * @return Version A Version instance.
     */
    public static function toVersion($version)
    {
        $components = self::toComponents($version);

        return new Version(
            $components['major'],
            $components['minor'],
            $components['patch'],
            $components['pre-release'],
            $components['build']
        );
    }
}
