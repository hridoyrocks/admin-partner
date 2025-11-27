<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    Users,
    Phone,
    Clock,
    TrendingUp,
    TrendingDown,
    UserPlus,
    UserCheck,
    UserX,
    Wifi,
    Flag,
    MessageCircle,
    Image,
    ArrowUpRight,
    ArrowDownRight,
    Activity,
    BarChart3,
    Timer,
    AlertTriangle,
    CheckCircle2,
    Eye
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    stats: {
        totalUsers: number;
        activeUsers: number;
        bannedUsers: number;
        onlineUsers: number;
        newUsersToday: number;
        newUsersYesterday: number;
        newUsersThisWeek: number;
        newUsersLastWeek: number;
        newUsersThisMonth: number;
        totalCalls: number;
        totalDuration: string;
        totalDurationSeconds: number;
        todayCalls: number;
        todayDuration: string;
        yesterdayCalls: number;
        weekCalls: number;
        weekDuration: string;
        lastWeekCalls: number;
        monthCalls: number;
        monthDuration: string;
        avgCallDuration: string;
        totalReports: number;
        pendingReports: number;
        resolvedReports: number;
        reportsToday: number;
        totalTopics: number;
        totalBanners: number;
        activeBanners: number;
        maleUsers: number;
        femaleUsers: number;
        otherGender: number;
    };
    recentCalls: Array<{
        id: string;
        caller: string;
        callerProfile: string;
        receiver: string;
        receiverProfile: string;
        duration: string;
        time: string;
    }>;
    topPerformers: Array<{
        rank: number;
        userId: string;
        name: string;
        profileUrl: string;
        duration: string;
        calls: number;
    }>;
    recentReports: Array<{
        id: number;
        userName: string;
        userProfile: string;
        reporterName: string;
        reason: string;
        status: string;
        time: string;
    }>;
    callsByDay: Array<{
        day: string;
        date: string;
        calls: number;
        duration: number;
    }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

// Calculate growth percentages
const userGrowth = computed(() => {
    if (props.stats.newUsersYesterday === 0) return props.stats.newUsersToday > 0 ? 100 : 0;
    return Math.round(((props.stats.newUsersToday - props.stats.newUsersYesterday) / props.stats.newUsersYesterday) * 100);
});

const callGrowth = computed(() => {
    if (props.stats.yesterdayCalls === 0) return props.stats.todayCalls > 0 ? 100 : 0;
    return Math.round(((props.stats.todayCalls - props.stats.yesterdayCalls) / props.stats.yesterdayCalls) * 100);
});

const weeklyGrowth = computed(() => {
    if (props.stats.lastWeekCalls === 0) return props.stats.weekCalls > 0 ? 100 : 0;
    return Math.round(((props.stats.weekCalls - props.stats.lastWeekCalls) / props.stats.lastWeekCalls) * 100);
});

// Max value for chart scaling
const maxCallsInDay = computed(() => Math.max(...props.callsByDay.map(d => d.calls), 1));

