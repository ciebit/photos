<?php
namespace Ciebit\Photos\Storages;

use Ciebit\Photos\Collection;
use Ciebit\Photos\Photo;

interface Storage
{
    public function addFilterById(string $operator, int ...$id): self;

    public function addFilterByStatus(string $operator, Status ...$status): self;

    public function addOrderBy(string $column, string $order): self;

    public function get(): ?Photo;

    public function getAll(): Collection;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;

    public function getTotalRecords(): int;
}
