<?php
namespace Ciebit\Photos\Albums\Storages;

use Ciebit\Photos\Albums\Collection;
use Ciebit\Photos\Albums\Album;
use Ciebit\Photos\Albums\Status;

interface Storage
{
    public function addFilterById(string $operator, string ...$ids): self;

    public function addFilterByStatus(string $operator, Status ...$statusList): self;

    public function addOrderBy(string $column, string $order): self;

    public function get(): ?Album;

    public function getAll(): Collection;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;

    public function getTotalRecords(): int;
}
