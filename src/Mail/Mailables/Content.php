<?php

namespace CapsulesCodes\InertiaMailable\Mail\Mailables;

use Illuminate\Mail\Mailables\Content as Base;

class Content extends Base
{
    public function __construct( string | null $root = null, string | null $view = null, string | null $html = null, string | null $text = null, array $with = [], array $props = [], string | null $htmlString = null )
    {
        $this->root = $root;
        $this->view = $view;
        $this->html = $html;
        $this->text = $text;
        $this->with = $with;
        $this->props = $props;
        $this->htmlString = $htmlString;
    }
}
