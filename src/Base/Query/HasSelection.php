<?php
/**
 * @Author yaangvu
 * @Date   Nov 08, 2022
 */

namespace YaangVu\LaravelBase\Base\Query;

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
     * @return HasSelection
     */
    public function setSelections(array|string $selections): static
    {
        $this->selections = $selections;

        return $this;
    }

    /**
     * Parse request params to selections for query
     *
     * @Author yaangvu
     * @Date   Nov 08, 2022
     *
     * @param array $params
     *
     * @return $this
     */
    public function parseSelections(array $params = []): static
    {
        $params = $params ?: request()->all();
        $this->setSelections($params['select'] ?? '*');

        return $this;
    }
}
