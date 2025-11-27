<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search, Flag, Ban, AlertTriangle, CheckCircle, X, Users } from 'lucide-vue-next';
import { ref } from 'vue';

interface Report {
    id: number;
    userId: string;
    userName: string | null;
    userProfile: string | null;
    userDbId: number | null;
    userPhone: string | null;
    userEmail: string | null;
    reporterId: string;
    reporterName: string | null;
    reporterDbId: number | null;
    reporterPhone: string | null;
    reporterEmail: string | null;
    callId: string | null;
    reason: string;
    description: string | null;
    status: 'pending' | 'resolved' | 'dismissed';
    actionTaken: string | null;
    resolvedBy: string | null;
    time: number | null;
    created_at: string;
}

interface Props {
    reports: {
        data: Report[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        status: string;
        search: string | null;
    };
    stats: {
        pending: number;
        resolved: number;
        dismissed: number;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Reports', href: '/reports' },
];

const changeStatus = (status: string) => {
    router.get('/reports', { status, search: search.value || undefined }, { preserveState: true, replace: true });
};

const applySearch = () => {
    router.get('/reports', { status: props.filters.status, search: search.value || undefined }, { preserveState: true, replace: true });
};

const resolveReport = (id: number, action: string) => {
    router.patch(`/reports/${id}/resolve`, { action }, { preserveScroll: true });
};

const formatDate = (timestamp: number | null, dateStr: string) => {
    if (timestamp) {
        return new Date(timestamp * 1000).toLocaleString();
    }
    return new Date(dateStr).toLocaleString();
};

const getStatusBadge = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'resolved':
            return 'bg-green-100 text-green-800';
        case 'dismissed':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="Reports" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card
                    class="cursor-pointer transition-colors hover:bg-muted/50"
                    :class="{ 'ring-2 ring-primary': filters.status === 'pending' }"
                    @click="changeStatus('pending')"
                >
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending</CardTitle>
                        <AlertTriangle class="h-4 w-4 text-yellow-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.pending }}</div>
                    </CardContent>
                </Card>

                <Card
                    class="cursor-pointer transition-colors hover:bg-muted/50"
                    :class="{ 'ring-2 ring-primary': filters.status === 'resolved' }"
                    @click="changeStatus('resolved')"
                >
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Resolved</CardTitle>
                        <CheckCircle class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.resolved }}</div>
                    </CardContent>
                </Card>

                <Card
                    class="cursor-pointer transition-colors hover:bg-muted/50"
                    :class="{ 'ring-2 ring-primary': filters.status === 'dismissed' }"
                    @click="changeStatus('dismissed')"
                >
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Dismissed</CardTitle>
                        <X class="h-4 w-4 text-gray-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.dismissed }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Reports Table -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Flag class="h-5 w-5 text-red-500" />
                            <CardTitle>User Reports</CardTitle>
                        </div>
                        <Button variant="outline" size="sm" @click="changeStatus('all')">
                            View All
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Search -->
                    <div class="mb-6 flex items-center gap-4">
                        <div class="relative flex-1 max-w-sm">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Search reports..."
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
                                    <th class="p-3 text-left text-sm font-medium">Reported User</th>
                                    <th class="p-3 text-left text-sm font-medium">Reporter</th>
                                    <th class="p-3 text-left text-sm font-medium">Reason</th>
                                    <th class="p-3 text-left text-sm font-medium">Status</th>
                                    <th class="p-3 text-left text-sm font-medium">Date</th>
                                    <th class="p-3 text-left text-sm font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="report in reports.data" :key="report.id" class="border-b">
                                    <td class="p-3">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="report.userProfile"
                                                :src="report.userProfile"
                                                :alt="report.userName || 'User'"
                                                class="h-8 w-8 rounded-full object-cover"
                                            />
                                            <div v-else class="h-8 w-8 rounded-full bg-muted flex items-center justify-center">
                                                <Users class="h-4 w-4 text-muted-foreground" />
                                            </div>
                                            <div>
                                                <Link :href="`/users/${report.userId}`" class="font-medium hover:underline">
                                                    {{ report.userName || 'Unknown' }}
                                                </Link>
                                                <p class="text-xs text-muted-foreground">ID: #{{ report.userDbId }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <p class="text-sm">{{ report.reporterName || 'Unknown' }}</p>
                                        <p class="text-xs text-muted-foreground">ID: #{{ report.reporterDbId }}</p>
                                    </td>
                                    <td class="p-3">
                                        <p class="text-sm font-medium">{{ report.reason }}</p>
                                        <p v-if="report.description" class="text-xs text-muted-foreground truncate max-w-[200px]">
                                            {{ report.description }}
                                        </p>
                                    </td>
                                    <td class="p-3">
                                        <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusBadge(report.status)]">
                                            {{ report.status }}
                                        </span>
                                        <p v-if="report.actionTaken" class="text-xs text-muted-foreground mt-1">
                                            {{ report.actionTaken }}
                                        </p>
                                    </td>
                                    <td class="p-3 text-sm text-muted-foreground">
                                        {{ formatDate(report.time, report.created_at) }}
                                    </td>
                                    <td class="p-3">
                                        <div v-if="report.status === 'pending'" class="flex items-center gap-1">
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                                @click="resolveReport(report.id, 'ban')"
                                                title="Ban User"
                                            >
                                                <Ban class="h-4 w-4" />
                                            </Button>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                @click="resolveReport(report.id, 'warn')"
                                                title="Issue Warning"
                                            >
                                                <AlertTriangle class="h-4 w-4" />
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="resolveReport(report.id, 'dismiss')"
                                                title="Dismiss"
                                            >
                                                <X class="h-4 w-4" />
                                            </Button>
                                        </div>
                                        <span v-else class="text-xs text-muted-foreground">
                                            by {{ report.resolvedBy || 'System' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="reports.data.length === 0">
                                    <td colspan="6" class="p-8 text-center text-muted-foreground">
                                        No reports found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="reports.last_page > 1" class="mt-4 flex items-center justify-center gap-2">
                        <template v-for="link in reports.links" :key="link.label">
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
