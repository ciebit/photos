<?php
namespace Ciebit\Photos\Storages;

use Ciebit\Photos\Collection;
use Ciebit\Photos\Photo;
use Ciebit\Photos\Status;

interface Storage
{
    public function addFilterByAlbumId(string $operator, string ...$ids): self;

    public function addFilterById(string $operator, string ...$ids): self;

    public function addFilterByStatus(string $operator, Status ...$statusList): self;

    public function addOrderBy(string $column, string $order): self;

    public function getTotalRecords(): int;

    public function findAll(): Collection;

    public function findOne(): ?Photo;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;
}
