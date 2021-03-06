<?php
namespace Neos\Form\Tests\Unit\Factory;

/*
 * This file is part of the Neos.Form package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Tests\UnitTestCase;
use Neos\Form\Core\Model\Page;
use Neos\Form\Factory\ArrayFormFactory;
use Neos\Form\FormElements\GenericFormElement;

/**
 * @covers \Neos\Form\Factory\ArrayFormFactory<extended>
 */
class ArrayFormFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function simpleFormObjectIsReturned()
    {
        $factory = $this->getArrayFormFactory();

        $configuration = array(
            'identifier' => 'myFormIdentifier'
        );
        $form = $factory->build($configuration, 'default');
        $this->assertSame('myFormIdentifier', $form->getIdentifier());
    }

    /**
     * @test
     */
    public function formObjectWithSubRenderablesIsReturned()
    {
        $factory = $this->getArrayFormFactory();

        $configuration = array(
            'identifier' => 'myFormIdentifier',
            'renderables' => array(
                array(
                    'identifier' => 'page1',
                    'type' => 'Neos.Form:Page',
                    'renderables' => array(
                        array(
                            'identifier' => 'element1',
                            'type' => 'Neos.Form:TestElement',
                            'properties' => array(
                                'options' => array(
                                    0 => array(
                                        '_key' => 'MyKey',
                                        '_value' => 'MyValue'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        $form = $factory->build($configuration, 'default');
        $page1 = $form->getPageByIndex(0);
        $this->assertSame('page1', $page1->getIdentifier());
        $element1 = $form->getElementByIdentifier('element1');
        $this->assertSame('element1', $element1->getIdentifier());
        $this->assertSame(array('options' => array('MyKey' => 'MyValue')), $element1->getProperties());
    }

    /**
     * @test
     * @expectedException \Neos\Form\Exception\IdentifierNotValidException
     */
    public function renderableWithoutIdentifierThrowsException()
    {
        $factory = $this->getArrayFormFactory();

        $configuration = array(
            'identifier' => 'myFormIdentifier',
            'renderables' => array(
                array(
                    // identifier missing
                )
            )
        );
        $factory->build($configuration, 'default');
    }

    /**
     * @return ArrayFormFactory
     */
    protected function getArrayFormFactory()
    {
        $settings = array(
            'presets' => array(
                'default' => array(
                    'formElementTypes' => array(
                        'Neos.Form:Form' => array(

                        ),
                        'Neos.Form:Page' => array(
                            'implementationClassName' => Page::class
                        ),
                        'Neos.Form:TestElement' => array(
                            'implementationClassName' => GenericFormElement::class
                        )
                    )
                )
            )
        );

        $accessibleFactory = $this->buildAccessibleProxy(ArrayFormFactory::class);
        $factory = new $accessibleFactory;
        $factory->_set('formSettings', $settings);
        return $factory;
    }
}
