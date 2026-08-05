<?php
declare(strict_types=1);

/**
 * OpenDXP
 *
 * This source file is licensed under the GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available
 * in LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (https://pimcore.com)
 * @copyright  Modification Copyright (c) OpenDXP (https://www.opendxp.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0.html  GNU General Public License version 3 (GPLv3)
 */

namespace OpenDxp\Model\DataObject\Traits;

use OpenDxp;
use OpenDxp\Model\DataObject\Data\ToStringFormatterService;

trait ToStringRenderTrait
{
    /**
     * Guards against recursive rendering when __toString() calls back into the formatter.
     */
    protected bool $toStringRendering = false;

    /**
     * Route string conversion through the shared formatter service so all data objects can
     * benefit from the same templated rendering path while preserving the legacy fallback.
     */
    public function __toString(): string
    {
        if ($this->toStringRendering) {
            return $this->renderToString();
        }

        $this->toStringRendering = true;

        try {
            $container = OpenDxp::getContainer();
            if ($container && $container->has(ToStringFormatterService::class)) {
                return $container->get(ToStringFormatterService::class)->render($this);
            }

            return $this->renderToString();
        } finally {
            $this->toStringRendering = false;
        }
    }

    protected function renderToString(): string
    {
        return '';
    }
}
