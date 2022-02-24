<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests\Form;

use App\Entity\Hirsch;
use App\Entity\Holidays;
use App\Form\HolidayType;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\Test\TypeTestCase;

class HolidayTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $start = new \DateTime('-1 day');
        $formData = [
            'start' => $start,
            'end'   => null,
        ];

        $model = new Holidays();
        // $model will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(HolidayType::class, $model);

        $expected = new Holidays();
        $expected->setStart($start);
        // ...populate $object properties with the data stored in $formData

        // submit the data to the form directly
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $model was modified as expected when the form was submitted
        $this->assertEquals($expected, $model);

        $this->expectExceptionObject(new LogicException('The form\'s view data is expected to be a "App\Entity\Holidays", but it is a "App\Entity\Hirsch". You can avoid this error by setting the "data_class" option to null or by adding a view transformer that transforms "App\Entity\Hirsch" to an instance of "App\Entity\Holidays".'));
        $this->factory->create(HolidayType::class, new Hirsch());
    }

    public function testCustomFormView(): void
    {
        $formData = new Holidays();
        // ... prepare the data as you need
        $start = new \DateTime('-1 day');
        $formData->setStart($start);
        $formData->setEnd(new \DateTime('+1 day'));

        // The initial data may be used to compute custom view variables
        $view = $this->factory->create(HolidayType::class, $formData)
            ->createView();

        /** @var FormErrorIterator $errors */
        $errors = $view->vars['errors'];
        $this->assertTrue($errors->count() == 0);
        $this->assertEquals($start, $view->vars['value']->getStart());
        $this->assertNotEmpty($view->children['start']->vars['row_attr']);
        $this->assertNotEmpty($view->children['end']->vars['row_attr']);
    }
}
