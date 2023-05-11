<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Tests\Form;

use App\Entity\Hirsch;
use App\Entity\Paypalmes;
use App\Form\PaypalmesType;
use App\Tests\Traits\FormValidationTrait;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class PaypalmesTypeTest extends TypeTestCase
{
    use FormValidationTrait;

    public function testSubmitValidData(): void
    {
        $link = 'test123';
        $name = 'test';
        $email = 'test@test.de';
        $formData = [
            'link' => $link,
            'name' => $name,
            'email' => $email,
        ];

        $model = new Paypalmes();
        // $model will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(PaypalmesType::class, $model);

        $expected = new Paypalmes();
        $expected->setLink($link);
        $expected->setName($name);
        $expected->setEmail($email);
        // ...populate $object properties with the data stored in $formData

        // submit the data to the form directly
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $model was modified as expected when the form was submitted
        $this->assertEquals($expected, $model);

        $this->expectExceptionObject(
            new LogicException('The form\'s view data is expected to be a "App\Entity\Paypalmes", but it is a "App\Entity\Hirsch". You can avoid this error by setting the "data_class" option to null or by adding a view transformer that transforms "App\Entity\Hirsch" to an instance of "App\Entity\Paypalmes".')
        );
        $this->factory->create(PaypalmesType::class, new Hirsch());
    }

    public function testCustomFormView(): void
    {
        $formData = new Paypalmes();
        // ... prepare the data as you need
        $link = 'test123';
        $name = 'test';
        $email = 'test@test.de';
        $formData->setLink($link);
        $formData->setName($name);
        $formData->setEmail($email);

        // The initial data may be used to compute custom view variables
        $view = $this->factory->create(PaypalmesType::class, $formData)
            ->createView();

        /** @var FormErrorIterator $errors */
        $errors = $view->vars['errors'];
        $this->assertTrue(0 == $errors->count());
        $this->assertEquals($link, $view->vars['value']->getLink());
        $this->assertNotEmpty($view->children['link']->vars['row_attr']);
        $this->assertNotEmpty($view->children['name']->vars['row_attr']);
        $this->assertNotEmpty($view->children['email']->vars['row_attr']);

        $this->assertEquals('Paypal.me Link', $view->children['link']->vars['label']);
        $this->assertEquals('Dein Name', $view->children['name']->vars['label']);
        $this->assertEquals('Deine E-Mail', $view->children['email']->vars['label']);
        $this->assertEquals('Speichern', $view->children['submit']->vars['label']);

        $this->assertNotEmpty($view->children['link']->vars['attr']);
        $this->assertNotEmpty($view->children['email']->vars['attr']);
        $this->assertNotEmpty($view->children['name']->vars['attr']);
        $this->assertNotEmpty($view->children['submit']->vars['attr']);
    }

    /**
     * @throws \Exception
     */
    public function testNoValidLink(): void
    {
        $model = new Paypalmes();
        $violations = $this->getFormViolations($model, PaypalmesType::class, ['link' => 'test', 'name' => 'Test']);
        $this->assertCount(1, $violations);
        /** @var ConstraintViolation $violation */
        $violation = $violations[0];
        $this->assertEquals('paypal.link.invalid', $violation->getMessage());
    }

    /**
     * @throws \Exception
     */
    public function testNoValidEmail(): void
    {
        $model = new Paypalmes();
        $violations = $this->getFormViolations($model, PaypalmesType::class, ['link' => 'https://paypal.me/rindulalp', 'name' => 'Test', 'email' => 'test']);
        $this->assertCount(1, $violations);
        /** @var ConstraintViolation $violation */
        $violation = $violations[0];
        $this->assertEquals('paypal.email.invalid', $violation->getMessage());
    }
}
