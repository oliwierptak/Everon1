<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Widget;

use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;;
use Everon\Interfaces\Collection;
use Everon\View;

class Manager implements View\Interfaces\WidgetManager
{
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Factory;
    use View\Dependency\ViewManager;

    use Helper\Arrays;
    use Helper\IsIterable;
    use Helper\String\LastTokenToName;
    use Helper\String\EndsWith;

    /**
     * @var Collection
     */
    protected $WidgetCollection = null;


    /**
     * @param View\Interfaces\Manager $Manager
     */
    public function __construct(View\Interfaces\Manager $Manager)
    {
        $this->ViewManager = $Manager;
        $this->WidgetCollection = new Helper\Collection([]);
    }

    /**
     * @inheritdoc
     */
    public function prepareView($name, $namespace='Everon\View')
    {
        $template_directory = implode(DIRECTORY_SEPARATOR, [
            $this->getViewManager()->getViewDirectory().$this->getViewManager()->getCurrentThemeName(), 'Widget', $name, 'templates'
        ]);
        $View = $this->getViewManager()->createView('Base', $template_directory.DIRECTORY_SEPARATOR, 'Everon\View');
        return $View;
    }

    /**
     * @inheritdoc
     */
    public function createWidget($name, $namespace='Everon\View')
    {
        $View = $this->prepareView($name);
        $namespace .= '\\'.$this->getViewManager()->getCurrentThemeName().'\Widget';
        $Widget = $this->getFactory()->buildViewWidget($name, $View, $namespace);
        return $Widget;
    }

    /**
     * @inheritdoc
     */
    public function includeWidget($name)
    {
        if ($this->WidgetCollection->has($name) === false) {
            $Widget = $this->createWidget($name);
            $this->WidgetCollection->set($name, $Widget);
        }

        return $this->WidgetCollection->get($name)
            ->render();
    }
}