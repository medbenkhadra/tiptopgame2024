<?php

namespace App\Tests\Unit\Entity;

use App\Entity\EmailService;
use App\Entity\EmailTemplate;
use App\Entity\EmailTemplateVariable;
use PHPUnit\Framework\TestCase;


class EmailServiceTest extends TestCase
{
    public function testGetTemplatesJson(): void
    {
        $emailService = new EmailService();
        $template1 = new EmailTemplate();
        $template1
            ->setName('Template 1')
            ->setSubject('Subject 1')
            ->setContent('Content 1')
            ->setTitle('Title 1')
            ->setDescription('Description 1')
            ->setRequired(true)
            ->setType('Type 1');
        $template2 = new EmailTemplate();
        $template2
            ->setName('Template 2')
            ->setSubject('Subject 2')
            ->setContent('Content 2')
            ->setTitle('Title 2')
            ->setDescription('Description 2')
            ->setRequired(false)
            ->setType('Type 2');

        $emailService->addEmailTemplate($template1);
        $emailService->addEmailTemplate($template2);

        $expectedJson = [
            [
                'id' => null,
                'name' => 'Template 1',
                'subject' => 'Subject 1',
                'content' => 'Content 1',
                'title' => 'Title 1',
                'description' => 'Description 1',
                'type' => 'Type 1',
            ],
            [
                'id' => null,
                'name' => 'Template 2',
                'subject' => 'Subject 2',
                'content' => 'Content 2',
                'title' => 'Title 2',
                'description' => 'Description 2',
                'type' => 'Type 2',
            ],
        ];

        $this->assertEquals($expectedJson, $emailService->getTemplatesJson());
    }

    public function testGetVariablesJson(): void
    {
        $emailService = new EmailService();
        $variable1 = new EmailTemplateVariable();
        $variable1
            ->setName('Variable 1')
            ->setLabel('Label 1');
        $variable2 = new EmailTemplateVariable();
        $variable2
            ->setName('Variable 2')
            ->setLabel('Label 2');

        $emailService->addVariable($variable1);
        $emailService->addVariable($variable2);

        $expectedJson = [
            [
                'id' => null,
                'name' => 'Variable 1',
                'label' => 'Label 1',
            ],
            [
                'id' => null,
                'name' => 'Variable 2',
                'label' => 'Label 2',
            ],
        ];

        $this->assertEquals($expectedJson, $emailService->getVariablesJson());
    }

    public function testGetEmailServiceJson(): void
    {
        $emailService = new EmailService();
        $emailService
            ->setName('Service Name')
            ->setLabel('Service Label')
            ->setDescription('Service Description');

        $template1 = new EmailTemplate();
        $template1
            ->setName('Template 1')
            ->setSubject('Subject 1')
            ->setContent('Content 1')
            ->setTitle('Title 1')
            ->setDescription('Description 1')
            ->setType('Type 1');

        $variable1 = new EmailTemplateVariable();
        $variable1
            ->setName('Variable 1')
            ->setLabel('Label 1');

        $emailService->addEmailTemplate($template1);
        $emailService->addVariable($variable1);

        $expectedJson = [
            'id' => null,
            'name' => 'Service Name',
            'label' => 'Service Label',
            'description' => 'Service Description',
            'templates' => [
                [
                    'id' => null,
                    'name' => 'Template 1',
                    'subject' => 'Subject 1',
                    'content' => 'Content 1',
                    'title' => 'Title 1',
                    'description' => 'Description 1',
                    'type' => 'Type 1',
                ],
            ],
            'variables' => [
                [
                    'id' => null,
                    'name' => 'Variable 1',
                    'label' => 'Label 1',
                ],
            ],
        ];

        $this->assertEquals($expectedJson, $emailService->getEmailServiceJson());
    }



    public function testSetId(): void
    {
        $emailService = new EmailService();
        $emailService->setId(1);
        $this->assertEquals(1, $emailService->getId());
    }


