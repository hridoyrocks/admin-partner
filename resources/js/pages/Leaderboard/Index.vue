<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Trophy, Users, Phone, Clock } from 'lucide-vue-next';

interface Rank {
    position: number;
    userId: string;
    name: string;
    profileUrl: string | null;
    duration: number;
    formattedDuration: string;
    calls: number;
}

interface Props {
    ranks: {
        data: Rank[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    period: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Leaderboard', href: '/leaderboard' },
];

const changePeriod = (period: string) => {
    router.get('/leaderboard', { period }, { preserveState: true, replace: true });
};

const getRankBadge = (position: number) => {
    if (position === 1) return 'bg-yellow-100 text-yellow-800 border-yellow-300';
    if (position === 2) return 'bg-gray-100 text-gray-800 border-gray-300';
    if (position === 3) return 'bg-orange-100 text-orange-800 border-orange-300';
    return 'bg-muted text-muted-foreground border-muted';
};

const getRankEmoji = (position: number) => {
    if (position === 1) return '🥇';
    if (position === 2) return '🥈';
    if (position === 3) return '🥉';
    return position.toString();
};
</script>

<template>
    <Head title="Leaderboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Trophy class="h-5 w-5 text-yellow-500" />
                            <CardTitle>Leaderboard</CardTitle>
                        </div>
                        <div class="flex gap-2">
                            <Button
                                :variant="period === 'weekly' ? 'default' : 'outline'"
                                size="sm"
                                @click="changePeriod('weekly')"
                            >
                                Weekly
                            </Button>
                            <Button
                                :variant="period === 'monthly' ? 'default' : 'outline'"
                                size="sm"
                                @click="changePeriod('monthly')"
                            >
                                Monthly
                            </Button>
                            <Button
                                :variant="period === 'lifetime' ? 'default' : 'outline'"
                                size="sm"
                                @click="changePeriod('lifetime')"
                            >
                                All Time
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Leaderboard Table -->
                    <div class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="p-3 text-center text-sm font-medium w-20">Rank</th>
                                    <th class="p-3 text-left text-sm font-medium">User</th>
                                    <th class="p-3 text-center text-sm font-medium">Calls</th>
                                    <th class="p-3 text-center text-sm font-medium">Duration</th>
                                    <th class="p-3 text-center text-sm font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="rank in ranks.data"
                                    :key="rank.userId"
                                    :class="[
                                        'border-b',
                                        rank.position <= 3 ? 'bg-muted/30' : ''
                                    ]"
                                >
                                    <td class="p-3 text-center">
                                        <span
                                            :class="[
                                                'inline-flex h-8 w-8 items-center justify-center rounded-full border text-sm font-bold',
                                                getRankBadge(rank.position)
                                            ]"
                                        >
                                            {{ getRankEmoji(rank.position) }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="rank.profileUrl"
                                                :src="rank.profileUrl"
                                                :alt="rank.name"
                                                class="h-10 w-10 rounded-full object-cover"
                                            />
                                            <div v-else class="h-10 w-10 rounded-full bg-muted flex items-center justify-center">
                                                <Users class="h-5 w-5 text-muted-foreground" />
                                            </div>
                                            <div>
                                                <p class="font-medium">{{ rank.name }}</p>
                                                <p class="text-xs text-muted-foreground">{{ rank.userId }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <Phone class="h-4 w-4 text-muted-foreground" />
                                            <span class="font-medium">{{ rank.calls }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <Clock class="h-4 w-4 text-muted-foreground" />
                                            <span class="font-medium">{{ rank.formattedDuration }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <Link :href="`/users/${rank.userId}`">
                                            <Button variant="ghost" size="sm">View Profile</Button>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="ranks.data.length === 0">
                                    <td colspan="5" class="p-8 text-center text-muted-foreground">
                                        No leaderboard data available
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="ranks.last_page > 1" class="mt-4 flex items-center justify-center gap-2">
                        <template v-for="link in ranks.links" :key="link.label">
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
