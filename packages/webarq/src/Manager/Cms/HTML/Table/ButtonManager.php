<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/9/2017
 * Time: 10:46 AM
 */

namespace Webarq\Manager\Cms\HTML\Table;


use Illuminate\Contracts\Support\Htmlable;
use Webarq\Manager\SetPropertyManagerTrait;

class ButtonManager implements Htmlable
{
    use SetPropertyManagerTrait;

    protected $attributes = [];

    protected $containerView;

    protected $type;

    protected $permalink;

    protected $label;

    protected $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;

        $this->setPropertyFromOptions($settings);
    }

    public function toHtml()
    {
        $html = \Html::link($this->permalink, $this->label ? : $this->type, $this->attributes);

        if (!empty($this->containerView)) {
            return view($this->containerView, $this->settings);
        }

        return $html;
    }
}