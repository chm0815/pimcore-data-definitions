<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Cleaner;

use function count;
use Instride\Bundle\DataDefinitionsBundle\Model\DataDefinitionInterface;
use Pimcore\Model\Dependency;

class ReferenceCleaner extends AbstractCleaner
{
    public function cleanup(DataDefinitionInterface $definition, array $objectIds): void
    {
        $notFoundObjects = $this->getObjectsToClean($definition, $objectIds);

        foreach ($notFoundObjects as $obj) {
            $dependency = $obj->getDependencies();

            if ($dependency instanceof Dependency) {
                if (count($dependency->getRequiredBy()) === 0) {
                    $obj->delete();
                } else {
                    $obj->setPublished(false);
                    $obj->save();
                }
            }
        }
    }
}
