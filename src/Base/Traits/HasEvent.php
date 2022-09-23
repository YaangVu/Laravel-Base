<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Traits;

trait HasEvent
{
    private string|array $allSelectionEvents  = [];
    private string|array $selectionEvents     = [];
    private string|array $uuidSelectionEvents = [];
    private string|array $additionEvents      = [];
    private string|array $patchEvents         = [];
    private string|array $putEvents           = [];
    private string|array $idDeletionEvents    = [];
    private string|array $idsDeletionEvents   = [];
    private string|array $uuidDeletionEvents  = [];
    private string|array $uuidsDeletionEvents = [];

    /**
     * @return array|string
     */
    public function getAllSelectionEvents(): array|string
    {
        return $this->allSelectionEvents;
    }

    /**
     * @param array|string $allSelectionEvents
     *
     * @return $this
     */
    public function setAllSelectionEvents(array|string $allSelectionEvents): static
    {
        $this->allSelectionEvents = $allSelectionEvents;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getSelectionEvents(): array|string
    {
        return $this->selectionEvents;
    }

    /**
     * @param array|string $selectionEvents
     *
     * @return $this
     */
    public function setSelectionEvents(array|string $selectionEvents): static
    {
        $this->selectionEvents = $selectionEvents;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getUuidSelectionEvents(): array|string
    {
        return $this->uuidSelectionEvents;
    }

    /**
     * @param array|string $uuidSelectionEvents
     *
     * @return $this
     */
    public function setUuidSelectionEvents(array|string $uuidSelectionEvents): static
    {
        $this->uuidSelectionEvents = $uuidSelectionEvents;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getAdditionEvents(): array|string
    {
        return $this->additionEvents;
    }

    /**
     * @param array|string $additionEvents
     *
     * @return $this
     */
    public function setAdditionEvents(array|string $additionEvents): static
    {
        $this->additionEvents = $additionEvents;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getPatchEvents(): array|string
    {
        return $this->patchEvents;
    }

    /**
     * @param array|string $patchEvents
     *
     * @return $this
     */
    public function setPatchEvents(array|string $patchEvents): static
    {
        $this->patchEvents = $patchEvents;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getPutEvents(): array|string
    {
        return $this->putEvents;
    }

    /**
     * @param array|string $putEvents
     *
     * @return $this
     */
    public function setPutEvents(array|string $putEvents): static
    {
        $this->putEvents = $putEvents;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getIdDeletionEvents(): array|string
    {
        return $this->idDeletionEvents;
    }

    /**
     * @param array|string $idDeletionEvents
     *
     * @return $this
     */
    public function setIdDeletionEvents(array|string $idDeletionEvents): static
    {
        $this->idDeletionEvents = $idDeletionEvents;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getIdsDeletionEvents(): array|string
    {
        return $this->idsDeletionEvents;
    }

    /**
     * @param array|string $idsDeletionEvents
     *
     * @return $this
     */
    public function setIdsDeletionEvents(array|string $idsDeletionEvents): static
    {
        $this->idsDeletionEvents = $idsDeletionEvents;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getUuidDeletionEvents(): array|string
    {
        return $this->uuidDeletionEvents;
    }

    /**
     * @param array|string $uuidDeletionEvents
     *
     * @return $this
     */
    public function setUuidDeletionEvents(array|string $uuidDeletionEvents): static
    {
        $this->uuidDeletionEvents = $uuidDeletionEvents;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getUuidsDeletionEvents(): array|string
    {
        return $this->uuidsDeletionEvents;
    }

    /**
     * @param array|string $uuidsDeletionEvents
     *
     * @return $this
     */
    public function setUuidsDeletionEvents(array|string $uuidsDeletionEvents): static
    {
        $this->uuidsDeletionEvents = $uuidsDeletionEvents;

        return $this;
    }

}
