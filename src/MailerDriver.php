<?php

declare(strict_types=1);

namespace Pollen\Mail;

abstract class MailerDriver implements MailerDriverInterface
{
    /**
     * @inheritDoc
     */
    public function linearizeContact(string $email, ?string $name = null): string
    {
        return $name !== null ? "$name <$email>" : $email;
    }

    /**
     * @inheritDoc
     */
    public function linearizeContacts(array $contacts): array
    {
        foreach($contacts as &$contact) {
            $contact = $this->linearizeContact(...$contact);
        }
        return $contacts;
    }
}
