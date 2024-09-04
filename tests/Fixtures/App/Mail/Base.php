<?php

namespace CapsulesCodes\InertiaMailable\Tests\Fixtures\App\Mail;


use CapsulesCodes\InertiaMailable\Mail\Mailable;
use CapsulesCodes\InertiaMailable\Mail\Mailables\Content;


class Base extends Mailable
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