    public function testGetEmailTemplates(): void
    {
        $emailService = new EmailService();
        $template1 = new EmailTemplate();
        $template1
            ->setName('Template 1')
            ->setSubject('Subject 1')
            ->setContent('Content 1')
            ->setTitle('Title 1')
            ->setDescription('Description 1')
            ->setType('Type 1');
        $template2 = new EmailTemplate();
        $template2
            ->setName('Template 2')
            ->setSubject('Subject 2')
            ->setContent('Content 2')
            ->setTitle('Title 2')
            ->setDescription('Description 2')
            ->setType('Type 2');

        $emailService->addEmailTemplate($template1);
        $emailService->addEmailTemplate($template2);

        $this->assertCount(2, $emailService->getEmailTemplates());
        $this->assertTrue($emailService->getEmailTemplates()->contains($template1));
        $this->assertTrue($emailService->getEmailTemplates()->contains($template2));
    }


    public function testRemoveEmailTemplate(): void
    {
        $emailService = new EmailService();
        $template1 = new EmailTemplate();
        $template1
            ->setName('Template 1')
            ->setSubject('Subject 1')
            ->setContent('Content 1')
            ->setTitle('Title 1')
            ->setDescription('Description 1')
            ->setType('Type 1');
        $template2 = new EmailTemplate();
        $template2
            ->setName('Template 2')
            ->setSubject('Subject 2')
            ->setContent('Content 2')
            ->setTitle('Title 2')
            ->setDescription('Description 2')
            ->setType('Type 2');

        $emailService->addEmailTemplate($template1);
        $emailService->addEmailTemplate($template2);
        $emailService->removeEmailTemplate($template1);

        $this->assertCount(1, $emailService->getEmailTemplates());
        $this->assertFalse($emailService->getEmailTemplates()->contains($template1));
        $this->assertTrue($emailService->getEmailTemplates()->contains($template2));
    }


    public function testGetVariables(): void
    {
        $emailService = new EmailService();
        $variable1 = new EmailTemplateVariable();
        $variable1
            ->setName('Variable 1')
            ->setLabel('Label 1');
        $variable2 = new EmailTemplateVariable();
        $variable2
            ->setName('Variable 2')
            ->setLabel('Label 2');

        $emailService->addVariable($variable1);
        $emailService->addVariable($variable2);

        $this->assertCount(2, $emailService->getVariables());
        $this->assertTrue($emailService->getVariables()->contains($variable1));
        $this->assertTrue($emailService->getVariables()->contains($variable2));
    }

    public function testRemoveVariable(): void
    {
        $emailService = new EmailService();
        $variable1 = new EmailTemplateVariable();
        $variable1
            ->setName('Variable 1')
            ->setLabel('Label 1');
        $variable2 = new EmailTemplateVariable();
        $variable2
            ->setName('Variable 2')
            ->setLabel('Label 2');

        $emailService->addVariable($variable1);
        $emailService->addVariable($variable2);
        $emailService->removeVariable($variable1);

        $this->assertCount(1, $emailService->getVariables());
        $this->assertFalse($emailService->getVariables()->contains($variable1));
        $this->assertTrue($emailService->getVariables()->contains($variable2));
    }


    public function testGetRequired(): void
    {
        $emailService = new EmailService();
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setId(1);
        $emailTemplate->setRequired(true);
        $emailService->addEmailTemplate($emailTemplate);
        $this->assertTrue($emailTemplate->getRequired());
    }


