<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Users, FileText, CheckCircle, Clock } from 'lucide-vue-next';

defineProps<{
    stats: {
        total: number;
        byStatus: Record<string, number>;
        byPosition: Array<{ name: string; applicants_count: number }>;
        recent: Array<any>;
    };
}>();

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const statusLabels: Record<string, string> = {
    new: 'Baru',
    screening: 'Seleksi Berkas',
    interview: 'Wawancara',
    accepted: 'Diterima',
    rejected: 'Ditolak',
};

const statusColors: Record<string, string> = {
    new: 'text-blue-600',
    screening: 'text-yellow-600',
    interview: 'text-purple-600',
    accepted: 'text-green-600',
    rejected: 'text-red-600',
};

const getMaxCount = (positions: any[]) => Math.max(...positions.map(p => p.applicants_count), 1);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            
            <!-- Key Metrics -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Pelamar</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total }}</div>
                        <p class="text-xs text-muted-foreground">kandidat masuk</p>
                    </CardContent>
                </Card>

                <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Baru</CardTitle>
                        <Clock class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.byStatus.new || 0 }}</div>
                            <p class="text-xs text-muted-foreground">menunggu review</p>
                    </CardContent>
                </Card>

                <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Wawancara</CardTitle>
                        <FileText class="h-4 w-4 text-purple-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.byStatus.interview || 0 }}</div>
                            <p class="text-xs text-muted-foreground">proses aktif</p>
                    </CardContent>
                </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Diterima</CardTitle>
                        <CheckCircle class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.byStatus.accepted || 0 }}</div>
                            <p class="text-xs text-muted-foreground">total rekrutan</p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 mt-4">
                
                <!-- Popular Positions -->
                <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>Posisi Populer</CardTitle>
                        <CardDescription>Distribusi pelamar berdasarkan posisi</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="pos in stats.byPosition" :key="pos.name" class="flex items-center">
                                <div class="w-1/3 text-sm font-medium truncate" :title="pos.name">{{ pos.name }}</div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-gray-700 ml-4 relative">
                                    <div class="bg-primary h-2.5 rounded-full" :style="`width: ${(pos.applicants_count / getMaxCount(stats.byPosition)) * 100}%`"></div>
                                </div>
                                <div class="w-12 text-right text-sm font-bold ml-2">{{ pos.applicants_count }}</div>
                            </div>
                            <div v-if="stats.byPosition.length === 0" class="text-sm text-gray-500 text-center py-4">
                                Belum ada data posisi.
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Applicants -->
                <Card class="col-span-3">
                    <CardHeader>
                        <CardTitle>Pelamar Terbaru</CardTitle>
                        <CardDescription>
                            5 kandidat terakhir
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="applicant in stats.recent" :key="applicant.id" class="flex items-center justify-between border-b pb-2 last:border-0 last:pb-0">
                                <div>
                                    <div class="text-sm font-medium">{{ applicant.full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ applicant.email }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-semibold" :class="statusColors[applicant.status]">
                                        {{ statusLabels[applicant.status] || applicant.status }}
                                    </div>
                                        <Link :href="`/admin/applicants/${applicant.id}`" class="text-[10px] text-blue-500 hover:underline">
                                        Lihat
                                    </Link>
                                </div>
                            </div>
                            <div v-if="stats.recent.length === 0" class="text-sm text-gray-500 text-center py-4">
                                Belum ada pelamar.
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
