<?php

namespace Arbory\Base\Admin\Form\Overview\Navigation;


use Illuminate\Contracts\Support\Renderable;

interface Navigable
{

    public function navigation(): Renderable;

}