    public function testGetServiceJson(): void
    {
        $emailService = new EmailService();
        $emailService
            ->setId(1);


        $emailService->setName('Service Name')
            ->setLabel('Service Label')
            ->setDescription('Service Description');

        $template1 = new EmailTemplate();
        $template1
            ->setName('Template 1')
            ->setSubject('Subject 1')
            ->setContent('Content 1')
            ->setTitle('Title 1')
            ->setDescription('Description 1')
            ->setType('Type 1');

        $variable1 = new EmailTemplateVariable();
        $variable1
            ->setName('Variable 1')
            ->setLabel('Label 1');

        $emailService->addEmailTemplate($template1);
        $emailService->addVariable($variable1);

        $expectedJson = [
            'id' => 1,
            'name' => 'Service Name',
            'description' => 'Service Description',
            'label' => 'Service Label',
            'templates' => [
                [
                    'id' => null,
                    'name' => 'Template 1',
                    'subject' => 'Subject 1',
                    'content' => 'Content 1',
                    'title' => 'Title 1',
                    'description' => 'Description 1',
                    'type' => 'Type 1',
                ],
            ],
            'variables' => [
                [
                    'id' => null,
                    'name' => 'Variable 1',
                    'label' => 'Label 1',
                ],
            ],
        ];


        $this->assertEquals($expectedJson, $template1->getServiceJson());
    }


        public function testGetService(): void
    {
        $emailService = new EmailService();
        $emailTemplateVariable = new EmailTemplateVariable();
        $emailTemplateVariable->setId(1);
        $emailService->addVariable($emailTemplateVariable);
        $this->assertEquals([$emailService->getEmailServiceJson()], $emailTemplateVariable->getServiceAsJson());
    }

    public function testAddService(): void
    {
        $emailService = new EmailService();
        $emailTemplateVariable = new EmailTemplateVariable();
        $emailTemplateVariable->addService($emailService);
        $this->assertEquals([$emailService->getEmailServiceJson()], $emailTemplateVariable->getServiceAsJson());
    }



    public function testAddServiceAux(): void
    {
        $emailService = new EmailService();
        $emailTemplateVariable = new EmailTemplateVariable();
        $emailTemplateVariable->addService($emailService);
        $this->assertEquals([$emailService->getEmailServiceJson()], $emailTemplateVariable->getServiceAsJson());
    }

    public function testGetServiceAux(): void
    {
        $emailService = new EmailService();
        $emailTemplateVariable = new EmailTemplateVariable();
        $emailTemplateVariable->setId(1);
        $emailService->addVariable($emailTemplateVariable);
        $this->assertEquals([$emailService->getEmailServiceJson()], $emailTemplateVariable->getServiceAsJson());
    }

    public function testAddServices(): void
    {
        $emailService = new EmailService();
        $emailTemplateVariable = new EmailTemplateVariable();
        $emailTemplateVariable->addServices([$emailService]);
        $this->assertEquals([$emailService->getEmailServiceJson()], $emailTemplateVariable->getServiceAsJson());
    }

    public function testRemoveServices(): void
    {
        $emailService = new EmailService();
        $emailTemplateVariable = new EmailTemplateVariable();
        $emailTemplateVariable->addServices([$emailService]);
        $emailTemplateVariable->removeServices([$emailService]);
        $this->assertEquals([], $emailTemplateVariable->getServiceAsJson());
    }


    public function testGetServiceAuxAux(): void
    {
        $emailService = new EmailService();
        $emailTemplateVariable = new EmailTemplateVariable();
        $emailTemplateVariable->setId(1);
        $emailService->addVariable($emailTemplateVariable);
        $this->assertEquals($emailService->getEmailServiceJson(), $emailTemplateVariable->getService()->first()->getEmailServiceJson());
    }

    public function testGetServicesJson(): void
    {
        $emailService = new EmailService();
        $emailService
            ->setId(1);

        $emailService->setName('Service Name')
            ->setLabel('Service Label')
            ->setDescription('Service Description');

        $template1 = new EmailTemplate();
        $template1
            ->setName('Template 1')
            ->setSubject('Subject 1')
            ->setContent('Content 1')
            ->setTitle('Title 1')
            ->setDescription('Description 1')
            ->setType('Type 1');

        $variable1 = new EmailTemplateVariable();
        $variable1
            ->setName('Variable 1')
            ->setLabel('Label 1');

        $emailService->addEmailTemplate($template1);
        $emailService->addVariable($variable1);

        $expectedJson = [
            [
                'id' => 1,
                'name' => 'Service Name',
                'label' => 'Service Label',
            ],
        ];


        $this->assertEquals($expectedJson, $variable1->getServicesJson());

    }


}