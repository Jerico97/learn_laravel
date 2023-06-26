<?php

declare(strict_types=1);

namespace Accounts\Contracts;

use Accounts\Contracts\DataTransferObjects\ShopDto;
use App\Contracts\DataTransferObjects\PaginatedListDto;

interface UserServiceContract
{
    /**
     * Получить магазин по ID
     *
     * @param int $id
     *
     * @return \Shops\Contracts\DataTransferObjects\ShopDto
     *
     * @throws \App\Exceptions\EntityValidationException
     */
    public function getById(int $id): ShopDto;

    /**
     * Получить список пользователей
     *
     * @param ?string $role
     * @param ?string $searchQuery
     * @param int $perPage
     * @param array $linksQueryString Дополнительные GET-параметры, которые нужно добавить к ссылкам
     *
     * @return PaginatedListDto
     */
    public function list(
        ?string $role = null,
        ?string $searchQuery = null,
        int $perPage = 25,
        array $linksQueryString = []
    ): PaginatedListDto;

    /**
     * Получить список магазинов ID => наименование
     * @return array [id => title]
     */
    public function getTeamUserNames(): array;

    /**
     * Создать магазин
     *
     * @param string $title
     * @param string $url
     *
     * @return \Shops\Contracts\DataTransferObjects\ShopDto
     *
     * @throws \App\Exceptions\EntityValidationException
     * @throws \App\Exceptions\EntityNotCreatedException
     */
    public function create(
        string $title,
        string $url,
    ): \Shops\Contracts\DataTransferObjects\ShopDto;

    /**
     * Отредактировать магазин
     *
     * @param int $id
     * @param ?string $title
     * @param ?string $url
     *
     * @return \Shops\Contracts\DataTransferObjects\ShopDto
     *
     * @throws \App\Exceptions\EntityNotFoundException
     * @throws \App\Exceptions\EntityValidationException
     * @throws \App\Exceptions\EntityNotUpdatedException
     */
    public function update(
        int $id,
        ?string $title = null,
        ?string $url = null,
    ): UserDto;

    /**
     * Удалить магазин
     *
     * @param int $id
     *
     * @return void
     *
     * @throws \App\Exceptions\EntityNotFoundException
     * @throws \App\Exceptions\EntityNotDeletedException
     */
    public function delete(int $id): void;
}
