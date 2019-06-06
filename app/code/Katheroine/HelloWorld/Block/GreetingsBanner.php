<?php declare(strict_types=1);

namespace Katheroine\HelloWorld\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Phrase;

class GreetingsBanner extends Template
{
    /**
     * @return Phrase
     */
    public function getDefaultGreeting(): Phrase
    {
        return __('Hello World!');
    }
}
