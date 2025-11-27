<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Settings, Globe, Bell, Shield, Link as LinkIcon } from 'lucide-vue-next';

interface AppConfig {
    id: number;
    appStatus: string;
    appStatusMessage: string | null;
    enablePremiumSwitch: boolean;
    facebook: string | null;
    latestVersion: number;
    forceUpdate: boolean;
    minVersion: number;
    notice: string | null;
    noticeAction: string | null;
    privacy: string | null;
    reportReasons: string | null;
    support: string | null;
    teacherNotice: string | null;
    terms: string | null;
    updateInfo: string | null;
    warnings: string | null;
    x: string | null;
    youtube: string | null;
}

interface Props {
    config: AppConfig | null;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'App Config', href: '/config' },
];

const form = useForm({
    appStatus: props.config?.appStatus || 'ACTIVE',
    appStatusMessage: props.config?.appStatusMessage || '',
    enablePremiumSwitch: props.config?.enablePremiumSwitch || false,
    latestVersion: props.config?.latestVersion || 1,
    forceUpdate: props.config?.forceUpdate || false,
    minVersion: props.config?.minVersion || 1,
    notice: props.config?.notice || '',
    noticeAction: props.config?.noticeAction || '',
    teacherNotice: props.config?.teacherNotice || '',
    updateInfo: props.config?.updateInfo || '',
    warnings: props.config?.warnings || '',
    reportReasons: props.config?.reportReasons || '',
    facebook: props.config?.facebook || '',
    youtube: props.config?.youtube || '',
    x: props.config?.x || '',
    support: props.config?.support || '',
    privacy: props.config?.privacy || '',
    terms: props.config?.terms || '',
});

const submitForm = () => {
    form.patch('/config');
};
</script>

<template>
    <Head title="App Configuration" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- App Status -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <Shield class="h-5 w-5" />
                            <CardTitle>App Status</CardTitle>
                        </div>
                        <CardDescription>Control the app's availability status</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="appStatus">Status</Label>
                                <select
                                    id="appStatus"
                                    v-model="form.appStatus"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="ACTIVE">Active</option>
                                    <option value="MAINTENANCE">Maintenance</option>
                                    <option value="DISABLED">Disabled</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <Label for="latestVersion">Latest Version Code</Label>
                                <Input
                                    id="latestVersion"
                                    v-model.number="form.latestVersion"
                                    type="number"
                                    placeholder="e.g., 520"
                                />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label for="appStatusMessage">Status Message</Label>
                            <Textarea
                                id="appStatusMessage"
                                v-model="form.appStatusMessage"
                                placeholder="Message to show when app is not active"
                                rows="2"
                            />
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <Label>Enable Premium Switch</Label>
                                <p class="text-sm text-muted-foreground">Allow users to access premium features</p>
                            </div>
                            <Switch v-model:checked="form.enablePremiumSwitch" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Force Update -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <Settings class="h-5 w-5 text-orange-500" />
                            <CardTitle>Force Update</CardTitle>
                        </div>
                        <CardDescription>Force users to update to a minimum version</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-lg border bg-orange-50 dark:bg-orange-950/20">
                            <div>
                                <Label class="text-base">Enable Force Update</Label>
                                <p class="text-sm text-muted-foreground">When enabled, users below minimum version must update</p>
                            </div>
                            <Switch v-model:checked="form.forceUpdate" />
                        </div>
                        <div class="space-y-2">
                            <Label for="minVersion">Minimum Required Version Code</Label>
                            <Input
                                id="minVersion"
                                v-model.number="form.minVersion"
                                type="number"
                                placeholder="e.g., 500"
                                :disabled="!form.forceUpdate"
                            />
                            <p class="text-xs text-muted-foreground">
                                Users with version code below this will be forced to update.
                                Current latest: {{ form.latestVersion }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Notices -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <Bell class="h-5 w-5" />
                            <CardTitle>Notices & Announcements</CardTitle>
                        </div>
                        <CardDescription>Configure in-app notices and updates</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="notice">General Notice</Label>
                            <Textarea
                                id="notice"
                                v-model="form.notice"
                                placeholder="General notice for all users"
                                rows="2"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="noticeAction">Notice Action URL</Label>
                            <Input
                                id="noticeAction"
                                v-model="form.noticeAction"
                                placeholder="https://example.com/action"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="teacherNotice">Teacher Notice</Label>
                            <Textarea
                                id="teacherNotice"
                                v-model="form.teacherNotice"
                                placeholder="Notice specifically for teachers"
                                rows="2"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="updateInfo">Update Information</Label>
                            <Textarea
                                id="updateInfo"
                                v-model="form.updateInfo"
                                placeholder="Information about the latest update"
                                rows="2"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="warnings">Warnings</Label>
                            <Textarea
                                id="warnings"
                                v-model="form.warnings"
                                placeholder="Warning messages for users"
                                rows="2"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="reportReasons">Report Reasons (comma separated)</Label>
                            <Textarea
                                id="reportReasons"
                                v-model="form.reportReasons"
                                placeholder="Spam, Harassment, Inappropriate content"
                                rows="2"
                            />
                        </div>
                    </CardContent>
                </Card>

                <!-- Social & Links -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <Globe class="h-5 w-5" />
                            <CardTitle>Social Media & Links</CardTitle>
                        </div>
                        <CardDescription>External links and social media</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="facebook">Facebook</Label>
                                <Input
                                    id="facebook"
                                    v-model="form.facebook"
                                    placeholder="https://facebook.com/..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="youtube">YouTube</Label>
                                <Input
                                    id="youtube"
                                    v-model="form.youtube"
                                    placeholder="https://youtube.com/..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="x">X (Twitter)</Label>
                                <Input
                                    id="x"
                                    v-model="form.x"
                                    placeholder="https://x.com/..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="support">Support Email/URL</Label>
                                <Input
                                    id="support"
                                    v-model="form.support"
                                    placeholder="support@example.com"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Legal -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <LinkIcon class="h-5 w-5" />
                            <CardTitle>Legal Links</CardTitle>
                        </div>
                        <CardDescription>Privacy policy and terms of service</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="privacy">Privacy Policy URL</Label>
                                <Input
                                    id="privacy"
                                    v-model="form.privacy"
                                    placeholder="https://example.com/privacy"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="terms">Terms of Service URL</Label>
                                <Input
                                    id="terms"
                                    v-model="form.terms"
                                    placeholder="https://example.com/terms"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <Button
                        type="submit"
                        size="lg"
                        :disabled="form.processing"
                    >
                        <Settings class="h-4 w-4 mr-2" />
                        Save Configuration
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
