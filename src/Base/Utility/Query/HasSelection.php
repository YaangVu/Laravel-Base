<?php
/**
 * @Author yaangvu
 * @Date   Feb 05, 2023
 */

namespace YaangVu\LaravelBase\Base\Utility\Query;

trait HasSelection
{
    private string|array $selections = '*';

    /**
     * @return array|string
     */
    public function getSelections(): array|string
    {
        return $this->selections;
    }

    /**
     * @param array|string $selections
     *
     * @return static
     */
    public function setSelections(array|string $selections): static
    {
        $this->selections = $selections;

        return $this;
    }

    /**
     * @Description  Parse request param to selections
     *
     * @Author       yaangvu
     * @Date         Feb 21, 2023
     *
     * @param string|array $selections
     *
     * @return $this
     */
    public function parseSelections(string|array $selections): static
    {
        if (is_array($selections))
            return $this->setSelections($selections);

        $selections = preg_split("/,+/", $selections);
        $selections = array_map('trim', $selections);

        return $this->setSelections($selections);
    }
}