import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ConfigController::index
* @see app/Http/Controllers/ConfigController.php:11
* @route '/config'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/config',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ConfigController::index
* @see app/Http/Controllers/ConfigController.php:11
* @route '/config'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConfigController::index
* @see app/Http/Controllers/ConfigController.php:11
* @route '/config'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConfigController::index
* @see app/Http/Controllers/ConfigController.php:11
* @route '/config'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ConfigController::index
* @see app/Http/Controllers/ConfigController.php:11
* @route '/config'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConfigController::index
* @see app/Http/Controllers/ConfigController.php:11
* @route '/config'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConfigController::index
* @see app/Http/Controllers/ConfigController.php:11
* @route '/config'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\ConfigController::update
* @see app/Http/Controllers/ConfigController.php:20
* @route '/config'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/config',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\ConfigController::update
* @see app/Http/Controllers/ConfigController.php:20
* @route '/config'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConfigController::update
* @see app/Http/Controllers/ConfigController.php:20
* @route '/config'
*/
update.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\ConfigController::update
* @see app/Http/Controllers/ConfigController.php:20
* @route '/config'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ConfigController::update
* @see app/Http/Controllers/ConfigController.php:20
* @route '/config'
*/
updateForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\ConfigController::updateTurn
* @see app/Http/Controllers/ConfigController.php:54
* @route '/config/turn'
*/
export const updateTurn = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateTurn.url(options),
    method: 'patch',
})

updateTurn.definition = {
    methods: ["patch"],
    url: '/config/turn',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\ConfigController::updateTurn
* @see app/Http/Controllers/ConfigController.php:54
* @route '/config/turn'
*/
updateTurn.url = (options?: RouteQueryOptions) => {
    return updateTurn.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConfigController::updateTurn
* @see app/Http/Controllers/ConfigController.php:54
* @route '/config/turn'
*/
updateTurn.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateTurn.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\ConfigController::updateTurn
* @see app/Http/Controllers/ConfigController.php:54
* @route '/config/turn'
*/
const updateTurnForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateTurn.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ConfigController::updateTurn
* @see app/Http/Controllers/ConfigController.php:54
* @route '/config/turn'
*/
updateTurnForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateTurn.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateTurn.form = updateTurnForm

const config = {
    index: Object.assign(index, index),
    update: Object.assign(update, update),
    updateTurn: Object.assign(updateTurn, updateTurn),
}

export default config