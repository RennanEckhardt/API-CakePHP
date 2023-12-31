<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BinaryUuidItemsFixture
 */
class BinaryUuidItemsFixture extends TestFixture
{
    /**
     * records property
     *
     * @var array
     */
    public array $records = [
        ['id' => '481fc6d0-b920-43e0-a40d-6d1740cf8569', 'published' => true, 'name' => 'Item 1'],
        ['id' => '48298a29-81c0-4c26-a7fb-413140cf8569', 'published' => false, 'name' => 'Item 2'],
        ['id' => '482b7756-8da0-419a-b21f-27da40cf8569', 'published' => true, 'name' => 'Item 3'],
    ];
}
