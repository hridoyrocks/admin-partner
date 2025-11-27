<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Users, Phone, Clock, Star, Calendar, Mail, ArrowLeft, Ban, CheckCircle, AlertTriangle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

interface User {
    id: number;
    uid: string;
    name: string;
    email: string;
    phone: string | null;
    gender: string | null;
    profileUrl: string | null;
    bio: string | null;
    status: string;
    accountStatus: string;
    banReason: string | null;
    totalCalls: number;
    totalDuration: string;
    points: number;
    followers: number;
    rating: number;
    registered: string;
}

interface Rating {
    id: number;
    odcId: string;
    odcName: string;
    rating: number;
    text: string | null;
}

interface Props {
    user: User;
    ratings: Rating[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/users' },
    { title: props.user.name, href: `/users/${props.user.uid}` },
];

// Ban dialog state
const showBanDialog = ref(false);
const banReason = ref('');

const openBanDialog = () => {
    banReason.value = '';
    showBanDialog.value = true;
};

const confirmBan = () => {
    router.patch(`/users/${props.user.uid}/status`, {
        status: 'BANNED',
        banReason: banReason.value
    }, { preserveScroll: true });
    showBanDialog.value = false;
};

const updateStatus = (status: string) => {
    router.patch(`/users/${props.user.uid}/status`, { status }, { preserveScroll: true });
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
    <Head :title="user.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Back Button -->
            <div>
                <Link href="/users">
                    <Button variant="outline" size="sm">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Users
                    </Button>
                </Link>
            </div>

            <!-- User Profile Card -->
            <Card>
                <CardContent class="p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Profile Picture -->
                        <div class="flex-shrink-0">
                            <img
                                v-if="user.profileUrl"
                                :src="user.profileUrl"
                                :alt="user.name"
                                class="h-32 w-32 rounded-full object-cover"
                            />
                            <div v-else class="h-32 w-32 rounded-full bg-muted flex items-center justify-center">
                                <Users class="h-16 w-16 text-muted-foreground" />
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 space-y-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h1 class="text-2xl font-bold">{{ user.name }}</h1>
                                    <p class="text-muted-foreground">{{ user.uid }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span :class="['px-3 py-1 rounded-full text-sm font-medium', getStatusBadge(user.accountStatus)]">
                                        {{ user.accountStatus }}
                                    </span>
                                    <Button
                                        v-if="user.accountStatus === 'ACTIVE'"
                                        variant="destructive"
                                        size="sm"
                                        @click="openBanDialog"
                                    >
                                        <Ban class="h-4 w-4 mr-2" />
                                        Ban User
                                    </Button>
                                    <Button
                                        v-else
                                        variant="default"
                                        size="sm"
                                        @click="updateStatus('ACTIVE')"
                                    >
                                        <CheckCircle class="h-4 w-4 mr-2" />
                                        Activate User
                                    </Button>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="flex items-center gap-2">
                                    <Mail class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ user.email }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Phone class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ user.phone || 'Not provided' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                    <span>Registered: {{ user.registered }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-muted-foreground">Gender:</span>
                                    <span>{{ user.gender || 'Not specified' }}</span>
                                </div>
                            </div>

                            <div v-if="user.bio" class="pt-2">
                                <p class="text-sm text-muted-foreground">{{ user.bio }}</p>
                            </div>

                            <!-- Ban Reason Display -->
                            <div v-if="user.accountStatus === 'BANNED' && user.banReason" class="pt-4">
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <AlertTriangle class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" />
                                        <div>
                                            <p class="font-medium text-red-800">Ban Reason</p>
                                            <p class="text-sm text-red-700 mt-1">{{ user.banReason }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Calls</CardTitle>
                        <Phone class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ user.totalCalls }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Duration</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ user.totalDuration }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Rating</CardTitle>
                        <Star class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold flex items-center gap-1">
                            <span class="text-yellow-500">★</span>
                            {{ user.rating }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Points</CardTitle>
                        <span class="text-muted-foreground">⭐</span>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ user.points }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Ratings -->
            <Card>
                <CardHeader>
                    <CardTitle>Recent Ratings</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div
                            v-for="rating in ratings"
                            :key="rating.id"
                            class="flex items-start gap-4 border-b pb-4 last:border-0"
                        >
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="font-medium">{{ rating.odcName }}</p>
                                    <div class="flex items-center">
                                        <Star class="h-4 w-4 text-yellow-500 fill-yellow-500" />
                                        <span class="ml-1 text-sm">{{ rating.rating }}</span>
                                    </div>
                                </div>
                                <p v-if="rating.text" class="text-sm text-muted-foreground mt-1">
                                    {{ rating.text }}
                                </p>
                            </div>
                        </div>
                        <p v-if="ratings.length === 0" class="text-muted-foreground">
                            No ratings yet
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>

    </AppLayout>

    <!-- Ban Dialog Modal (outside AppLayout for proper z-index) -->
    <Teleport to="body">
        <div v-if="showBanDialog" class="fixed inset-0 z-[100] flex items-center justify-center">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/50" @click="showBanDialog = false"></div>

            <!-- Modal -->
            <div class="relative bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6 mx-4 z-[101]">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-full">
                        <Ban class="h-6 w-6 text-red-600" />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Ban User</h3>
                        <p class="text-sm text-muted-foreground">{{ user.name }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Ban Reason</label>
                    <textarea
                        v-model="banReason"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                        rows="3"
                        placeholder="Enter the reason for banning this user..."
                    ></textarea>
                    <p class="text-xs text-muted-foreground mt-1">This will be shown to the user in the app.</p>
                </div>

                <div class="flex justify-end gap-3">
                    <Button variant="outline" @click="showBanDialog = false">
                        Cancel
                    </Button>
                    <Button variant="destructive" @click="confirmBan">
                        <Ban class="h-4 w-4 mr-2" />
                        Confirm Ban
                    </Button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
