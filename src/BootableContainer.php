<?php

namespace Supercluster\Gravity;

use Respect\Config\Container;

/**
 * A container that boots a single of its keys, discarding the non used ones.
 */
class BootableContainer extends Container
{
    /** @var string Name of the single key to be booted */
    protected $frontName = null;

    /**
     * @param  string $bootFile  Relative location to a boot file
     * @return string $frontName Name of the single key to be booted
     */
    public function __construct($bootFile, $frontName = 'application')
    {
        $this->frontName = $frontName;
        $this->loadEnvironment($bootFile);
    }

    /**
     * Boots the chosen key
     *
     * @return mixed Whatever the front application returns
     */
    public function run()
    {
        $front = $this->{$this->frontName};
        $this->exchangeArray(array());
        print $front->run();
    }

    /**
     * Loads a boot configuration file
     *
     * @param  string $file Relative location to a boot file
     */
    public function loadEnvironment($file)
    {
        if (!file_exists($file)) {
            return; // Requested file does not exist.
        }

        $bootFiles = parse_ini_file($file, true);


        foreach ($bootFiles['pre'] as $loaded) {
            $this->loadFile($loaded);
        }

        if (isset($bootFiles['boot'])) {
            foreach ($bootFiles['boot'] as $booted) {
                $bootedFiles = parse_ini_file($booted, true);
                if (!isset($bootedFiles['load'])) {
                    continue;
                }

                foreach ($bootedFiles['load'] as $bootLoad) {
                    $this->loadFile(dirname($booted) . DIRECTORY_SEPARATOR . $bootLoad);
                }
            }
        }

        foreach ($bootFiles['load'] as $loaded) {
            $this->loadFile($loaded);
        }
    }
}
