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

namespace OpenDxp\Tests\Unit\Model\DataObject\Traits;

use OpenDxp;
use OpenDxp\Model\DataObject\Data\ToStringFormatterService;
use OpenDxp\Model\DataObject\Traits\ToStringRenderTrait;
use OpenDxp\Tests\Support\Test\TestCase;

/**
 * @internal
 */
final class ToStringRenderTraitTest extends TestCase
{
    public function testStringConversionUsesFormatterService(): void
    {
        $formatter = new class extends ToStringFormatterService {
            public function render(object $object): string
            {
                return 'from-service';
            }
        };

        OpenDxp::getContainer()->set(ToStringFormatterService::class, $formatter);

        $value = new class {
            use ToStringRenderTrait;

            protected function renderToString(): string
            {
                return 'fallback';
            }
        };

        $this->assertSame('from-service', (string) $value);
    }
}
