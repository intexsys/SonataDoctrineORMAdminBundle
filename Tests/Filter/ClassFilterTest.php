<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DoctrineORMAdminBundle\Tests\Filter;

use Sonata\CoreBundle\Form\Type\EqualType;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\DoctrineORMAdminBundle\Filter\ClassFilter;
use Sonata\DoctrineORMAdminBundle\Tests\Helpers\PHPUnit_Framework_TestCase;

class ClassFilterTest extends PHPUnit_Framework_TestCase
{
    public function testFilterEmpty()
    {
        $filter = new ClassFilter();
        $filter->initialize('field_name', ['field_options' => ['class' => 'FooBar']]);

        $builder = new ProxyQuery(new QueryBuilder());

        $filter->filter($builder, 'alias', 'field', null);
        $filter->filter($builder, 'alias', 'field', 'asds');

        $this->assertEquals([], $builder->query);
        $this->assertEquals(false, $filter->isActive());
    }

    public function testFilterInvalidOperator()
    {
        $filter = new ClassFilter();
        $filter->initialize('field_name', ['field_options' => ['class' => 'FooBar']]);

        $builder = new ProxyQuery(new QueryBuilder());

        $filter->filter($builder, 'alias', 'field', ['type' => 'foo']);

        $this->assertEquals([], $builder->query);
        $this->assertEquals(false, $filter->isActive());
    }

    public function testFilter()
    {
        $filter = new ClassFilter();
        $filter->initialize('field_name', ['field_options' => ['class' => 'FooBar']]);

        $builder = new ProxyQuery(new QueryBuilder());

        $filter->filter($builder, 'alias', 'field', ['type' => EqualType::TYPE_IS_EQUAL, 'value' => 'type']);
        $filter->filter($builder, 'alias', 'field', ['type' => EqualType::TYPE_IS_NOT_EQUAL, 'value' => 'type']);
        $filter->filter($builder, 'alias', 'field', ['value' => 'type']);

        $expected = [
            'alias INSTANCE OF type',
            'alias NOT INSTANCE OF type',
            'alias INSTANCE OF type',
        ];

        $this->assertEquals($expected, $builder->query);
        $this->assertEquals(true, $filter->isActive());
    }
}
