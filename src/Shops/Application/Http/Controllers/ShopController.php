<?php

namespace Shops\Application\Http\Controllers;

use Shops\Domain\Services\ShopService;
use Shops\Domain\Models\Shop;
use Shops\Contracts\DataTransferObjects\ShopDTO;
use App\Exceptions\EntityNotCreatedException;
use App\Exceptions\EntityNotDeletedException;
use App\Exceptions\EntityNotFoundException;
use App\Exceptions\EntityNotUpdatedException;
use App\Exceptions\EntityValidationException;
use App\Helpers\DomainModelController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ShopController extends Controller
{
    use DomainModelController;

    public function __construct(
        private ShopService $shopService
    ) {
    }

    /**
     * Список пользователей
     * GET /users
     */
    public function index(Request $request)
    {
        abort_if($request->user()->cannot('viewAny', User::class), 403);
        $paginatedShops = $this->shopService->list(
            role: $request->get('role'),
            searchQuery: $request->get('q'),
        );

        // Shop::query()->create(
        //     [
        //         'title' => 'Комус',
        //         'url' => 'https://comus.ru'
        //     ]
        //     );

        //$roleTitles = config('models.shops.roles', []); 

        return Inertia::render('Shops/Index', [
            'shops' => $this->outputPaginatedList($paginatedShops, fn(ShopDTO $shop) => [
                'id' => $shop->id,
                'title' => $shop->title,
                'url' => $shop->url,
                'created_at' => $shop->created_at,
            ]),
            'initialFilter' => $request->only(['role', 'q']),
        ]);
    }

    /**
     * Форма создания пользователя
     * GET /users/create
     */
    public function create(Request $request)
    {
        abort_if($request->user()->cannot('create', User::class), 403);

        return Inertia::render('Shops/Create', [
            'userRoles' => config('models.shops.roles', ['admin' => 'Админ']),
            'teamRoles' => config('models.shops.team_roles', ['admin']),
        ]);
    }

    /**
     * Сохранение созданного магазина
     * POST /shops
     */
    public function store(Request $request)
    {
        abort_if($request->user()->cannot('create', User::class), 403);
        try {
            $request->validate([
                'url' => 'required|url'
            ]);

            //dd($request);

            $this->shopService->create(
                title: $request->string('title'),
                url: $request->string('url'),
            );

            

        } catch (EntityValidationException $exception) {
            return back()->withErrors($exception->messages);
        } catch (EntityNotCreatedException $exception) {
            return back()->withErrors(['name' => 'Не удается создать магазин: ' . $exception->getMessage()]);
        }

        return Redirect::route('shops.index');
    }

    /**
     * Страница магазина
     * GET /shops/{id}
     */
    public function show(Request $request, int $shop)
    {
        // try {
        //     $shopDto = $this->shopService->getById($shop);
        //     abort_if($request->user()->cannot('view', $shopDto), 403);
        // } catch (EntityNotFoundException $exception) {
        //     abort(Response::HTTP_NOT_FOUND);
        // }

        // return Inertia::render('Shops/Show', [
        //     'user' => array_merge((array)$userDto, [
        //         'role_title' => config('models.shops.roles.' . $shopDto->role, $userDto->role),
        //     ]),
        // ]);
    }

    /**
     * Форма редактирования магазина
     * GET /shops/{id}/edit
     */
    public function edit(Request $request, int $shop)
    {
        try {
            $shopDto = $this->shopService->getById($shop);
            abort_if($request->user()->cannot('update', $shopDto), 403);
        } catch (EntityNotFoundException $exception) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return Inertia::render('Shops/Edit', [
            'id' => $shopDto->id,
            'values' => $shopDto,
            'userRoles' => config('models.users.roles', ['admin' => 'Админ']),
            'teamRoles' => config('models.users.team_roles', ['admin']),
        ]);
    }

    /**
     * Сохранение редактируемого магазина
     * PUT /users/{id}
     */
    public function update(Request $request, int $shop)
    {
        try {
            $shopDto = $this->shopService->getById($shop);
            abort_if($request->user()->cannot('update', $shopDto), 403);
            $this->shopService->update(
                id: $shopDto->id,
                title: $request->stringOrNull('title'),
                url: $request->stringOrNull('url')
            );
        } catch (EntityNotFoundException $exception) {
            abort(Response::HTTP_NOT_FOUND);
        } catch (EntityValidationException $exception) {
            return back()->withErrors($exception->messages);
        } catch (EntityNotUpdatedException $exception) {
            return back()->withErrors(['url' => 'Не удается отредактировать магазин: ' . $exception->message]);
        }

        return Redirect::route('shops.index');
    }

    /**
     * Удаление магазина
     * DELETE /shops/{id}
     */
    public function destroy(Request $request, int $shop)
    {
        try {
            $shopDto = $this->shopService->getById($shop);
            abort_if($request->user()->cannot('delete', $shopDto), 403);
            $this->shopService->delete($shopDto->id);
        } catch (EntityNotFoundException $exception) {
            abort(Response::HTTP_NOT_FOUND);
        } catch (EntityNotDeletedException $exception) {
            return back()->withErrors(['id' => 'Не удается удалить магазин: ' . $exception->message]);
        }

        return Redirect::back(303);
    }
}
