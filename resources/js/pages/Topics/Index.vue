<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { MessageCircle, Plus, Trash2, Edit, X } from 'lucide-vue-next';
import { ref } from 'vue';

interface Topic {
    id: number;
    title: string;
    description: string;
}

interface Props {
    topics: Topic[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Call Topics', href: '/topics' },
];

const showAddModal = ref(false);
const editingTopic = ref<Topic | null>(null);

const form = useForm({
    title: '',
    description: '',
});

const openAddModal = () => {
    form.reset();
    editingTopic.value = null;
    showAddModal.value = true;
};

const openEditModal = (topic: Topic) => {
    editingTopic.value = topic;
    form.title = topic.title;
    form.description = topic.description;
    showAddModal.value = true;
};

const closeModal = () => {
    showAddModal.value = false;
    editingTopic.value = null;
    form.reset();
};

const submitForm = () => {
    if (editingTopic.value) {
        form.patch(`/topics/${editingTopic.value.id}`, {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post('/topics', {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteTopic = (topic: Topic) => {
    if (confirm('Are you sure you want to delete this topic?')) {
        router.delete(`/topics/${topic.id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Call Topics" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <MessageCircle class="h-5 w-5" />
                            <CardTitle>Call Topics Management</CardTitle>
                        </div>
                        <Button @click="openAddModal">
                            <Plus class="h-4 w-4 mr-2" />
                            Add Topic
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="topic in topics"
                            :key="topic.id"
                            class="rounded-lg border p-4"
                        >
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-semibold text-lg">{{ topic.title }}</h3>
                                <span class="text-xs text-muted-foreground bg-muted px-2 py-1 rounded">
                                    #{{ topic.id }}
                                </span>
                            </div>
                            <p class="text-sm text-muted-foreground mb-4 line-clamp-3">
                                {{ topic.description }}
                            </p>
                            <div class="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="openEditModal(topic)"
                                >
                                    <Edit class="h-4 w-4 mr-1" />
                                    Edit
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    @click="deleteTopic(topic)"
                                >
                                    <Trash2 class="h-4 w-4 mr-1" />
                                    Delete
                                </Button>
                            </div>
                        </div>

                        <div
                            v-if="topics.length === 0"
                            class="col-span-full p-8 text-center text-muted-foreground"
                        >
                            No topics found. Click "Add Topic" to create one.
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Add/Edit Modal -->
        <Teleport to="body">
            <div
                v-if="showAddModal"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50"
            >
                <div class="w-full max-w-md rounded-lg bg-background p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">
                            {{ editingTopic ? 'Edit Topic' : 'Add New Topic' }}
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
                                placeholder="Topic title"
                                required
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Topic description for users to discuss during calls..."
                                rows="4"
                                required
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
                                {{ editingTopic ? 'Update' : 'Create' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