// Rank badge colors
const getRankBadge = (rank: number) => {
    switch(rank) {
        case 1: return 'bg-gradient-to-r from-yellow-400 to-yellow-600 text-white';
        case 2: return 'bg-gradient-to-r from-gray-300 to-gray-500 text-white';
        case 3: return 'bg-gradient-to-r from-amber-600 to-amber-800 text-white';
        default: return 'bg-muted text-muted-foreground';
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Welcome Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
                    <p class="text-muted-foreground">Welcome back! Here's what's happening with your app.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 rounded-full bg-green-100 px-3 py-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-sm font-medium text-green-700">{{ stats.onlineUsers }} Online</span>
                    </div>
                </div>
            </div>

            <!-- Main Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Users -->
                <Card class="relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-4 transform rounded-full bg-blue-500/10"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Total Users</CardTitle>
                        <div class="rounded-lg bg-blue-100 p-2">
                            <Users class="h-4 w-4 text-blue-600" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ stats.totalUsers.toLocaleString() }}</div>
                        <div class="mt-2 flex items-center gap-2 text-sm">
                            <span class="flex items-center text-green-600">
                                <UserCheck class="mr-1 h-3 w-3" />
                                {{ stats.activeUsers }}
                            </span>
                            <span class="text-muted-foreground">active</span>
                            <span class="text-muted-foreground">|</span>
                            <span class="flex items-center text-red-600">
                                <UserX class="mr-1 h-3 w-3" />
                                {{ stats.bannedUsers }}
                            </span>
                            <span class="text-muted-foreground">banned</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Today's Calls -->
                <Card class="relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-4 transform rounded-full bg-green-500/10"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Today's Calls</CardTitle>
                        <div class="rounded-lg bg-green-100 p-2">
                            <Phone class="h-4 w-4 text-green-600" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ stats.todayCalls.toLocaleString() }}</div>
                        <div class="mt-2 flex items-center gap-2 text-sm">
                            <span :class="[
                                'flex items-center',
                                callGrowth >= 0 ? 'text-green-600' : 'text-red-600'
                            ]">
                                <ArrowUpRight v-if="callGrowth >= 0" class="h-3 w-3" />
                                <ArrowDownRight v-else class="h-3 w-3" />
                                {{ Math.abs(callGrowth) }}%
                            </span>
                            <span class="text-muted-foreground">vs yesterday ({{ stats.yesterdayCalls }})</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Total Duration -->
                <Card class="relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-4 transform rounded-full bg-purple-500/10"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Total Duration</CardTitle>
                        <div class="rounded-lg bg-purple-100 p-2">
                            <Clock class="h-4 w-4 text-purple-600" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ stats.totalDuration }}</div>
                        <div class="mt-2 flex items-center gap-2 text-sm">
                            <Timer class="h-3 w-3 text-muted-foreground" />
                            <span class="text-muted-foreground">Avg: {{ stats.avgCallDuration }} per call</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- New Users -->
                <Card class="relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-4 transform rounded-full bg-orange-500/10"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">New Users Today</CardTitle>
                        <div class="rounded-lg bg-orange-100 p-2">
                            <UserPlus class="h-4 w-4 text-orange-600" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ stats.newUsersToday }}</div>
                        <div class="mt-2 flex items-center gap-2 text-sm">
                            <span :class="[
                                'flex items-center',
                                userGrowth >= 0 ? 'text-green-600' : 'text-red-600'
                            ]">
                                <ArrowUpRight v-if="userGrowth >= 0" class="h-3 w-3" />
                                <ArrowDownRight v-else class="h-3 w-3" />
                                {{ Math.abs(userGrowth) }}%
                            </span>
                            <span class="text-muted-foreground">vs yesterday</span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Secondary Stats Row -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Weekly Calls -->
                <Card>
                    <CardContent class="pt-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground">This Week</p>
                                <p class="text-2xl font-bold">{{ stats.weekCalls.toLocaleString() }}</p>
                                <p class="text-xs text-muted-foreground">{{ stats.weekDuration }} duration</p>
                            </div>
                            <div :class="[
                                'flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium',
                                weeklyGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                            ]">
                                <TrendingUp v-if="weeklyGrowth >= 0" class="h-3 w-3" />
                                <TrendingDown v-else class="h-3 w-3" />
                                {{ Math.abs(weeklyGrowth) }}%
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Monthly Calls -->
                <Card>
                    <CardContent class="pt-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground">This Month</p>
                                <p class="text-2xl font-bold">{{ stats.monthCalls.toLocaleString() }}</p>
                                <p class="text-xs text-muted-foreground">{{ stats.monthDuration }} duration</p>
                            </div>
                            <BarChart3 class="h-8 w-8 text-muted-foreground/50" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Pending Reports -->
                <Card :class="stats.pendingReports > 0 ? 'border-orange-200 bg-orange-50/50' : ''">
                    <CardContent class="pt-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground">Pending Reports</p>
                                <p class="text-2xl font-bold">{{ stats.pendingReports }}</p>
                                <p class="text-xs text-muted-foreground">{{ stats.reportsToday }} new today</p>
                            </div>
                            <div :class="[
                                'rounded-full p-2',
                                stats.pendingReports > 0 ? 'bg-orange-100' : 'bg-muted'
                            ]">
                                <AlertTriangle :class="[
                                    'h-5 w-5',
                                    stats.pendingReports > 0 ? 'text-orange-600' : 'text-muted-foreground'
                                ]" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Total Calls -->
                <Card>
                    <CardContent class="pt-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground">Total Calls</p>
                                <p class="text-2xl font-bold">{{ stats.totalCalls.toLocaleString() }}</p>
                                <p class="text-xs text-muted-foreground">All time</p>
                            </div>
                            <Activity class="h-8 w-8 text-muted-foreground/50" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Charts & Activity Row -->
            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Weekly Call Chart -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Call Activity (Last 7 Days)</CardTitle>
                            <div class="flex items-center gap-4 text-sm">
                                <div class="flex items-center gap-1">
                                    <div class="h-3 w-3 rounded-full bg-primary"></div>
                                    <span class="text-muted-foreground">Calls</span>
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="flex h-[200px] items-end gap-2">
                            <div
                                v-for="day in callsByDay"
                                :key="day.date"
                                class="flex flex-1 flex-col items-center gap-2"
                            >
                                <div class="relative w-full flex-1">
                                    <div
                                        class="absolute bottom-0 w-full rounded-t-md bg-primary transition-all hover:bg-primary/80"
                                        :style="{ height: `${(day.calls / maxCallsInDay) * 100}%`, minHeight: day.calls > 0 ? '8px' : '0' }"
                                    >
                                        <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-medium opacity-0 transition-opacity group-hover:opacity-100">
                                            {{ day.calls }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs font-medium">{{ day.day }}</p>
                                    <p class="text-[10px] text-muted-foreground">{{ day.calls }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Quick Stats -->
                <Card>
                    <CardHeader>
                        <CardTitle>Quick Stats</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <Link href="/topics" class="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-muted/50">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-blue-100 p-2">
                                    <MessageCircle class="h-4 w-4 text-blue-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Call Topics</p>
                                    <p class="text-xs text-muted-foreground">Active topics</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold">{{ stats.totalTopics }}</span>
                        </Link>

                        <Link href="/banners" class="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-muted/50">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-purple-100 p-2">
                                    <Image class="h-4 w-4 text-purple-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Banners</p>
                                    <p class="text-xs text-muted-foreground">{{ stats.activeBanners }} active</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold">{{ stats.totalBanners }}</span>
                        </Link>

                        <Link href="/reports" class="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-muted/50">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-red-100 p-2">
                                    <Flag class="h-4 w-4 text-red-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Reports</p>
                                    <p class="text-xs text-muted-foreground">{{ stats.resolvedReports }} resolved</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold">{{ stats.totalReports }}</span>
                        </Link>

                        <div class="flex items-center justify-between rounded-lg border p-3">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-green-100 p-2">
                                    <UserPlus class="h-4 w-4 text-green-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">This Month</p>
                                    <p class="text-xs text-muted-foreground">New signups</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold">{{ stats.newUsersThisMonth }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Bottom Row -->
            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Recent Calls -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle>Recent Calls</CardTitle>
                        <Link href="/calls">
                            <Button variant="ghost" size="sm">
                                View all
                                <ArrowUpRight class="ml-1 h-3 w-3" />
                            </Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-for="call in recentCalls"
                                :key="call.id"
                                class="flex items-center gap-3 rounded-lg border p-2"
                            >
                                <div class="flex -space-x-2">
                                    <img
                                        v-if="call.callerProfile"
                                        :src="call.callerProfile"
                                        class="h-8 w-8 rounded-full border-2 border-background object-cover"
                                    />
                                    <div v-else class="h-8 w-8 rounded-full border-2 border-background bg-muted flex items-center justify-center">
                                        <Users class="h-4 w-4 text-muted-foreground" />
                                    </div>
                                    <img
                                        v-if="call.receiverProfile"
                                        :src="call.receiverProfile"
                                        class="h-8 w-8 rounded-full border-2 border-background object-cover"
                                    />
                                    <div v-else class="h-8 w-8 rounded-full border-2 border-background bg-muted flex items-center justify-center">
                                        <Users class="h-4 w-4 text-muted-foreground" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ call.caller }}</p>
                                    <p class="text-xs text-muted-foreground truncate">to {{ call.receiver }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium">{{ call.duration }}</p>
                                    <p class="text-xs text-muted-foreground">{{ call.time }}</p>
                                </div>
                            </div>
                            <p v-if="recentCalls.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                                No recent calls
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Top Performers -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle>Top Performers</CardTitle>
                        <Link href="/leaderboard">
                            <Button variant="ghost" size="sm">
                                Leaderboard
                                <ArrowUpRight class="ml-1 h-3 w-3" />
                            </Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-for="performer in topPerformers"
                                :key="performer.userId"
                                class="flex items-center gap-3"
                            >
                                <div :class="[
                                    'flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold',
                                    getRankBadge(performer.rank)
                                ]">
                                    {{ performer.rank }}
                                </div>
                                <img
                                    v-if="performer.profileUrl"
                                    :src="performer.profileUrl"
                                    :alt="performer.name"
                                    class="h-10 w-10 rounded-full object-cover"
                                />
                                <div v-else class="h-10 w-10 rounded-full bg-muted flex items-center justify-center">
                                    <Users class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ performer.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ performer.calls }} calls</p>
                                </div>
                                <span class="text-sm font-semibold text-primary">{{ performer.duration }}</span>
                            </div>
                            <p v-if="topPerformers.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                                No data available
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Reports -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle>Recent Reports</CardTitle>
                        <Link href="/reports">
                            <Button variant="ghost" size="sm">
                                View all
                                <ArrowUpRight class="ml-1 h-3 w-3" />
                            </Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-for="report in recentReports"
                                :key="report.id"
                                class="flex items-center gap-3 rounded-lg border p-2"
                            >
                                <img
                                    v-if="report.userProfile"
                                    :src="report.userProfile"
                                    class="h-10 w-10 rounded-full object-cover"
                                />
                                <div v-else class="h-10 w-10 rounded-full bg-muted flex items-center justify-center">
                                    <Users class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ report.userName }}</p>
                                    <p class="text-xs text-muted-foreground truncate">{{ report.reason }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span :class="[
                                        'rounded-full px-2 py-0.5 text-xs font-medium',
                                        report.status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700'
                                    ]">
                                        {{ report.status }}
                                    </span>
                                    <span class="text-xs text-muted-foreground">{{ report.time }}</span>
                                </div>
                            </div>
                            <p v-if="recentReports.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                                No reports
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
