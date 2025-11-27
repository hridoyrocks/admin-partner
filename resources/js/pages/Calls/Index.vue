<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search, Phone, Clock, Users } from 'lucide-vue-next';
import { ref } from 'vue';

interface Call {
    callId: string;
    callerName: string;
    callerId: string;
    callerProfile: string | null;
    receiverName: string;
    receiverId: string;
    receiverProfile: string | null;
    duration: number;
    formattedDuration: string;
    callTime: string;
    timestamp: number;
}

interface Props {
    calls: {
        data: Call[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        search: string | null;
        date_from: string | null;
        date_to: string | null;
        min_duration: number | null;
    };
    stats: {
        totalCalls: number;
        totalDuration: string;
        avgDuration: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Call Logs', href: '/calls' },
];

const applyFilters = () => {
    router.get('/calls', {
        search: search.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    }, { preserveState: true, replace: true });
};

const clearFilters = () => {
    search.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    router.get('/calls');
};
</script>

<template>
    <Head title="Call Logs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Calls</CardTitle>
                        <Phone class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalCalls.toLocaleString() }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Duration</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalDuration }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Average Duration</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.avgDuration }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Calls Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Call Logs</CardTitle>
                </CardHeader>
                <CardContent>
                    <!-- Filters -->
                    <div class="mb-6 flex flex-wrap items-center gap-4">
                        <div class="relative flex-1 min-w-[200px] max-w-sm">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Search by name or ID..."
                                class="pl-10"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-muted-foreground">From:</span>
                            <Input
                                v-model="dateFrom"
                                type="date"
                                class="w-40"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-muted-foreground">To:</span>
                            <Input
                                v-model="dateTo"
                                type="date"
                                class="w-40"
                            />
                        </div>
                        <Button @click="applyFilters">Apply</Button>
                        <Button variant="outline" @click="clearFilters">Clear</Button>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="p-3 text-left text-sm font-medium">Caller</th>
                                    <th class="p-3 text-left text-sm font-medium">Receiver</th>
                                    <th class="p-3 text-left text-sm font-medium">Duration</th>
                                    <th class="p-3 text-left text-sm font-medium">Call Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="call in calls.data" :key="call.callId" class="border-b">
                                    <td class="p-3">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="call.callerProfile"
                                                :src="call.callerProfile"
                                                :alt="call.callerName"
                                                class="h-8 w-8 rounded-full object-cover"
                                            />
                                            <div v-else class="h-8 w-8 rounded-full bg-muted flex items-center justify-center">
                                                <Users class="h-4 w-4 text-muted-foreground" />
                                            </div>
                                            <div>
                                                <Link :href="`/users/${call.callerId}`" class="font-medium hover:underline">
                                                    {{ call.callerName }}
                                                </Link>
                                                <p class="text-xs text-muted-foreground">{{ call.callerId }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="call.receiverProfile"
                                                :src="call.receiverProfile"
                                                :alt="call.receiverName"
                                                class="h-8 w-8 rounded-full object-cover"
                                            />
                                            <div v-else class="h-8 w-8 rounded-full bg-muted flex items-center justify-center">
                                                <Users class="h-4 w-4 text-muted-foreground" />
                                            </div>
                                            <div>
                                                <Link :href="`/users/${call.receiverId}`" class="font-medium hover:underline">
                                                    {{ call.receiverName }}
                                                </Link>
                                                <p class="text-xs text-muted-foreground">{{ call.receiverId }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <span class="font-medium">{{ call.formattedDuration }}</span>
                                    </td>
                                    <td class="p-3 text-muted-foreground">
                                        {{ call.callTime }}
                                    </td>
                                </tr>
                                <tr v-if="calls.data.length === 0">
                                    <td colspan="4" class="p-8 text-center text-muted-foreground">
                                        No calls found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="calls.last_page > 1" class="mt-4 flex items-center justify-center gap-2">
                        <template v-for="link in calls.links" :key="link.label">
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
