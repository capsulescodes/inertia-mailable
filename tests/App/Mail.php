<?php

namespace CapsulesCodes\InertiaMailable\Tests\App;


use CapsulesCodes\InertiaMailable\Mail\Mailable;


class Mail extends Mailable
{
    private string $email;
    private string $name;


    public function __construct( string $email, string $name )
    {
        $this->email = $email;
        $this->name = $name;
    }

    public function prepare() : void
    {
        $this->view( 'Component', [ 'name' => $this->name ] );
    }

    public function build() : self
    {
        return $this->to( $this->email, $this->name )->subject( 'Hello Inertia Mailable World' );
    }
}
