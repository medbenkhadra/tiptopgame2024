<?php

namespace App\Service\Mailer;

use App\Entity\EmailingHistory;
use App\Entity\EmailService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostManMailerService
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    private string $mailtrapHost;
    private string $mailtrapUser;
    private string $mailtrapPassword;
    private string $mailtrapPort;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var Environment
     */
    private $twig;


    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(MailerInterface $mailer, string $mailtrapHost, string $mailtrapUser, string $mailtrapPassword, string $mailtrapPort, EntityManagerInterface $entityManager
    , Environment $twig , ParameterBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->mailtrapHost = $mailtrapHost;
        $this->mailtrapUser = $mailtrapUser;
        $this->mailtrapPassword = $mailtrapPassword;
        $this->mailtrapPort = $mailtrapPort;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->params = $params;


    }

    public function initMailer(): PHPMailer
    {

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $this->mailtrapHost;
        $mail->SMTPAuth = true;
        $mail->Username = $this->mailtrapUser;
        $mail->Password = $this->mailtrapPassword;
        $mail->SMTPOptions = [
            'ssl'=> [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $this->mailtrapPort;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPAutoTLS = false;
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        return $mail;

    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function sendEmailTemplate($emailService , $receiver , $options): bool
    {

        $emailServiceEntity = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => $emailService]);
        $emailServiceTemplates = $emailServiceEntity->getEmailTemplates();

        $recipient = '';
        $subject = '';
        $body = '';

        $template = null;

        foreach ($emailServiceTemplates as $emailServiceTemplate) {
            if ($emailServiceTemplate->getRequired()) {
                $template = $emailServiceTemplate;
                $recipient = $receiver->getEmail();
                $subject = $emailServiceTemplate->getSubject();
                $body = $emailServiceTemplate->getContent();
            }
        }

        $getTokenExpirationDate = function ($receiver) {
                $token = $receiver->getToken();
                if ($token) {
                    $expirationDate = $receiver->getTokenExpiredAt();
                    if ($expirationDate) {
                        return $expirationDate->format('d/m/Y');
                    }
                }
                return '';
        };

            $activateAccountLinkClient = function ($receiver , $emailServiceEntity)  {
            if ($emailServiceEntity->getName() != EmailService::EMAILSERVICE_ACCOUNT_ACTIVATION_CLIENT) {
                return '';
            }


            $receiver_email = $receiver->getEmail();
            $token = $receiver->getToken();


            $baseUrl = $this->params->get('app_base_url');
            $link = $baseUrl . '/dashboard/client/activate_account/?email=' . $receiver_email . '&token=' . $token;

            return '<a href="' . $link . '" class="activateBtn">Vérifier mon compte</a>';
        };

        $getResetPasswordLink = function ($receiver) {
            $token = $receiver->getToken();
            $email = $receiver->getEmail();
            $baseUrl = $this->params->get('app_base_url');
            $url = $baseUrl . '/reset_password_process/?email=' . $email . '&token=' . $token;
            return '<a href="' . $url . '" class="activateBtn">Réinitialiser mon mot de passe</a>';
        };

        if(!$receiver){
            return false;
        }

        $variableMappings = [
            'client_lastname' => $receiver->getLastname(),
            'client_firstname' => $receiver->getFirstname(),
            'client_email' => $receiver->getEmail(),
            'client_phone' => $receiver->getPhone(),
            'client_address' => $receiver->getUserPersonalInfo()?->getAddress(),
            'client_city' => $receiver->getUserPersonalInfo()?->getCity(),
            'client_country' => $receiver->getUserPersonalInfo()?->getCountry(),
            'client_zipcode' => $receiver->getUserPersonalInfo()?->getPostalCode(),
            'employee_lastname' => $receiver->getLastname(),
            'employee_firstname' => $receiver->getFirstname(),
            'employee_email' => $receiver->getEmail(),
            'employee_phone' => $receiver->getPhone(),
            'store_name' => $receiver->getStores()[0]?->getName(),
            'store_email' => $receiver->getStores()[0]?->getEmail(),
            'store_address' => $receiver->getStores()[0]?->getAddress(),
            'store_zipcode' => $receiver->getStores()[0]?->getPostalCode(),
            'store_city' => $receiver->getStores()[0]?->getCity(),
            'store_country' => $receiver->getStores()[0]?->getCountry(),
            'ticket_number' => $options ? $options['ticket'] ? '#'.$options['ticket']->getTicketCode() :'' :'' ,
            'ticket_created_at' => $options && $options['ticket'] ? ($options['ticket']->getTicketGeneratedAt() ? $options['ticket']->getTicketGeneratedAt()->format('d/m/Y') : '') : '',
            'ticket_printed_at' => $options && $options['ticket'] ? ($options['ticket']->getTicketPrintedAt() ? $options['ticket']->getTicketPrintedAt()->format('d/m/Y') : '') : '',
            'ticket_confirmed_at' => $options && $options['ticket'] ? ($options['ticket']->getUpdatedAt() ? $options['ticket']->getUpdatedAt()->format('d/m/Y') : '') : '',
            'reset_password_link' => $getResetPasswordLink($receiver),
            'password_reset_link_employee' => $getResetPasswordLink($receiver),
            'activate_account_link_client' => $activateAccountLinkClient($receiver , $emailServiceEntity),
            'activate_account_link_employee' => 'getActivateAccountLinkEmployee',
            'token_expiration_date' => $getTokenExpirationDate($receiver),
            'wheel_of_fortune_link' => 'www.dsp5-archi-f23-15m-g7.fr/',
            'win_declaration_link' => 'www.dsp5-archi-f23-15m-g7.fr/',
            'password' => $options['password'] ?? '',
        ];





        $rawBody = $body;
        $body = $this->replacePlaceholders($rawBody, $variableMappings);
        $subject = $this->replacePlaceholders($subject, $variableMappings);

        $wrappedBody = $this->renderTemplate('email/wrapper.html.twig', ['content' => $body]);

        return $this->sendEmail($recipient, $subject, $wrappedBody, $emailService , $receiver);

        }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function renderTemplate($template, $context): string
    {
        return $this->twig->render($template, $context);
    }

    private function replacePlaceholders($content, $variablesValues)
    {
        foreach ($variablesValues as $variable => $value) {
            $placeholder = '{{ ' . $variable . ' }}';
            $content = $content && $value ? str_replace($placeholder, $value, $content) : $content;
        }

        return $content;
    }


        public function sendEmail($recipient, $subject, $body, $emailService , $receiver): bool
        {

            try {

                $mail = $this->initMailer();
                $mail->setFrom('tiptop@dsp5-archi-f23-15m-g7.com', 'Thé - Tiptop');

                if(!$recipient){
                    return false;
                }

                $mail->addAddress($recipient);

                $subject = $this->convertHtmlToText($subject);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->send();
                $this->createEmailingHistory($emailService , $receiver);
                return true;
            } catch (Exception $e) {
                dd($e->getMessage());
                return false;
            }
        }

        private
        function convertHtmlToText($subject): string
        {
            $subject = strip_tags($subject);
            return html_entity_decode($subject);
        }

    private function createEmailingHistory($emailService, $receiver): void
    {
        $emailService = $this->entityManager->getRepository(EmailService::class)->findOneBy(['name' => $emailService]);

        $emailingHistory = new EmailingHistory();
        $emailingHistory->setService($emailService);
        $emailingHistory->setReceiver($receiver);
        $emailingHistory->setSentAt(new DateTime());

        $this->entityManager->persist($emailingHistory);
        $this->entityManager->flush();
    }


}