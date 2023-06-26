<?php

namespace Shops\Domain\Services;
use App\Contracts\DataTransferObjects\PaginatedListDto;
use App\Exceptions\EntityNotCreatedException;
use App\Exceptions\EntityNotFoundException;
use App\Exceptions\EntityNotUpdatedException;
use App\Helpers\DomainModelService;
use Illuminate\Database\QueryException;
use Shops\Contracts\DataTransferObjects\ShopDto;
use Shops\Contracts\ShopServiceContract;
use Shops\Domain\Models\Shop;


class ShopService extends DomainModelService implements ShopServiceContract
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): ShopDto
    {
        $shop = \Shops\Domain\Models\Shop::find($id);
        if (!$shop) {
            throw new EntityNotFoundException();
        }

        return $shop->toDto();
    }

    /**
     * @inheritDoc
     */
    public function list(
        ?string $role = null,
        ?string $searchQuery = null,
        int $perPage = 25,
        array $linksQueryString = []
    ): PaginatedListDto {
        $shops = \Shops\Domain\Models\Shop::query()
            ->maybeSearch($searchQuery);

        return $this->toPaginatedListDto($shops, $perPage, $linksQueryString);
    }

    /**
     * @inheritDoc
     */
    public function getTeamUserNames(): array
    {
        return \Shops\Domain\Models\Shop::query()
            ->whereIn('role', config('models.users.team_roles', ['admin']))
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function create(
        string $title,
        string $url,
    ): ShopDto {
        $shop = new \Shops\Domain\Models\Shop();
        $this->validateAndFill($shop, [
            'title' => $title,
            'url' => $url,
        ]);
        try {
            $shop->save();
        } catch (QueryException $exception) {
            throw new EntityNotCreatedException($exception->getMessage());
        }

        return $shop->toDto();
    }

    /**
     * @inheritDoc
     */
    public function update(
        int $id,
        ?string $title = null,
        ?string $url = null,
    ): ShopDto {
        $shop = \Shops\Domain\Models\Shop::find($id);
        if (! $shop) {
            throw new EntityNotFoundException();
        }
        $this->validateAndFill($shop, [
            'title' => $title,
            'url' => $url,
        ]);
        try {
            $shop->save();
        } catch (QueryException $exception) {
            throw new EntityNotUpdatedException($exception->getMessage());
        }

        return $shop->toDto();
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): void
    {
        $user = \Shops\Domain\Models\Shop::find($id);
        if (! $user) {
            throw new EntityNotFoundException();
        }

        $user->delete();
    }
}
