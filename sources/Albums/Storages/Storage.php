<?php
namespace Ciebit\Photos\Albums\Storages;

use Ciebit\Photos\Albums\Collection;
use Ciebit\Photos\Albums\Album;
use Ciebit\Photos\Albums\Status;

interface Storage
{
    public function addFilterById(string $operator, string ...$ids): self;

    public function addFilterByStatus(string $operator, Status ...$statusList): self;

    public function addFilterByUri(string $operator, string ...$uriList): self;

    public function addOrderBy(string $column, string $order): self;

    public function findOne(): ?Album;

    public function findAll(): Collection;

    public function getTotalRecords(): int;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;

}
