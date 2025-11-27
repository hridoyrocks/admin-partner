<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search, Eye, CheckCircle, Users, AlertTriangle } from 'lucide-vue-next';
import { ref } from 'vue';

interface User {
    id: number;
    uid: string;
    name: string;
    email: string;
    phone: string | null;
    gender: string | null;
    profileUrl: string | null;
    accountStatus: string;
    banReason: string | null;
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
        sort: string;
        direction: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Banned Users', href: '/users/banned' },
];

const applySearch = () => {
    router.get('/users/banned', {
        search: search.value || undefined,
    }, { preserveState: true, replace: true });
};

const unbanUser = (uid: string) => {
    router.patch(`/users/${uid}/status`, { status: 'ACTIVE' }, { preserveScroll: true });
};
</script>

<template>
    <Head title="Banned Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-100 rounded-full">
                                <AlertTriangle class="h-5 w-5 text-red-600" />
                            </div>
                            <CardTitle>Banned Users</CardTitle>
                        </div>
                        <div class="text-sm text-muted-foreground">
                            Total: {{ users.total }} banned users
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Search -->
                    <div class="mb-6 flex items-center gap-4">
                        <div class="relative flex-1 max-w-md">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Search by name, email, phone..."
                                class="pl-10"
                                @keyup.enter="applySearch"
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
                                    <th class="p-3 text-left text-sm font-medium">Ban Reason</th>
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
                                    <td class="p-3 max-w-xs">
                                        <p v-if="user.banReason" class="text-sm text-red-600 line-clamp-2">
                                            {{ user.banReason }}
                                        </p>
                                        <p v-else class="text-sm text-muted-foreground italic">
                                            No reason provided
                                        </p>
                                    </td>
                                    <td class="p-3 text-sm text-muted-foreground">
                                        {{ user.registered }}
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <Link :href="`/users/${user.uid}`">
                                                <Button variant="ghost" size="icon" title="View Profile">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                class="text-green-600 border-green-600 hover:bg-green-50"
                                                @click="unbanUser(user.uid)"
                                            >
                                                <CheckCircle class="h-4 w-4 mr-1" />
                                                Unban
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="5" class="p-8 text-center text-muted-foreground">
                                        No banned users found
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
