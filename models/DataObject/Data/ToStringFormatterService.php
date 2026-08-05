<?php
declare(strict_types=1);

/**
 * OpenDXP
 *
 * This source file is licensed under the GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (https://pimcore.com)
 * @copyright  Modification Copyright (c) OpenDXP (https://www.opendxp.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0.html  GNU General Public License version 3 (GPLv3)
 */

namespace OpenDxp\Model\DataObject\Data;

use Stringable;
use Twig\Environment;

class ToStringFormatterService
{
    /**
     * Maps fully-qualified data object classes to Twig templates used for string rendering.
     *
     * @var array<class-string, string>
     */
    private array $templates = [];

    /**
     * Create a formatter that can render configured Twig templates for stringable data objects.
     * When no template is configured, the existing renderToString() fallback is used.
     */
    public function __construct(private readonly ?Environment $twig = null, ?array $templates = null)
    {
        if (is_array($templates)) {
            $this->templates = $templates;
        }
    }

    /**
     * Render a data object to a string. If a template is configured for the object's class,
     * the Twig template is used; otherwise the class-specific renderToString() logic is used.
     */
    public function render(object $object): string
    {
        if (!$object instanceof Stringable) {
            return (string) $object;
        }

        $class = $object::class;
        if (isset($this->templates[$class])) {
            $template = $this->templates[$class];
            if ($template === 'legacy') {
                return $this->renderLegacy($object);
            }

            return $this->renderTemplate($template, $object);
        }

        return $this->renderLegacy($object);
    }

    /**
     * Fall back to the legacy implementation provided by the object itself.
     */
    private function renderLegacy(Stringable $object): string
    {
        if (method_exists($object, 'renderToString')) {
            return $object->renderToString();
        }

        return (string) $object;
    }

    /**
     * Render a configured Twig template for the given object.
     * The object is provided as both `data` and `object` so the template can use either name.
     */
    private function renderTemplate(string $template, object $object): string
    {
        if (!$this->twig instanceof Environment) {
            return $this->renderLegacy($object);
        }

        return $this->twig->render($template, ['data' => $object, 'object' => $object]);
    }
}
