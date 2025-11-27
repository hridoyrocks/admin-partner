import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:12
* @route '/users'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:12
* @route '/users'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:12
* @route '/users'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:12
* @route '/users'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:12
* @route '/users'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:12
* @route '/users'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::index
* @see app/Http/Controllers/UserController.php:12
* @route '/users'
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
* @see \App\Http\Controllers\UserController::banned
* @see app/Http/Controllers/UserController.php:76
* @route '/users/banned'
*/
export const banned = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: banned.url(options),
    method: 'get',
})

banned.definition = {
    methods: ["get","head"],
    url: '/users/banned',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::banned
* @see app/Http/Controllers/UserController.php:76
* @route '/users/banned'
*/
banned.url = (options?: RouteQueryOptions) => {
    return banned.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::banned
* @see app/Http/Controllers/UserController.php:76
* @route '/users/banned'
*/
banned.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: banned.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::banned
* @see app/Http/Controllers/UserController.php:76
* @route '/users/banned'
*/
banned.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: banned.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::banned
* @see app/Http/Controllers/UserController.php:76
* @route '/users/banned'
*/
const bannedForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: banned.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::banned
* @see app/Http/Controllers/UserController.php:76
* @route '/users/banned'
*/
bannedForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: banned.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::banned
* @see app/Http/Controllers/UserController.php:76
* @route '/users/banned'
*/
bannedForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: banned.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

banned.form = bannedForm

/**
* @see \App\Http\Controllers\UserController::show
* @see app/Http/Controllers/UserController.php:124
* @route '/users/{uid}'
*/
export const show = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/users/{uid}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::show
* @see app/Http/Controllers/UserController.php:124
* @route '/users/{uid}'
*/
show.url = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { uid: args }
    }

    if (Array.isArray(args)) {
        args = {
            uid: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        uid: args.uid,
    }

    return show.definition.url
            .replace('{uid}', parsedArgs.uid.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::show
* @see app/Http/Controllers/UserController.php:124
* @route '/users/{uid}'
*/
show.get = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::show
* @see app/Http/Controllers/UserController.php:124
* @route '/users/{uid}'
*/
show.head = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::show
* @see app/Http/Controllers/UserController.php:124
* @route '/users/{uid}'
*/
const showForm = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::show
* @see app/Http/Controllers/UserController.php:124
* @route '/users/{uid}'
*/
showForm.get = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::show
* @see app/Http/Controllers/UserController.php:124
* @route '/users/{uid}'
*/
showForm.head = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\UserController::updateStatus
* @see app/Http/Controllers/UserController.php:154
* @route '/users/{uid}/status'
*/
export const updateStatus = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

updateStatus.definition = {
    methods: ["patch"],
    url: '/users/{uid}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\UserController::updateStatus
* @see app/Http/Controllers/UserController.php:154
* @route '/users/{uid}/status'
*/
updateStatus.url = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { uid: args }
    }

    if (Array.isArray(args)) {
        args = {
            uid: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        uid: args.uid,
    }

    return updateStatus.definition.url
            .replace('{uid}', parsedArgs.uid.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::updateStatus
* @see app/Http/Controllers/UserController.php:154
* @route '/users/{uid}/status'
*/
updateStatus.patch = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\UserController::updateStatus
* @see app/Http/Controllers/UserController.php:154
* @route '/users/{uid}/status'
*/
const updateStatusForm = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserController::updateStatus
* @see app/Http/Controllers/UserController.php:154
* @route '/users/{uid}/status'
*/
updateStatusForm.patch = (args: { uid: string | number } | [uid: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateStatus.form = updateStatusForm

const users = {
    index: Object.assign(index, index),
    banned: Object.assign(banned, banned),
    show: Object.assign(show, show),
    updateStatus: Object.assign(updateStatus, updateStatus),
}

export default users