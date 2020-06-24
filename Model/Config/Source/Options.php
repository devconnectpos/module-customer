<?php

namespace SM\Customer\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Options extends AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Thợ lợp ngói, lát sàn'), 'value' => '1'],
            ['label' => __('Thợ mộc'), 'value' => '2'],
            ['label' => __('Thợ hàn'), 'value' => '3'],
            ['label' => __('Thợ sơn, lát sàn'), 'value' => '4'],
            ['label' => __('Thợ điện'), 'value' => '5'],
            ['label' => __('Thợ sửa ống nước'), 'value' => '6'],
            ['label' => __('Các ngành nghề khác'), 'value' => '7']
        ];

        return $this->_options;
    }
}
