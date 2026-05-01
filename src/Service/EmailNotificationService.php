<?php

namespace App\Service;

use App\Entity\Bug;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationService
{
    private $mailer;
    private $adminEmail;

    public function __construct(MailerInterface $mailer, string $adminEmail = 'admin@rth.local')
    {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function sendBugReportNotification(Bug $bug): void
    {
        $email = (new Email())
            ->from('system@rth.local')
            ->to($this->adminEmail)
            ->subject('New Bug Reported: ' . $bug->getSummary())
            ->html(sprintf(
                '<h3>New Bug Reported in Project: %s</h3>
                <p><strong>Summary:</strong> %s</p>
                <p><strong>Severity:</strong> %s</p>
                <p><strong>Priority:</strong> %s</p>
                <p><strong>Description:</strong> %s</p>
                <hr>
                <p>View project details: <a href="http://localhost:8081/project/%d">Click here</a></p>',
                $bug->getProject()->getName(),
                $bug->getSummary(),
                $bug->getSeverity(),
                $bug->getPriority(),
                nl2br($bug->getDescription()),
                $bug->getProject()->getId()
            ));

        $this->mailer->send($email);
    }
}
