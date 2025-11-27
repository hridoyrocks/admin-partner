<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Image, Plus, Trash2, Edit, X } from 'lucide-vue-next';
import { ref } from 'vue';

interface Banner {
    id: number;
    title: string;
    imageUrl: string;
    active: boolean;
}

interface Props {
    banners: Banner[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Banners', href: '/banners' },
];

const showAddModal = ref(false);
const editingBanner = ref<Banner | null>(null);

const form = useForm({
    title: '',
    imageUrl: '',
    active: true,
});

const openAddModal = () => {
    form.reset();
    editingBanner.value = null;
    showAddModal.value = true;
};

const openEditModal = (banner: Banner) => {
    editingBanner.value = banner;
    form.title = banner.title;
    form.imageUrl = banner.imageUrl;
    form.active = banner.active;
    showAddModal.value = true;
};

const closeModal = () => {
    showAddModal.value = false;
    editingBanner.value = null;
    form.reset();
};

const submitForm = () => {
    if (editingBanner.value) {
        form.patch(`/banners/${editingBanner.value.id}`, {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post('/banners', {
            onSuccess: () => closeModal(),
        });
    }
};

const toggleStatus = (banner: Banner) => {
    router.patch(`/banners/${banner.id}/toggle`, {}, { preserveScroll: true });
};

const deleteBanner = (banner: Banner) => {
    if (confirm('Are you sure you want to delete this banner?')) {
        router.delete(`/banners/${banner.id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Banners" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Image class="h-5 w-5" />
                            <CardTitle>Banner Management</CardTitle>
                        </div>
                        <Button @click="openAddModal">
                            <Plus class="h-4 w-4 mr-2" />
                            Add Banner
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="banner in banners"
                            :key="banner.id"
                            class="rounded-lg border overflow-hidden"
                        >
                            <div class="relative aspect-video bg-muted">
                                <img
                                    :src="banner.imageUrl"
                                    :alt="banner.title"
                                    class="w-full h-full object-cover"
                                />
                                <div
                                    v-if="!banner.active"
                                    class="absolute inset-0 bg-black/50 flex items-center justify-center"
                                >
                                    <span class="text-white font-medium">Inactive</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-medium">{{ banner.title }}</h3>
                                    <Switch
                                        :checked="banner.active"
                                        @update:checked="toggleStatus(banner)"
                                    />
                                </div>
                                <div class="mt-3 flex items-center gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        @click="openEditModal(banner)"
                                    >
                                        <Edit class="h-4 w-4 mr-1" />
                                        Edit
                                    </Button>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="deleteBanner(banner)"
                                    >
                                        <Trash2 class="h-4 w-4 mr-1" />
                                        Delete
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="banners.length === 0"
                            class="col-span-full p-8 text-center text-muted-foreground"
                        >
                            No banners found. Click "Add Banner" to create one.
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Add/Edit Modal -->
        <div
            v-if="showAddModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        >
            <div class="w-full max-w-md rounded-lg bg-background p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">
                        {{ editingBanner ? 'Edit Banner' : 'Add New Banner' }}
                    </h2>
                    <Button variant="ghost" size="icon" @click="closeModal">
                        <X class="h-4 w-4" />
                    </Button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="title">Title</Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            placeholder="Banner title"
                            required
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="imageUrl">Image URL</Label>
                        <Input
                            id="imageUrl"
                            v-model="form.imageUrl"
                            placeholder="https://example.com/image.jpg"
                            required
                        />
                    </div>

                    <div v-if="form.imageUrl" class="rounded-lg border overflow-hidden">
                        <img
                            :src="form.imageUrl"
                            alt="Preview"
                            class="w-full h-32 object-cover"
                        />
                    </div>

                    <div class="flex items-center justify-between">
                        <Label for="active">Active</Label>
                        <Switch
                            id="active"
                            v-model:checked="form.active"
                        />
                    </div>

                    <div class="flex gap-2 pt-4">
                        <Button
                            type="button"
                            variant="outline"
                            class="flex-1"
                            @click="closeModal"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            class="flex-1"
                            :disabled="form.processing"
                        >
                            {{ editingBanner ? 'Update' : 'Create' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
