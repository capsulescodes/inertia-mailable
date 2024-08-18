<?php

namespace CapsulesCodes\InertiaMailable\Tests\Fixtures;


use CapsulesCodes\InertiaMailable\Mail\Mailable;
use CapsulesCodes\InertiaMailable\Mail\Mailables\Content;


class Mail extends Mailable
{
    private string $name;


    public function __construct( string $name )
    {
        $this->name = $name;
    }

    public function content() : Content
    {
        return new Content( view : 'Welcome', props : [ 'name' => $this->name ] );
    }

    public function attachments() : array
    {
        return [];
    }
}
