<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search, Eye, Ban, CheckCircle, Users } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface User {
    id: number;
    uid: string;
    name: string;
    email: string;
    phone: string | null;
    gender: string | null;
    profileUrl: string | null;
    status: string;
    accountStatus: string;
    totalCalls: number;
    totalDuration: string;
    rating: number;
    registered: string;
}

interface Props {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        search: string | null;
        id_search: string | null;
        status: string | null;
        sort: string;
        direction: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const idSearch = ref(props.filters.id_search || '');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/users' },
];

const applySearch = () => {
    router.get('/users', {
        search: search.value || undefined,
        id_search: idSearch.value || undefined
    }, { preserveState: true, replace: true });
};

const searchById = () => {
    router.get('/users', {
        id_search: idSearch.value || undefined
    }, { preserveState: true, replace: true });
};

const updateStatus = (uid: string, status: string) => {
    router.patch(`/users/${uid}/status`, { status }, { preserveScroll: true });
};

const getStatusBadge = (status: string) => {
    switch (status) {
        case 'ACTIVE':
            return 'bg-green-100 text-green-800';
        case 'BANNED':
            return 'bg-red-100 text-red-800';
        case 'SUSPENDED':
            return 'bg-yellow-100 text-yellow-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Users Management</CardTitle>
                        <div class="text-sm text-muted-foreground">
                            Total: {{ users.total }} users
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Search -->
                    <div class="mb-6 flex items-center gap-4">
                        <div class="relative flex-1 max-w-xs">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Search by name, email..."
                                class="pl-10"
                                @keyup.enter="applySearch"
                            />
                        </div>
                        <div class="relative max-w-[150px]">
                            <Input
                                v-model="idSearch"
                                placeholder="Search by ID"
                                type="number"
                                @keyup.enter="searchById"
                            />
                        </div>
                        <Button @click="applySearch">Search</Button>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="p-3 text-left text-sm font-medium">User</th>
                                    <th class="p-3 text-left text-sm font-medium">Contact</th>
                                    <th class="p-3 text-left text-sm font-medium">Stats</th>
                                    <th class="p-3 text-left text-sm font-medium">Rating</th>
                                    <th class="p-3 text-left text-sm font-medium">Status</th>
                                    <th class="p-3 text-left text-sm font-medium">Registered</th>
                                    <th class="p-3 text-left text-sm font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.uid" class="border-b">
                                    <td class="p-3">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="user.profileUrl"
                                                :src="user.profileUrl"
                                                :alt="user.name"
                                                class="h-10 w-10 rounded-full object-cover"
                                            />
                                            <div v-else class="h-10 w-10 rounded-full bg-muted flex items-center justify-center">
                                                <Users class="h-5 w-5 text-muted-foreground" />
                                            </div>
                                            <div>
                                                <p class="font-medium">{{ user.name }}</p>
                                                <p class="text-xs text-muted-foreground">ID: #{{ user.id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <p class="text-sm">{{ user.email }}</p>
                                        <p class="text-xs text-muted-foreground">{{ user.phone || 'N/A' }}</p>
                                    </td>
                                    <td class="p-3">
                                        <p class="text-sm">{{ user.totalCalls }} calls</p>
                                        <p class="text-xs text-muted-foreground">{{ user.totalDuration }}</p>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-1">
                                            <span class="text-yellow-500">★</span>
                                            <span class="text-sm">{{ user.rating }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusBadge(user.accountStatus)]">
                                            {{ user.accountStatus }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-sm text-muted-foreground">
                                        {{ user.registered }}
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <Link :href="`/users/${user.uid}`">
                                                <Button variant="ghost" size="icon">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                v-if="user.accountStatus === 'ACTIVE'"
                                                variant="ghost"
                                                size="icon"
                                                @click="updateStatus(user.uid, 'BANNED')"
                                            >
                                                <Ban class="h-4 w-4 text-red-500" />
                                            </Button>
                                            <Button
                                                v-else
                                                variant="ghost"
                                                size="icon"
                                                @click="updateStatus(user.uid, 'ACTIVE')"
                                            >
                                                <CheckCircle class="h-4 w-4 text-green-500" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="7" class="p-8 text-center text-muted-foreground">
                                        No users found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="users.last_page > 1" class="mt-4 flex items-center justify-center gap-2">
                        <template v-for="link in users.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="[
                                    'px-3 py-1 rounded text-sm',
                                    link.active ? 'bg-primary text-primary-foreground' : 'bg-muted hover:bg-muted/80'
                                ]"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="px-3 py-1 text-sm text-muted-foreground"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
