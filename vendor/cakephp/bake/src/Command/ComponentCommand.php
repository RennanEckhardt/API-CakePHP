<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.1.0
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Bake\Command;

/**
 * Component code generator.
 */
class ComponentCommand extends SimpleBakeCommand
{
    /**
     * Task name used in path generation.
     *
     * @var string
     */
    public string $pathFragment = 'Controller/Component/';

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'component';
    }

    /**
     * @inheritDoc
     */
    public function fileName(string $name): string
    {
        return $name . 'Component.php';
    }

    /**
     * @inheritDoc
     */
    public function template(): string
    {
        return 'Bake.Controller/component';
    }
}
