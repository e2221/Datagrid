<?php
declare(strict_types=1);

namespace e2221\Datagrid\Confirmation;


class Confirmation
{

    private string $message;

    public function __construct(string $message)
    {

        $this->message = $message;
    }

    private function createConfirmation(string $message): string
    {
        return "return confirm('$message');";
    }

    public function __toString()
    {
        return $this->createConfirmation($this->message);
    }

}