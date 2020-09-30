<?php
declare(strict_types=1);

namespace SM\Customer\Api\Data;

/**
 * Interface ScgCustomerGroupInterface
 * @package SM\Customer\Api\Data
 */
interface ScgCustomerGroupInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $value
     * @return $this
     */
    public function setId($value);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);

    /**
     * @return string
     */
    public function getNote();

    /**
     * @param string $value
     * @return $this
     */
    public function setNote($value);
}
